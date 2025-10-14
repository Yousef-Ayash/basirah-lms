<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Models\StudentExamAttempt;

class ExportController extends Controller
{
    public function exportAttemptsPdf(Request $request)
    {
        $user = $request->user();
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 15);

        $attempts = StudentExamAttempt::with(['exam', 'marksReport'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // mPDF options: register font dir and font data
        $defaultFontDirs = (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'];
        $defaultFontData = (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'];

        $fontDir = array_merge($defaultFontDirs, [public_path('fonts')]);

        $fontData = $defaultFontData;
        $fontData['notokufi'] = [
            'R' => 'NotoKufiArabic-Regular.ttf',
            'B' => 'NotoKufiArabic-Bold.ttf',
            // add other variants if present
        ];

        // Create mPDF instance
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'fontDir' => $fontDir,
            'fontdata' => $fontData,
            'default_font' => 'notokufi', // use the font key we registered
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        // Prepare HTML from a Blade view (you can reuse /resources/views/pdfs/attempts_index_ar.blade.php)
        $html = View::make('pdfs.attempts_index_ar', [
            'attempts' => $attempts,
            'generated_at' => now(),
        ])->render();

        // mPDF supports RTL; ensure the HTML wrapper has dir="rtl" or set direction via CSS
        // If your blade view already includes <html dir="rtl"> it's fine.
        $mpdf->WriteHTML($html);

        $fileName = 'attempts-' . $user->id . '-' . now()->format('Ymd_His') . '.pdf';

        // Output for download
        return response($mpdf->Output($fileName, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }
}
