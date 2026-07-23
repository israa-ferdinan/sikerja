<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperationalTicket extends Model
{
    use HasFactory;

    public const SOURCE_INTERNAL = 'internal';
    public const SOURCE_PUBLIC = 'public';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';

    public const STATUS_BARU = 'baru';
    public const STATUS_DIPROSES = 'diproses';
    public const STATUS_MENUNGGU_PEMOHON = 'menunggu_pemohon';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const CATEGORY_ZOOM = 'dukungan_rapat_zoom';

    protected $fillable = [
        'ticket_code',
        'public_token',
        'source',
        'requester_name',
        'requester_contact',
        'requester_unit',
        'category',
        'title',
        'description',
        'priority',
        'status',
        'unit_id',
        'assigned_to_employee_id',
        'created_by_user_id',
        'closed_by_user_id',
        'closed_at',
        'last_public_viewed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'last_public_viewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (OperationalTicket $ticket) {
            if (blank($ticket->ticket_code)) {
                $ticket->ticket_code = self::generateTicketCode();
            }

            if (blank($ticket->public_token)) {
                $ticket->public_token = Str::random(40);
            }

            if (blank($ticket->source)) {
                $ticket->source = self::SOURCE_INTERNAL;
            }

            if (blank($ticket->priority)) {
                $ticket->priority = self::PRIORITY_NORMAL;
            }

            if (blank($ticket->status)) {
                $ticket->status = self::STATUS_BARU;
            }
        });
    }

    public static function generateTicketCode(): string
    {
        do {
            $code = 'OPS-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('ticket_code', $code)->exists());

        return $code;
    }

    public static function sourceOptions(): array
    {
        return [
            self::SOURCE_INTERNAL => 'Internal',
            self::SOURCE_PUBLIC => 'Public',
        ];
    }

    public static function priorityOptions(): array
    {
        return [
            self::PRIORITY_LOW => 'Rendah',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_HIGH => 'Tinggi',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_BARU => 'Baru',
            self::STATUS_DIPROSES => 'Diproses',
            self::STATUS_MENUNGGU_PEMOHON => 'Menunggu Pemohon',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
        ];
    }

    public static function categoryOptions(): array
    {
        return [
            'gangguan_aplikasi' => 'Gangguan Aplikasi',
            'gangguan_jaringan' => 'Gangguan Jaringan',
            'gangguan_perangkat' => 'Gangguan Perangkat',
            'permintaan_akses' => 'Permintaan Akses',
            'permintaan_data' => 'Permintaan Data',
            'penggunaan_ruangan_lab' => 'Penggunaan Ruangan/Lab',
            'dukungan_rapat_zoom' => 'Permintaan Zoom',
            'lainnya' => 'Lainnya',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->status] ?? ucfirst((string) $this->status);
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::priorityOptions()[$this->priority] ?? ucfirst((string) $this->priority);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryOptions()[$this->category] ?? ucfirst(str_replace('_', ' ', (string) $this->category));
    }

    public function getSourceLabelAttribute(): string
    {
        return self::sourceOptions()[$this->source] ?? ucfirst((string) $this->source);
    }

    public function isClosed(): bool
    {
        return in_array($this->status, [
            self::STATUS_SELESAI,
            self::STATUS_DIBATALKAN,
        ], true);
    }

    public function isEditable(): bool
    {
        return ! $this->isClosed();
    }

    public function isDeletable(): bool
    {
        if (! in_array($this->status, [
            self::STATUS_BARU,
            self::STATUS_DIBATALKAN,
        ], true)) {
            return false;
        }

        return ! $this->dailyReports()->exists();
    }
    
    public function hasDailyReports(): bool
    {
        if ($this->relationLoaded('dailyReports')) {
            return $this->dailyReports->isNotEmpty();
        }

        return $this->dailyReports()->exists();
    }

    public function hasAutomaticDelegations(): bool
    {
        if ($this->relationLoaded('automaticDelegations')) {
            return $this->automaticDelegations->isNotEmpty();
        }

        return $this->automaticDelegations()->exists();
    }

    public function canUpdateStatus(): bool
    {
        return ! $this->isClosed();
    }

    public function canUploadDocument(): bool
    {
        return ! $this->isClosed();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function assignedToEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to_employee_id');
    }

    public function dailyReports()
    {
        return $this->hasMany(
            DailyReport::class,
            'operational_ticket_id'
        );
    }

    public function automaticDelegations()
    {
        return $this->hasMany(
            DutyDelegation::class,
            'operational_ticket_id'
        );
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function notes()
    {
        return $this->hasMany(OperationalTicketNote::class, 'operational_ticket_id');
    }

    public function hasAutomaticDelegation(): bool
    {
        return $this->automaticDelegations()->exists();
    }
    
    public function hasDailyReportForEmployeeAndDate(
        int $employeeId,
        string $reportDate
    ): bool {
        return $this->dailyReports()
            ->where('employee_id', $employeeId)
            ->whereDate('report_date', $reportDate)
            ->exists();
    }
}