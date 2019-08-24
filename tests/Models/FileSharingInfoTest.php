<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class FileSharingInfoTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var Kunnu\Dropbox\Models\FileSharingInfo $testedClassLoaded */
    protected static $testedClassLoaded;

    /** @var array $data */
    protected static $data;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\Models\FileSharingInfo';

        $class = new Test\Models\TestHelpers\DataProvider;

        self::$data = $class->getFileSharingInfo();

        self::$testedClassLoaded = new self::$testedClass (self::$data);
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        $this->assertNull($constructor->invoke($instance,  self::$data));
    }

    public function testIsReadOnly()
    {
        $this->assertEquals(self::$testedClassLoaded->isReadOnly(), self::$data['read_only']);
    }

    public function testGetParentSharedFolderId()
    {
        $this->assertEquals(self::$testedClassLoaded->getParentSharedFolderId(), self::$data['parent_shared_folder_id']);
    }

    public function testGetModifiedBy()
    {
        $this->assertEquals(self::$testedClassLoaded->getModifiedBy(), self::$data['modified_by']);
    }
}
