<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\User;
use Carbon\Carbon;

class LeaveController extends Controller
{
    // Get all leave types
    public function getLeaveTypes()
    {
        $types = LeaveType::where('is_active', true)->get();
        return response()->json([
            'message' => 'Leave types',
            'data' => $types
        ]);
    }

    // Get user's leave balance
    public function getBalance(Request $req)
    {
        $user = $req->user();
        $year = $req->input('year', now()->year);

        $balances = LeaveBalance::where('user_id', $user->id)
            ->where('year', $year)
            ->with('leaveType')
            ->get();

        // If no balance exists, create initial balance for all leave types
        if ($balances->isEmpty()) {
            $leaveTypes = LeaveType::where('is_active', true)->get();
            foreach ($leaveTypes as $type) {
                LeaveBalance::create([
                    'user_id' => $user->id,
                    'leave_type_id' => $type->id,
                    'year' => $year,
                    'total_days' => $type->max_days_per_year,
                    'used_days' => 0,
                    'remaining_days' => $type->max_days_per_year,
                ]);
            }
            $balances = LeaveBalance::where('user_id', $user->id)
                ->where('year', $year)
                ->with('leaveType')
                ->get();
        }

        return response()->json([
            'message' => 'Leave balance',
            'year' => $year,
            'data' => $balances
        ]);
    }

