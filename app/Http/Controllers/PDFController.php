<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;

class PDFController extends Controller
{
    // ✅ معاينة PDF في المتصفح
    public function preview()
    {
        $teachers = Teacher::all();
        $html = View::make('pdf.teachers', compact('teachers'))->render();

        return response($html);
    }

    public function download(Request $request)
    {
        $teacherIds = $request->input('teacher_ids', []);

        $teachers = Teacher::when(!empty($teacherIds), function ($query) use ($teacherIds) {
            $query->whereIn('id', $teacherIds);
        })->get();

        $html = View::make('pdf.teachers', compact('teachers'))->render();

        $mpdf = new Mpdf([
            'tempDir' => storage_path('tempdir'),
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'Cairo',
            'dpi' => 300,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 5,
            'margin_right' => 5,
            'shrink_tables_to_fit' => 1,
        ]);

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'I')),
            'قائمة-المعلمين.pdf'
        );
    }


    public function downloadStudentsPresence(Request $request)
    {
        // Optionally filter by date range
        $from = $request->input('start_date');
        $to = $request->input('end_date');
        $teachersIds = $request->input('teachers');
        $students = Student::query()->whereIn('teacher_id',$teachersIds)
            ->with(['recitationSessions' => function ($query) use ($from, $to) {
            if ($from && $to) {
                $query->whereBetween('session_date', [$from, $to]);
            }

            $query->select('id', 'student_id', 'session_date', 'present');
        }])->get();

        // Compute individual stats and accumulate totals
        $totalPresence = 0;
        $totalAbsence = 0;
        $totalSessions = 0;

        foreach ($students as $student) {
            $presenceCount = $student->recitationSessions->where('present', 'present')->count();
            $absenceCount = $student->recitationSessions->where('present', '!=', 'present')->count();
            $sessionCount = $student->recitationSessions->count();

            $student->presence_count = $presenceCount;
            $student->absence_count = $absenceCount;
            $student->total_sessions = $sessionCount;

            $student->presence_percentage = $sessionCount > 0
                ? (int)round(($presenceCount / $sessionCount) * 100)
                : 0;

            $student->absence_percentage = $sessionCount > 0
                ? (int)round(($absenceCount / $sessionCount) * 100)
                : 0;

            $totalPresence += $presenceCount;
            $totalAbsence += $absenceCount;
            $totalSessions += $sessionCount;
        }

        // Overall stats
        $overallStats = [
            'total_presence' => $totalPresence,
            'total_absence' => $totalAbsence,
            'total_sessions' => $totalSessions,
            'total_presence_percentage' => $totalSessions > 0
                ? (int)round(($totalPresence / $totalSessions) * 100)
                : 0,
            'total_absence_percentage' => $totalSessions > 0
                ? (int)round(($totalAbsence / $totalSessions) * 100)
                : 0,
        ];

        // Render PDF view
        $html = View::make('pdf.students-presence', compact('students', 'overallStats'))->render();

        $mpdf = new Mpdf([
            'tempDir' => storage_path('tempdir'),
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'Cairo',
            'dpi' => 300,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 5,
            'margin_right' => 5,
            'shrink_tables_to_fit' => 1,
        ]);

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'I')),
            'تقرير-الحضور-للطلاب.pdf'
        );
    }

    public function downloadStudentsReport(Request $request)
    {

        $from = $request->input('start_date');
        $to = $request->input('end_date');
        $teachersIds = $request->input('teachers');
        $students = Student::query()->whereIn('teacher_id',$teachersIds)
            ->when($from, function (Builder $query) use($from) {
            $query->whereDate('start_date','>=',$from);
        })->when($to, function (Builder $query) use($to) {
            $query->whereDate('start_date','<=',$to);
        })->get();



        $html = View::make('pdf.students', compact('students'))->render();

        $mpdf = new Mpdf([
            'tempDir' => storage_path('tempdir'),
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'Cairo',
            'dpi' => 300,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 5,
            'margin_right' => 5,
            'shrink_tables_to_fit' => 1,
        ]);

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'I')),
            'قائمة-الطلاب.pdf'
        );
    }


    // pdf Download
    // public function download()
    // {
    //     $teachers = Teacher::all();
    //     $html = View::make('pdf.teachers', compact('teachers'))->render();

    //     $mpdf = new Mpdf([
    //         'mode' => 'utf-8',
    //         'format' => 'A4',
    //         'default_font' => 'NotoKufiArabicMedium',
    //     ]);

    //     $mpdf->WriteHTML($html);
    //     return response()->streamDownload(
    //         fn () => print($mpdf->Output('', 'S')),
    //         'قائمة-المعلمين.pdf'
    //     );
    // }
}
