<?php

namespace App\Service;

class Censurator
{
    const TABOU_WORDS = ['covid', 'sql', 'mourir'];

    public function purify(String $text){

        /*
         * preg_match($motsInterdits,$string, $result)
         * foreach ($result as $match) {
         *  $numOfLetters = strlen($match);
         *  $res = preg_replace($motInterdits,'#^\*{'.$numOfLetters.'}$#',$string)
         * }
        return $res*/
        $pattern = '#('.implode('|',self::TABOU_WORDS).')#i';
        return preg_replace($pattern,'*******',$text);

    }

}