<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use Illuminate\Http\Request;
use App\Models\Teacher;
use Illuminate\Support\Facades\View;

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
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'NotoKufiArabicMedium', 
        ]);

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn () => print($mpdf->Output('', 'S')),
            'قائمة-المعلمين.pdf'
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
