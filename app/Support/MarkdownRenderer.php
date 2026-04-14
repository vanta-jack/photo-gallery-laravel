<?php

namespace App\Support;

use Illuminate\Support\Str;

class MarkdownRenderer
{
    public static function toSafeHtml(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        return Str::markdown($value, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}
