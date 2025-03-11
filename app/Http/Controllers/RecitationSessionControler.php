<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\RecitationSession;
use Illuminate\Support\Facades\View;

class RecitationSessionControler extends Controller
{
    
    public function preview()
    {
        $recitation = RecitationSession::all();
        $html = View::make('pdf.recitation', compact('recitation'))->render();

        return response($html);
    }
    public function download(Request $request)
    {
        $timeRange = $request->input('time_range');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

    
        if ($timeRange === 'custom' && (!$startDate || !$endDate)) {
            return back()->withErrors(['error' => 'يرجى تحديد تاريخ البداية والنهاية للتقرير المخصص']);
        }

        
        $sessions = RecitationSession::query()
            ->when($timeRange === 'monthly', function ($query) {
                $query->whereBetween('session_date', [
                    Carbon::now()->startOfMonth()->toDateString(),
                    Carbon::now()->endOfMonth()->toDateString(),
                ]);
            })
            ->when($timeRange === 'yearly', function ($query) {
                $query->whereBetween('session_date', [
                    Carbon::now()->startOfYear()->toDateString(),
                    Carbon::now()->endOfYear()->toDateString(),
                ]);
            })
            ->when($timeRange === 'custom', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('session_date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()]);
            })
            ->get();

            $html = View::make('pdf.recitation', compact('sessions', 'timeRange', 'startDate', 'endDate'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'Cairo',
            'dpi' => 300,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'I')),
            'تقرير-حصص-التسميع.pdf'
        );
    }
}
