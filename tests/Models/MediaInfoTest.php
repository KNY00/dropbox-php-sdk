<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class MediaInfoTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var Kunnu\Dropbox\Models\MediaInfo $testedClassLoaded */
    protected static $testedClassLoaded;

    /** @var array $data */
    protected static $data;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\Models\MediaInfo';

        $class = new  Test\Models\TestHelpers\DataProvider;

        self::$data = $class->getMediaInfoProperty();

        self::$testedClassLoaded = new self::$testedClass (self::$data);
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $data = self::$data;
        $data['pending'] = true;

        $constructor = $reflectedClass->getConstructor();

        $this->assertNull($constructor->invoke($instance,  $data));
    }

    public function testSetMediaMetadata()
    {
        $namespace = 'Kunnu\Dropbox\Models';

        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        // .tag = video treated in constructor

        // if .tag photo
        $class = new  Test\Models\TestHelpers\DataProvider;
        $data = $class->getMediaInfoProperty('photo');

        $constructor->invoke($instance,  $data);

        $fx = $reflectedClass->getMethod('getMediaMetadata');

        $this->assertInstanceOf($namespace . '\PhotoMetadata', $fx->invoke($instance));

        // if .tag other
        // $metadata returns array but empty
        $class = new  Test\Models\TestHelpers\DataProvider;
        $data = $class->getMediaInfoProperty('other');

        $constructor->invoke($instance,  $data);

        $fx = $reflectedClass->getMethod('getMediaMetadata');

        $this->assertInstanceOf($namespace . '\MediaMetadata', $fx->invoke($instance));
    }

    public function testIsPending()
    {
        $class = self::$testedClassLoaded;
        $this->assertNull($class->isPending());
    }

    public function testGetMediaMetadata()
    {
        $class = self::$testedClassLoaded;
        $this->assertInstanceOf('Kunnu\Dropbox\Models' . '\VideoMetadata', $class->getMediaMetadata());
    }
}
