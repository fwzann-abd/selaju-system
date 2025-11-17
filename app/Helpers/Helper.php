<?php

namespace App\Helpers;

class Helper
{
    /**
     * Get the full web URL for a Sejajan store photo.
     *
     * @param string|null $filename The photo filename (e.g., '1763351816_1761729372932.jpg')
     * @return string|null The full URL or null if filename is empty
     */
    public static function getPhotoPath($filename)
    {
        if (empty($filename)) {
            return null;
        }

        $appUrl = config('app.url');
        return rtrim($appUrl, '/') . '/assets/modules/sejajan/mart/' . $filename;
    }

    /**
     * Get the base photo path for Sejajan store photos.
     *
     * @return string The base photo directory path
     */
    public static function getPhotoBasePath()
    {
        $appUrl = config('app.url');
        return rtrim($appUrl, '/') . '/assets/modules/sejajan/mart/';
    }
}
