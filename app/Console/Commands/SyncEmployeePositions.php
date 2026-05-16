<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncEmployeePositions extends Command
{
    protected $signature = 'employees:sync-positions';

    protected $description = 'Sinkronisasi jabatan text lama employees.position ke master positions dan employees.position_id';

    public function handle(): int
    {
        $this->info('Mulai sinkronisasi jabatan pegawai...');

        $employees = Employee::query()
            ->whereNull('position_id')
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->get();

        if ($employees->isEmpty()) {
            $this->info('Tidak ada data pegawai yang perlu disinkronkan.');
            return self::SUCCESS;
        }

        $createdPositions = 0;
        $updatedEmployees = 0;

        foreach ($employees as $employee) {
            $positionName = trim($employee->position);

            if ($positionName === '') {
                continue;
            }

            $position = Position::query()
                ->whereRaw('LOWER(name) = ?', [Str::lower($positionName)])
                ->first();

            if (! $position) {
                $position = Position::create([
                    'name' => $positionName,
                    'code' => null,
                    'description' => 'Data hasil migrasi dari kolom lama employees.position.',
                    'is_active' => true,
                ]);

                $createdPositions++;
            }

            $employee->update([
                'position_id' => $position->id,
            ]);

            $updatedEmployees++;
        }

        $this->info('Sinkronisasi selesai.');
        $this->line("Jabatan baru dibuat: {$createdPositions}");
        $this->line("Pegawai diperbarui: {$updatedEmployees}");

        return self::SUCCESS;
    }
}