<?php

namespace App\Services;

class YoutubeService
{
    /**
     * Extract the Video ID from various YouTube URL formats.
     */
    public static function extractId(?string $url): ?string
    {
        if (!$url) return null;

        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }

        return strlen($url) === 11 ? $url : null;
    }

    /**
     * Build the privacy-compliant Embed URL.
     */
    public static function getEmbedUrl(string $id): string
    {
        $params = [
            'rel' => 0,
            'modestbranding' => 1,
            'enablejsapi' => 1,
            'origin' => config('app.url'),
        ];

        return "https://www.youtube-nocookie.com/embed/{$id}?" . http_build_query($params);
    }

    /**
     * Get the high-resolution thumbnail from YouTube.
     */
    public static function getThumbnail(string $id): string
    {
        return "https://img.youtube.com/vi/{$id}/maxresdefault.jpg";
    }
}
