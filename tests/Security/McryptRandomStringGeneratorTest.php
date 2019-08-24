<?php
namespace Kunnu\Dropbox\Security;

use PHPUnit\Framework\TestCase;

$testClass = '\Kunnu\Dropbox\Security\McryptRandomStringGeneratorTest';

require_once 'Test_Helpers/helperFunctionExists.php';

/**
 * Function used to mock mcrypt_create_iv
 *
 * @param  int $length    [description]
 *
 * @param  int $secondArg [description]
 *
 * @return boolean|int
 */
function mcrypt_create_iv($length, $secondArg)
{
    if ( McryptRandomStringGeneratorTest::$mcryptReturnTrue) {
        return '6868686868686868';
    } else {
        return false;
    }
}

/**
 * @coversDefaultClass Kunnu\Dropbox\Security\McryptRandomStringGenerator
 */
class McryptRandomStringGeneratorTest extends TestCase
{

    /** @var Boolean $mcryptExists */
    public static $mcryptExists = true;

    /** @var Boolean $mcryptReturnTrue */
    public static $mcryptReturnTrue = true;

    protected function setUp()
    {
        self::$mcryptExists = true;
        self::$mcryptReturnTrue = true;
        $GLOBALS['testClass'] = '\\' . static::class;
    }

    /**
     * returns constructor's output
     *
     * @throws \Kunnu\Dropbox\Exceptions\DropboxClientException
     *
     */
    private function reflection()
    {

        $classname = '\Kunnu\Dropbox\Security\McryptRandomStringGenerator';

        $reflectedClass = new \ReflectionClass($classname);

        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        return $constructor->invoke($instance);
    }

    public function testConstructorCallsInternalMethods()
    {
        // if the function exists
        self::$mcryptExists = true;

        // checks return null
        $this->assertNull($this->reflection());

        // if the function does not exist
        self::$mcryptExists = false;

        $this->expectException(\Kunnu\Dropbox\Exceptions\DropboxClientException::class);

        $this->reflection();
    }

    /**
     * checks function existence
     */
    public function testFunctionExistence()
    {
        $this->assertTrue(function_exists('mcrypt_create_iv', false));
    }

    /**
     * @covers ::generateString
     * @group depreciation
     * @group mcrypt_create_iv
     */
    public function testGenerateString()
    {
        self::$mcryptExists = true;

        $class = new \Kunnu\Dropbox\Security\McryptRandomStringGenerator;

        $length = 8;

        // tests true return of mcrypt_create_iv
        // should return a binary string
        self::$mcryptReturnTrue = true;

        $result = $class->generateString($length);

        $this->assertTrue(ctype_xdigit($result));

        // tests false return of mcrypt_create_iv
        // should be @throw \Kunnu\Dropbox\Exceptions\DropboxClientException
        self::$mcryptReturnTrue = false;

        $this->expectException(\Kunnu\Dropbox\Exceptions\DropboxClientException::class);

        $result = $class->generateString($length);
    }
}
