<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'max_days_per_year',
        'requires_proof',
        'color',
        'description',
        'is_active',
    ];

    protected $casts = [
        'requires_proof' => 'boolean',
        'is_active' => 'boolean',
        'max_days_per_year' => 'integer',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }
}
