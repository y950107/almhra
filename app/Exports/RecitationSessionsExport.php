<?php

namespace App\Exports;

use App\Models\RecitationSession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RecitationSessionsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return RecitationSession::with('student',  'halaka')
            ->get()
            ->map(function ($session) {
                return [
                    'التاريخ' => $session->session_date,
                    'الطالب' => $session->student->name ?? 'غير متوفر',
                    //'المعلم' => $session->teacher->name ?? 'غير متوفر',
                    'الحلقة' => $session->halaka->name ?? 'غير متوفر',
                    'النتيجة' => $session->memory_score . '/10',
                ];
            });
    }

    public function headings(): array
    {
        return ['التاريخ', 'الطالب', 'المعلم', 'الحلقة', 'النتيجة'];
    }
}
