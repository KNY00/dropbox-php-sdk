<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class FileMetadataTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var Kunnu\Dropbox\Models\FileMetadata $testedClassLoaded */
    protected static $testedClassLoaded;

    /** @var array $data */
    protected static $data;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\Models\FileMetadata';

        // media_info MediaInfo? Additional information if the file is a photo or video.
        // This field will not be set on entries returned by list_folder, list_folder/continue,
        // or get_thumbnail_batch,
        // starting December 2, 2019. This field is optional.
        $class = new Test\Models\TestHelpers\DataProvider;

        // added media info property
        $class->addMediaInfoProperty('video');

        self::$data = $class->getMetadataFile();

        self::$testedClassLoaded = new self::$testedClass (self::$data);
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        $this->assertNull($constructor->invoke($instance,  self::$data));
    }


    public function testGetId()
    {
        $this->assertEquals(self::$testedClassLoaded->getId(), self::$data['id']);
    }

    public function testGetTag()
    {
        $this->assertEquals(self::$testedClassLoaded->getTag(), self::$data['.tag']);
    }

    public function testGetName()
    {
        $this->assertEquals(self::$testedClassLoaded->getName(), self::$data['name']);
    }

    public function testGetRev()
    {
        $this->assertEquals(self::$testedClassLoaded->getRev(), self::$data['rev']);
    }

    public function testGetSize()
    {
        $this->assertEquals(self::$testedClassLoaded->getSize(), self::$data['size']);
    }

    public function testGetPathLower()
    {
        $this->assertEquals(self::$testedClassLoaded->getPathLower(), self::$data['path_lower']);
    }

    public function testGetMediaInfo()
    {
        $this->assertEquals(self::$testedClassLoaded->getMediaInfo(), new Kunnu\Dropbox\Models\MediaInfo(self::$data['media_info']));
    }

    public function testGetSharingInfo()
    {
        $this->assertEquals(self::$testedClassLoaded->getSharingInfo(), new Kunnu\Dropbox\Models\FileSharingInfo(self::$data['sharing_info']));
    }

    public function testGetPathDisplay()
    {
        $this->assertEquals(self::$testedClassLoaded->getPathDisplay(), self::$data['path_display']);
    }

    public function testGetClientModified()
    {
        $this->assertEquals(self::$testedClassLoaded->getClientModified(), self::$data['client_modified']);
    }

    public function testGetServerModified()
    {
        $this->assertEquals(self::$testedClassLoaded->getServerModified(), self::$data['server_modified']);
    }

    public function testHasExplicitSharedMembers()
    {
        $this->assertEquals(self::$testedClassLoaded->hasExplicitSharedMembers(), self::$data['has_explicit_shared_members']);
    }

}
