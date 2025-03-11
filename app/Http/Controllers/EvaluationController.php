<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function updateEvaluation(Request $request)
    {
        $evaluation = Evaluation::findOrFail($request->evaluationId);
        $evaluation->update([
            'tajweed_score' => $request->tajweed_score,
            'voice_score' => $request->voice_score,
            'memorization_score' => $request->memorization_score,
            'total_score' => ($request->tajweed_score + $request->voice_score + $request->memorization_score) / 3,
            'status' => $request->total_score >= 80 ? 'passed' : 'pending',
        ]);

        return redirect()->back()->with('success', 'تم تحديث التقييم بنجاح.');
    }
}
