<?php
namespace Kunnu\Dropbox\Security;

use PHPUnit\Framework\TestCase;

$testClass = '\Kunnu\Dropbox\Security\OpenSslRandomStringGeneratorTest';

require_once 'Test_Helpers/helperFunctionExists.php';

/**
 * Function used to mock and return $cryptoStrong = False
 *
 * @param  int $length Length of the string to return
 *
 * @param  boolean &$cryptoStrong
 *
 * @return string
 */
function openssl_random_pseudo_bytes($length, &$cryptoStrong)
{
    if (! OpenSslRandomStringGeneratorTest::$cryptoStrong) {
        $cryptoStrong = false;
        return random_bytes($length);
    }

    return \openssl_random_pseudo_bytes($length, $cryptoStrong);
}

/**
 * @coversDefaultClass \Kunnu\Dropbox\Security\OpenSslRandomStringGenerator
 */
class OpenSslRandomStringGeneratorTest extends TestCase
{
    /** @var Boolean $cryptoStrong */
    public static $cryptoStrong = true;

    /** @var Boolean $opensslExists */
    public static $opensslExists = true;

    protected function setUp()
    {
        self::$cryptoStrong = true;
        self::$opensslExists = true;
        $GLOBALS['testClass'] = '\\' . static::class;
    }

    /**
     * returns constructor's output
     *
     * @throws \Kunnu\Dropbox\Exceptions\DropboxClientException
     *
     */
    private function reflectConstructor()
    {
        $classname = '\Kunnu\Dropbox\Security\OpenSslRandomStringGenerator';

        $reflectedClass = new \ReflectionClass($classname);

        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        return $constructor->invoke($instance);
    }

    public function testConstructorCallsInternalMethods()
    {
        // if the function exists
        self::$opensslExists = true;

        // checks return null
        $this->assertNull($this->reflectConstructor());

        self::$opensslExists = false;

        $this->expectException(\Kunnu\Dropbox\Exceptions\DropboxClientException::class);

        $this->reflectConstructor();
    }

    /**
     * @covers ::generateString
     */
    public function testGenerateString()
    {
        $class = new \Kunnu\Dropbox\Security\OpenSslRandomStringGenerator;

        self::$cryptoStrong = true;

        $hexString =  $class->generateString(8);

        // checks if string returned is hex
        $this->assertTrue(ctype_xdigit($hexString));

        $this->expectException(\Kunnu\Dropbox\Exceptions\DropboxClientException::class);

        // test exception on false
        $class->generateString(false);
    }

    /**
     * @depends testGenerateString
     */
    public function testGenerateString2()
    {
        $class = new \Kunnu\Dropbox\Security\OpenSslRandomStringGenerator;

        self::$cryptoStrong = false;

        $this->expectException(\Kunnu\Dropbox\Exceptions\DropboxClientException::class);

        $class->generateString(8);
    }
}
