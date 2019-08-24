<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class DeletedMetadataTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var array $data */
    protected static $data;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\Models\DeletedMetadata';

        $class = new Test\Models\TestHelpers\DataProvider;

        self::$data = $class->getMetadataDeltedFile();
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        $this->assertNull($constructor->invoke($instance,  self::$data));
    }

    public function testGetName()
    {
        $class = new self::$testedClass(self::$data);

        $name = $class->getName();

        $this->assertEquals($name, self::$data['name']);
    }

    public function testGetPathLower()
    {
        $class = new self::$testedClass(self::$data);

        $pathLower = $class->getPathLower();

        $this->assertEquals($pathLower, self::$data['path_lower']);
    }

    public function testGetPathDisplay()
    {
        $class = new self::$testedClass(self::$data);

        $pathDisplay = $class->getPathDisplay();

        $this->assertEquals($pathDisplay, self::$data['path_display']);
    }

    public function testGetSharingInfo()
    {
        $class = new self::$testedClass(self::$data);

        $sharingInfo = $class->getSharingInfo();

        $this->assertEquals($sharingInfo, self::$data['sharing_info']);
    }
}
