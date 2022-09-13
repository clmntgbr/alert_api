<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;

class GetAttribute
{
    public function get(string $key, Request $request): string
    {
        $value = $request->attributes->get($key);
        if (gettype($value) !== 'string') {
            return '';
        }

        return $value;
    }
}