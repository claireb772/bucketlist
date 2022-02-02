<?php

namespace App\Service;

class Censurator
{
    const TABOU_WORDS = ['covid', 'sql', 'mourir'];

    public function purify(String $text){

        $pattern = '#('.implode('|',self::TABOU_WORDS).')#i';
        return preg_replace_callback($pattern,
            function ($matches) {
            return str_pad('',strlen($matches[0]), '*');
        }
        ,$text);

    }


}