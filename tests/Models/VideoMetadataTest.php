<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class VideoMetadataTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var Kunnu\Dropbox\Models\FolderSharingInfo $testedClassLoaded */
    protected static $testedClassLoaded;

    /** @var array $data */
    protected static $data;

    protected function setUp()
    {
        $data = new Test\Models\TestHelpers\DataProvider();

        $getMediaInfoProperty = $data->getMediaInfoProperty('video');

        self::$data = $getMediaInfoProperty['metadata'];

        self::$testedClass = 'Kunnu\Dropbox\Models\VideoMetadata';

        self::$testedClassLoaded = new self::$testedClass(self::$data);
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        $cst = $constructor->invoke($instance,  self::$data);

        $this->assertNull($cst);
    }

    public function testGetDuration()
    {
        $this->assertEquals(self::$testedClassLoaded->getDuration(), self::$data['duration']);
    }
}
