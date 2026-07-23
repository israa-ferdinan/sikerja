<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Employee;
use App\Models\Unit;
use App\Models\JobDuty;
use App\Models\Server;
use App\Models\Application;
use App\Models\DailyReportPhoto;
use App\Models\DutyDelegation;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'unit_id',
        'duty_id',
        'server_id',
        'application_id',
        'report_date',
        'title',
        'description',
        'notes',
        'status',
        'is_delegated',
        'delegation_id',
        'duty_owner_employee_id',
        'reported_by_employee_id',
        'operational_ticket_id',
    ];

    protected $casts = [
        'report_date' => 'date',
        'is_delegated' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function duty()
    {
        return $this->belongsTo(JobDuty::class, 'duty_id');
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function photos()
    {
        return $this->hasMany(DailyReportPhoto::class);
    }

    public function delegation()
    {
        return $this->belongsTo(DutyDelegation::class, 'delegation_id');
    }

    public function dutyOwnerEmployee()
    {
        return $this->belongsTo(Employee::class, 'duty_owner_employee_id');
    }

    public function reportedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'reported_by_employee_id');
    }

    public function operationalTicket()
    {
        return $this->belongsTo(
            OperationalTicket::class,
            'operational_ticket_id'
        );
    }
}