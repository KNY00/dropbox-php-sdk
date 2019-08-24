<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class FolderMetadataTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var Kunnu\Dropbox\Models\FolderMetadata $testedClassLoaded */
    protected static $testedClassLoaded;

    /** @var array $data */
    protected static $dataFolder;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\Models\FolderMetadata';

        $class = new  Test\Models\TestHelpers\DataProvider;

        self::$dataFolder = $class->getDataFolder();

        self::$testedClassLoaded = new self::$testedClass (self::$dataFolder);
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        $this->assertNull($constructor->invoke($instance,  self::$dataFolder));
    }

    public function testGetId()
    {
        $this->assertEquals(self::$testedClassLoaded->getId(), self::$dataFolder['id']);
    }

    public function testGetTag()
    {
        $this->assertEquals(self::$testedClassLoaded->getTag(), self::$dataFolder['.tag']);
    }

    public function testGetName()
    {
        $this->assertEquals(self::$testedClassLoaded->getName(), self::$dataFolder['name']);
    }

    public function testGetPathLower()
    {
        $this->assertEquals(self::$testedClassLoaded->getPathLower(), self::$dataFolder['path_lower']);
    }

    public function testGetSharingInfo()
    {
        $this->assertEquals(self::$testedClassLoaded->getSharingInfo(), new Kunnu\Dropbox\Models\FolderSharingInfo(self::$dataFolder['sharing_info']));
    }

    public function testGetPathDisplay()
    {
        $this->assertEquals(self::$testedClassLoaded->getPathDisplay(), self::$dataFolder['path_display']);
    }
}
