<?php

namespace App\Service;

class UniqueNumberGenerator
{
    /**
     * @param $size
     * @return false|string
     */
    public function generate($size, $type = true): string
    {
        $chaines = ($type) ? "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ" : "0123456789";

        $chaines = str_shuffle($chaines);
        $chaines = substr($chaines,0, $size);

        return $chaines;
    }
}