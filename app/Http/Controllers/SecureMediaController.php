<?php

namespace App\Http\Controllers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Auth;

class SecureMediaController extends Controller
{
    public function show(Media $media, $conversion = null)
    {
        // 1. Security Check
        if (!Auth::check()) {
            abort(403, 'Unauthorized access to private media.');
        }

        // 2. Resolve Path
        // Spatie's getPath() returns the absolute server path
        $path = $conversion ? $media->getPath($conversion) : $media->getPath();

        // 3. Verify Existence
        if (!file_exists($path)) {
            abort(404, 'File missing from secure storage.');
        }

        // 4. Stream Response
        // response()->file() automatically handles MIME types (image/jpeg, application/pdf, etc)
        return response()->file($path, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate, private',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
