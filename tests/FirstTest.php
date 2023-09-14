<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
//use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase ; // permet de tester dans le cadre du Kernel, surtout pour les tests fonctionnels → tester dans un contexte d'application
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase ; // pratique pour tester les Controllers surtout → permet d'envoyer une requête et voir la réponse qui est retournée

class FirstTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testAdd(int $a, int $b, int $expected): void
    {
        $this->assertSame($expected, $a + $b);
    }

    public function additionProvider(): array
    {
        return [
            [0, 0, 0],
            [0, 1, 1],
            [1, 0, 1],
            [1, 1, 2]
        ];
    }

}