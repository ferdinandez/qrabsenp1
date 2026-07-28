<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'total_days',
        'used_days',
        'remaining_days',
    ];

    protected $casts = [
        'year' => 'integer',
        'total_days' => 'integer',
        'used_days' => 'integer',
        'remaining_days' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    // Helper method to deduct days
    public function deduct($days)
    {
        $this->used_days += $days;
        $this->remaining_days = $this->total_days - $this->used_days;
        $this->save();
    }

    // Helper method to refund days
    public function refund($days)
    {
        $this->used_days = max(0, $this->used_days - $days);
        $this->remaining_days = $this->total_days - $this->used_days;
        $this->save();
    }
}
