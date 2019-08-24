<?php
use PHPUnit\Framework\TestCase;

class RandomStringGeneratorTraitTest extends TestCase
{
    public function testBinToHex()
    {
        $mock = $this->getMockForTrait(Kunnu\Dropbox\Security\RandomStringGeneratorTrait::class);

        $string = 'Hello';

        $binary = bin2hex($string);

        $hex = $mock->binToHex($string, 10);

        $this->assertSame($hex, $binary);
    }
}
