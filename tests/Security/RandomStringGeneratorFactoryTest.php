<?php
namespace Kunnu\Dropbox\Security;

use PHPUnit\Framework\TestCase;

$testClass = '\Kunnu\Dropbox\Security\RandomStringGeneratorFactoryTest';

require_once 'Test_Helpers/helperFunctionExists.php';

/**
 * @coversDefaultClass \Kunnu\Dropbox\Security\RandomStringGeneratorFactory
 */
class RandomStringGeneratorFactoryTest extends TestCase
{
    /** @var Boolean $opensslExists */
    public static $opensslExists = true;

    /** @var Boolean $mcryptExists */
    public static $mcryptExists = true;

    protected function setUp()
    {
        self::$mcryptExists = true;
        self::$opensslExists = true;
        $GLOBALS['testClass'] = '\\' . static::class;
    }

    /**
     * Checks function existence
     */
    public function testFunctionExist()
    {
        $this->assertTrue(function_exists('mcrypt_create_iv', false));
        $this->assertTrue(function_exists('openssl_random_pseudo_bytes', false));
    }

    /**
     * @depends testFunctionExist
     * @covers ::makeRandomStringGenerator
     */
    public function testMakeRandomStringGenerator()
    {
        $class = new \Kunnu\Dropbox\Security\RandomStringGeneratorFactory;

        // test null
        $class->makeRandomStringGenerator(null);

        // both functions exist
        self::$opensslExists = true;
        self::$mcryptExists = true;

        // Reflects protected method defaultRandomStringGenerator()
        $resultingClass = $this->reflection();

        $this->assertEquals($resultingClass, new \Kunnu\Dropbox\Security\McryptRandomStringGenerator);

        // mocks RandomStringGeneratorInterface
        $mockRandomStringGeneratorInterface = $this->getMockBuilder(\Kunnu\Dropbox\Security\RandomStringGeneratorInterface::class)->getMock();

        // passing mock to function
        $return = $class->makeRandomStringGenerator($mockRandomStringGeneratorInterface);

        // checking if it returns the same value
        $this->assertSame($mockRandomStringGeneratorInterface, $return);

        // test mcrypt
        $return = $class->makeRandomStringGenerator('mcrypt');
        $this->assertInstanceOf(\Kunnu\Dropbox\Security\McryptRandomStringGenerator::class, $return);

        // test openssl
        $return = $class->makeRandomStringGenerator('openssl');
        $this->assertInstanceOf(\Kunnu\Dropbox\Security\OpenSslRandomStringGenerator::class, $return);

        // test not accepted value
        $this->expectException(\InvalidArgumentException::class);
        $return = $class->makeRandomStringGenerator(false);
    }

    /**
     * @covers ::defaultRandomStringGenerator
     * @group depreciation
     * @group mcrypt_create_iv_check
     */
    public function testDefaultRandomStringGenerator()
    {
        // both functions exist
        self::$opensslExists = true;
        self::$mcryptExists = true;

        // reflect protected method
        $resultingClass = $this->reflection();

        $this->assertEquals($resultingClass, new \Kunnu\Dropbox\Security\McryptRandomStringGenerator);

        // only $opensslExists
        self::$mcryptExists = false;
        self::$opensslExists = true;

        // reflect protected method
        $resultingClass = $this->reflection();

        $this->assertEquals($resultingClass, new \Kunnu\Dropbox\Security\OpenSslRandomStringGenerator);

        // both functions do not exist
        self::$opensslExists = false;

        $this->expectException(\Kunnu\Dropbox\Exceptions\DropboxClientException::class);

        // reflect protected method
        $resultingClass = $this->reflection();
    }

    private function reflection()
    {
        $className = '\Kunnu\Dropbox\Security\RandomStringGeneratorFactory';

        $method = new \ReflectionMethod($className, 'defaultRandomStringGenerator');
        $method->setAccessible(true);

        return $method->invoke(new \Kunnu\Dropbox\Security\RandomStringGeneratorFactory);
    }
}
