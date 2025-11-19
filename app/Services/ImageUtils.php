<?php

namespace App\Services;

use function PHPUnit\Framework\assertFalse;

final class ImageUtils
{
    public static function getBase64( string $path ): string
    {
        $type = pathinfo( $path, PATHINFO_EXTENSION );
        $data = base64_encode( file_get_contents( $path ) );

        return 'data:image/' . $type . ';base64,' . $data;
    }
}
