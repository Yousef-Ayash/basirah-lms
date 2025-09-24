<?php
namespace App\Jobs;

use App\Models\SubjectMaterial;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Exception;
use Imagick;

class GeneratePdfThumbnailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $materialId;

    public function __construct(int $materialId)
    {
        $this->materialId = $materialId;
    }

    public function handle()
    {
        $material = SubjectMaterial::find($this->materialId);
        if (!$material || $material->type !== 'pdf' || !$material->file_path) {
            return;
        }

        $disk = Storage::disk('public');
        $pdfPath = $material->file_path;

        if (!$disk->exists($pdfPath)) {
            \Log::warning("PDF file not found for material {$this->materialId}");
            return;
        }

        try {
            // Attempt to use Imagick if available
            if (class_exists('Imagick')) {
                $tmpFile = $disk->path($pdfPath);

                $imagick = new Imagick();
                $imagick->setResolution(150, 150); // decent resolution
                $imagick->readImage($tmpFile . '[0]'); // first page
                $imagick->setImageFormat('jpeg');
                $imageBlob = $imagick->getImageBlob();
                $imagick->clear();
                $imagick->destroy();

                $img = Image::make($imageBlob);

                // resize to mainly 800px width
                $img->resize(800, null, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

                $basename = pathinfo($pdfPath, PATHINFO_FILENAME);
                $thumbPath = "materials/thumbnails/{$basename}.jpg";

                $disk->put($thumbPath, (string) $img->encode('jpg', 80));
                \Log::info("PDF thumbnail created for material {$material->id}");
                return;
            }

            // If Imagick not available, try Ghostscript via shell (optional)
            // note: many systems won't allow exec. Wrap in try/catch.
            if (function_exists('exec')) {
                $tmpPdf = $disk->path($pdfPath);
                $tmpOut = sys_get_temp_dir() . '/' . pathinfo($pdfPath, PATHINFO_FILENAME) . '.jpg';

                // Example ghostscript command (may vary by system)
                $cmd = "gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -dFirstPage=1 -dLastPage=1 -r150 -sOutputFile=" . escapeshellarg($tmpOut) . " " . escapeshellarg($tmpPdf) . " 2>&1";
                exec($cmd, $output, $returnVar);

                if ($returnVar === 0 && file_exists($tmpOut)) {
                    $img = Image::make($tmpOut);
                    $img->resize(800, null, function ($c) {
                        $c->aspectRatio();
                        $c->upsize();
                    });
                    $basename = pathinfo($pdfPath, PATHINFO_FILENAME);
                    $thumbPath = "materials/thumbnails/{$basename}.jpg";
                    $disk->put($thumbPath, (string) $img->encode('jpg', 80));
                    @unlink($tmpOut);
                    \Log::info("PDF thumbnail created via Ghostscript for material {$material->id}");
                    return;
                }
            }

            \Log::warning("No PDF thumbnail method available (Imagick/Ghostscript missing) for material {$material->id}.");
        } catch (Exception $e) {
            \Log::error("GeneratePdfThumbnailJob error for material {$material->id}: " . $e->getMessage());
        }
    }
}
