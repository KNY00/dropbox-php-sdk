<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class MediaMetadataTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var Kunnu\Dropbox\Models\MediaMetadata $testedClassLoaded */
    protected static $testedClassLoaded;

    /** @var array $data */
    protected static $data;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\Models\MediaMetadata';

        $class = new  Test\Models\TestHelpers\DataProvider;

        self::$data = $class->getMediaInfoMetadata('photo');

        self::$testedClassLoaded = new self::$testedClass (self::$data);
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        $this->assertNull($constructor->invoke($instance,  self::$data));
    }

    public function testGetLocation()
    {
        $class = self::$testedClassLoaded;
        $this->assertSame($class->getLocation(), self::$data['location']);
    }

    public function testGetDimensions()
    {
        $class = self::$testedClassLoaded;
        $this->assertSame($class->getDimensions(), self::$data['dimensions']);
        $this->assertArrayHasKey('height', $class->getDimensions());
    }

    public function testGetTimeTaken()
    {
        $class = self::$testedClassLoaded;
        $this->assertInstanceOf('DateTime', $class->getTimeTaken());
    }
}
