<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\JobDuty;
use App\Models\OperationalTicket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OperationalTicketDutyResolver
{
    /**
     * Cari tupoksi yang paling relevan untuk tiket dan PIC.
     *
     * Prioritas:
     * 1. Tupoksi pribadi PIC.
     * 2. Tupoksi pegawai aktif lain dalam unit yang sama.
     * 3. Tidak ditemukan.
     */
    public function resolve(
        OperationalTicket $ticket,
        Employee $pic
    ): array {
        $keywords = $this->buildKeywords($ticket);

        if (empty($keywords)) {
            return $this->notFoundResult(
                reason: 'Tidak ada keyword yang dapat digunakan untuk menentukan tupoksi.'
            );
        }

        $personalResult = $this->findBestPersonalDuty(
            pic: $pic,
            keywords: $keywords,
            ticket: $ticket
        );

        if ($personalResult !== null) {
            return [
                'found' => true,
                'type' => 'personal',
                'duty' => $personalResult['duty'],
                'owner_employee' => $pic,
                'score' => $personalResult['score'],
                'matched_keywords' => $personalResult['matched_keywords'],
                'reason' => 'Tupoksi yang sesuai ditemukan pada PIC.',
            ];
        }

        $ownerResult = $this->findBestDutyOwnerInUnit(
            pic: $pic,
            keywords: $keywords,
            ticket: $ticket
        );

        if ($ownerResult !== null) {
            return [
                'found' => true,
                'type' => 'delegation_required',
                'duty' => $ownerResult['duty'],
                'owner_employee' => $ownerResult['owner_employee'],
                'score' => $ownerResult['score'],
                'matched_keywords' => $ownerResult['matched_keywords'],
                'reason' => 'PIC tidak mempunyai tupoksi yang sesuai. Ditemukan tupoksi milik pegawai lain dalam unit.',
            ];
        }

        return $this->notFoundResult(
            reason: 'Tidak ditemukan tupoksi yang sesuai pada PIC maupun pegawai lain dalam unit.'
        );
    }

    /**
     * Kumpulkan keyword kategori serta kata penting dari tiket.
     */
    private function buildKeywords(OperationalTicket $ticket): array
    {
        $categoryKeywords = config(
            'operational_ticket_duties.categories.' . $ticket->category,
            []
        );

        $ticketWords = $this->extractTicketKeywords(
            collect([
                $ticket->title,
                $ticket->description,
            ])
                ->filter()
                ->implode(' ')
        );

        return collect($categoryKeywords)
            ->merge($ticketWords)
            ->map(fn ($keyword) => $this->normalize((string) $keyword))
            ->filter(fn ($keyword) => mb_strlen($keyword) >= 3)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Cari tupoksi pribadi PIC.
     */
    private function findBestPersonalDuty(
        Employee $pic,
        array $keywords,
        OperationalTicket $ticket
    ): ?array {
        $dutyIds = DB::table('employee_duty')
            ->where('employee_id', $pic->id)
            ->pluck('duty_id');

        if ($dutyIds->isEmpty()) {
            return null;
        }

        $duties = JobDuty::query()
            ->with('classification')
            ->whereIn('id', $dutyIds)
            ->where(function ($query) use ($pic) {
                $query
                    ->whereNull('unit_id')
                    ->orWhere('unit_id', $pic->unit_id);
            })
            ->orderBy('id')
            ->get();

        return $this->findBestDuty(
            duties: $duties,
            keywords: $keywords,
            ticket: $ticket
        );
    }

    /**
     * Cari pegawai pemilik tupoksi yang sesuai dalam unit PIC.
     */
    private function findBestDutyOwnerInUnit(
        Employee $pic,
        array $keywords,
        OperationalTicket $ticket
    ): ?array {
        if (! $pic->unit_id) {
            return null;
        }

        $employees = Employee::query()
            ->where('unit_id', $pic->unit_id)
            ->where('is_active', true)
            ->whereKeyNot($pic->id)
            ->orderBy('id')
            ->get([
                'id',
                'unit_id',
                'name',
                'is_active',
            ]);

        $bestResult = null;

        foreach ($employees as $owner) {
            $dutyIds = DB::table('employee_duty')
                ->where('employee_id', $owner->id)
                ->pluck('duty_id');

            if ($dutyIds->isEmpty()) {
                continue;
            }

            $duties = JobDuty::query()
                ->with('classification')
                ->whereIn('id', $dutyIds)
                ->where(function ($query) use ($pic) {
                    $query
                        ->whereNull('unit_id')
                        ->orWhere('unit_id', $pic->unit_id);
                })
                ->orderBy('id')
                ->get();

            $result = $this->findBestDuty(
                duties: $duties,
                keywords: $keywords,
                ticket: $ticket
            );

            if ($result === null) {
                continue;
            }

            if (
                $bestResult === null
                || $result['score'] > $bestResult['score']
            ) {
                $bestResult = [
                    ...$result,
                    'owner_employee' => $owner,
                ];
            }
        }

        return $bestResult;
    }

    /**
     * Pilih tupoksi dengan skor tertinggi.
     */
    private function findBestDuty(
        Collection $duties,
        array $keywords,
        OperationalTicket $ticket
    ): ?array {
        $minimumScore = (int) config(
            'operational_ticket_duties.minimum_score',
            10
        );

        $bestResult = null;

        foreach ($duties as $duty) {
            $result = $this->scoreDuty(
                duty: $duty,
                keywords: $keywords,
                ticket: $ticket
            );

            if ($result['score'] < $minimumScore) {
                continue;
            }

            if (
                $bestResult === null
                || $result['score'] > $bestResult['score']
                || (
                    $result['score'] === $bestResult['score']
                    && $duty->id < $bestResult['duty']->id
                )
            ) {
                $bestResult = [
                    'duty' => $duty,
                    'score' => $result['score'],
                    'matched_keywords' => $result['matched_keywords'],
                ];
            }
        }

        return $bestResult;
    }

    /**
     * Hitung skor kecocokan satu tupoksi.
     */
    private function scoreDuty(
        JobDuty $duty,
        array $keywords,
        OperationalTicket $ticket
    ): array {
        $dutyName = $this->normalize((string) $duty->name);

        $classificationName = $this->normalize(
            (string) ($duty->classification?->name ?? '')
        );

        $dutyHaystack = trim(
            $dutyName . ' ' . $classificationName
        );

        $ticketHaystack = $this->normalize(
            collect([
                $ticket->title,
                $ticket->description,
            ])
                ->filter()
                ->implode(' ')
        );

        $score = 0;
        $matchedKeywords = [];

        foreach ($keywords as $keyword) {
            $keywordScore = 0;

            /*
             * Nama tupoksi merupakan sinyal terkuat.
             */
            if (Str::contains($dutyName, $keyword)) {
                $keywordScore += 15;
            }

            /*
             * Klasifikasi tupoksi menjadi sinyal kedua.
             */
            if (
                $classificationName !== ''
                && Str::contains($classificationName, $keyword)
            ) {
                $keywordScore += 10;
            }

            /*
             * Kecocokan umum pada nama + klasifikasi.
             */
            if (
                $keywordScore === 0
                && Str::contains($dutyHaystack, $keyword)
            ) {
                $keywordScore += 8;
            }

            /*
             * Keyword juga terdapat dalam isi tiket.
             * Ini hanya bonus, bukan dasar tunggal pemilihan tupoksi.
             */
            if (
                $keywordScore > 0
                && $ticketHaystack !== ''
                && Str::contains($ticketHaystack, $keyword)
            ) {
                $keywordScore += 2;
            }

            if ($keywordScore > 0) {
                $score += $keywordScore;
                $matchedKeywords[] = $keyword;
            }
        }

        return [
            'score' => $score,
            'matched_keywords' => array_values(
                array_unique($matchedKeywords)
            ),
        ];
    }

    /**
     * Ambil kata yang cukup bermakna dari judul dan deskripsi tiket.
     */
    private function extractTicketKeywords(string $text): array
    {
        $stopWords = [
        'yang',
        'dan',
        'atau',
        'untuk',
        'dengan',
        'dari',
        'pada',
        'tidak',
        'bisa',
        'mohon',
        'tolong',
        'kami',
        'saya',
        'ada',
        'ini',
        'itu',
        'karena',
        'agar',
        'segera',
        'terjadi',
        'mengalami',
        'permintaan',
        'gangguan',

        'tiket',
        'internal',
        'public',
        'setelah',
        'sebelum',
        'integrasi',
        'activity',
        'aktivitas',
        'laporan',
        'harian',
        'pegawai',
        'pic',
        'operasional',
        'sistem',
        'proses',
        'update',
        'data',
    ];

        return collect(
            preg_split(
                '/[\s,.;:\/()\[\]\-_]+/u',
                $this->normalize($text)
            )
        )
            ->filter()
            ->filter(fn ($word) => mb_strlen($word) >= 4)
            ->reject(fn ($word) => in_array($word, $stopWords, true))
            ->unique()
            ->values()
            ->all();
    }

    private function normalize(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9\s]+/', ' ')
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();
    }

    private function notFoundResult(string $reason): array
    {
        return [
            'found' => false,
            'type' => 'not_found',
            'duty' => null,
            'owner_employee' => null,
            'score' => 0,
            'matched_keywords' => [],
            'reason' => $reason,
        ];
    }
}