    // Submit leave request
    public function submitRequest(Request $req)
    {
        $user = $req->user();

        $req->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'proof_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $startDate = Carbon::parse($req->start_date);
        $endDate = Carbon::parse($req->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Check balance
        $balance = LeaveBalance::where('user_id', $user->id)
            ->where('leave_type_id', $req->leave_type_id)
            ->where('year', $startDate->year)
            ->first();

        if (!$balance) {
            // Create balance if not exists
            $leaveType = LeaveType::find($req->leave_type_id);
            $balance = LeaveBalance::create([
                'user_id' => $user->id,
                'leave_type_id' => $req->leave_type_id,
                'year' => $startDate->year,
                'total_days' => $leaveType->max_days_per_year,
                'used_days' => 0,
                'remaining_days' => $leaveType->max_days_per_year,
            ]);
        }

        if ($balance->remaining_days < $totalDays) {
            return response()->json([
                'message' => 'Saldo cuti tidak mencukupi',
                'required' => $totalDays,
                'available' => $balance->remaining_days
            ], 400);
        }

        // Check for overlapping leave requests
        $overlap = LeaveRequest::where('user_id', $user->id)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'message' => 'Anda sudah memiliki pengajuan cuti pada tanggal tersebut'
            ], 400);
        }

        // Handle file upload
        $proofPath = null;
        if ($req->hasFile('proof_document')) {
            $proofPath = $req->file('proof_document')->store('leave-documents', 'public');
        }

        // Create leave request
        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $req->leave_type_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'reason' => $req->reason,
            'proof_document' => $proofPath,
            'status' => 'pending',
        ]);

        // Notify all admins about new leave request
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            // In-app notification
            \App\Http\Controllers\NotificationController::create(
                $admin->id,
                'leave_request',
                'Pengajuan Cuti Baru',
                "{$user->name} mengajukan cuti {$leaveRequest->leaveType->name} dari {$startDate->format('d M Y')} - {$endDate->format('d M Y')}",
                ['leave_request_id' => $leaveRequest->id]
            );
            
            // Email notification
            try {
                \Illuminate\Support\Facades\Mail::to($admin->email)
                    ->send(new \App\Mail\LeaveRequestMail($leaveRequest->load('leaveType'), $user));
            } catch (\Exception $e) {
                \Log::error('Failed to send email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dikirim',
            'data' => $leaveRequest->load('leaveType')
        ], 201);
    }

    // Get user's leave history
    public function getHistory(Request $req)
    {
        $user = $req->user();

        $query = LeaveRequest::where('user_id', $user->id)
            ->with(['leaveType', 'approver'])
            ->orderBy('created_at', 'desc');

        if ($req->filled('status')) {
            $query->where('status', $req->status);
        }

        if ($req->filled('year')) {
            $query->whereYear('start_date', $req->year);
        }

        $history = $query->get();

        return response()->json([
            'message' => 'Leave history',
            'total' => $history->count(),
            'data' => $history
        ]);
    }

    // Cancel pending leave request
    public function cancelRequest(Request $req, $id)
    {
        $user = $req->user();

        $leaveRequest = LeaveRequest::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$leaveRequest) {
            return response()->json([
                'message' => 'Pengajuan tidak ditemukan atau tidak dapat dibatalkan'
            ], 404);
        }

        $leaveRequest->delete();

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dibatalkan'
        ]);
    }

    // ADMIN: Get all pending leave requests
    public function getPendingRequests(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $pending = LeaveRequest::pending()
            ->with(['user', 'leaveType'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'message' => 'Pending leave requests',
            'total' => $pending->count(),
            'data' => $pending
        ]);
    }

    // ADMIN: Get all leave requests (with filters)
    public function getAllRequests(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $query = LeaveRequest::with(['user', 'leaveType', 'approver'])
            ->orderBy('created_at', 'desc');

        if ($req->filled('status')) {
            $query->where('status', $req->status);
        }

        if ($req->filled('user_id')) {
            $query->where('user_id', $req->user_id);
        }

        if ($req->filled('start_date')) {
            $query->where('start_date', '>=', $req->start_date);
        }

        if ($req->filled('end_date')) {
            $query->where('end_date', '<=', $req->end_date);
        }

        $requests = $query->get();

        return response()->json([
            'message' => 'All leave requests',
            'total' => $requests->count(),
            'data' => $requests
        ]);
    }

    // ADMIN: Approve leave request
    public function approveRequest(Request $req, $id)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $leaveRequest = LeaveRequest::find($id);

        if (!$leaveRequest) {
            return response()->json(['message' => 'Pengajuan tidak ditemukan'], 404);
        }

        if ($leaveRequest->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan sudah diproses'], 400);
        }

        // Deduct balance
        $balance = LeaveBalance::where('user_id', $leaveRequest->user_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', Carbon::parse($leaveRequest->start_date)->year)
            ->first();

        if ($balance) {
            $balance->deduct($leaveRequest->total_days);
        }

        // Update leave request
        $leaveRequest->status = 'approved';
        $leaveRequest->approved_by = $req->user()->id;
        $leaveRequest->approved_at = now();
        $leaveRequest->save();

        // Notify user about approval
        \App\Http\Controllers\NotificationController::create(
            $leaveRequest->user_id,
            'approval',
            'Cuti Disetujui',
            "Pengajuan cuti Anda untuk {$leaveRequest->leaveType->name} dari " . 
            Carbon::parse($leaveRequest->start_date)->format('d M Y') . " - " . 
            Carbon::parse($leaveRequest->end_date)->format('d M Y') . " telah disetujui oleh admin.",
            ['leave_request_id' => $leaveRequest->id]
        );
        
        // Email notification
        try {
            \Illuminate\Support\Facades\Mail::to($leaveRequest->user->email)
                ->send(new \App\Mail\LeaveApprovedMail($leaveRequest->load('leaveType'), $leaveRequest->user));
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Pengajuan cuti berhasil disetujui',
            'data' => $leaveRequest->load(['user', 'leaveType'])
        ]);
    }

    // ADMIN: Reject leave request
    public function rejectRequest(Request $req, $id)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $req->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leaveRequest = LeaveRequest::find($id);

        if (!$leaveRequest) {
            return response()->json(['message' => 'Pengajuan tidak ditemukan'], 404);
        }

        if ($leaveRequest->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan sudah diproses'], 400);
        }

        $leaveRequest->status = 'rejected';
        $leaveRequest->approved_by = $req->user()->id;
        $leaveRequest->approved_at = now();
        $leaveRequest->rejection_reason = $req->rejection_reason;
        $leaveRequest->save();

        // Notify user about rejection
        \App\Http\Controllers\NotificationController::create(
            $leaveRequest->user_id,
            'rejection',
            'Cuti Ditolak',
            "Pengajuan cuti Anda untuk {$leaveRequest->leaveType->name} dari " . 
            Carbon::parse($leaveRequest->start_date)->format('d M Y') . " - " . 
            Carbon::parse($leaveRequest->end_date)->format('d M Y') . " ditolak. Alasan: {$req->rejection_reason}",
            ['leave_request_id' => $leaveRequest->id]
        );
        
        // Email notification
        try {
            \Illuminate\Support\Facades\Mail::to($leaveRequest->user->email)
                ->send(new \App\Mail\LeaveRejectedMail($leaveRequest->load('leaveType'), $leaveRequest->user, $req->rejection_reason));
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Pengajuan cuti ditolak',
            'data' => $leaveRequest->load(['user', 'leaveType'])
        ]);
    }

    // ADMIN: Get leave statistics
    public function getStatistics(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $year = $req->input('year', now()->year);

        $stats = [
            'total_requests' => LeaveRequest::whereYear('start_date', $year)->count(),
            'pending' => LeaveRequest::pending()->whereYear('start_date', $year)->count(),
            'approved' => LeaveRequest::approved()->whereYear('start_date', $year)->count(),
            'rejected' => LeaveRequest::rejected()->whereYear('start_date', $year)->count(),
            'total_days_taken' => LeaveRequest::approved()
                ->whereYear('start_date', $year)
                ->sum('total_days'),
        ];

        // Who's on leave today
        $today = now()->toDateString();
        $onLeaveToday = LeaveRequest::approved()
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->with(['user', 'leaveType'])
            ->get();

        return response()->json([
            'message' => 'Leave statistics',
            'year' => $year,
            'statistics' => $stats,
            'on_leave_today' => $onLeaveToday
        ]);
    }
}
