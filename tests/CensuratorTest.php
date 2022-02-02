<?php

namespace App\Tests;

use App\Service\Censurator;
use PHPUnit\Framework\TestCase;

class CensuratorTest extends TestCase
{
    public function testPurify(): void
    {
        $text = 'Je suis développeur et je programme en php avec SQL serveur';
        $textCensored = 'Je suis développeur et je programme en php avec *** serveur';
        $c = new Censurator();
        $this->assertEquals($textCensored, $c->purify($text));
        $textTest = 'une chaine de caractères avec aucun mot censurés';
        $this->assertEquals($textTest,$c->purify($textTest));
    }
}
