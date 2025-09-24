<?php
namespace App\Services;

class YouTubeService
{
    public static function extractYoutubeId(string $url): ?string
    {
        // Common patterns: v=ID, youtu.be/ID, embed/ID
        if (preg_match('/(?:youtube\.com\/.*v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_-]{11})/i', $url, $m)) {
            return $m[1];
        }
        // fallback: parse query for v param
        $parts = parse_url($url);
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $qs);
            if (!empty($qs['v']) && preg_match('/^[A-Za-z0-9_-]{11}$/', $qs['v'])) {
                return $qs['v'];
            }
        }

        return null;
    }
}