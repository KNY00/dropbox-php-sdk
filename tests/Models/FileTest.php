<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class FileTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var array $data */
    protected static $data;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\Models\File';

        $class = new Test\Models\TestHelpers\DataProvider;

        self::$data = $class->getDataFile();
    }

    public function testConstructorCallsInternalMethods()
    {
        $contentsString = 'hello';

        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        // invoke constructor
        // with string as second parameter
        $this->assertNull($constructor->invoke($instance,  self::$data, $contentsString));
    }

    public function testGetMetadata()
    {
        $class = new self::$testedClass(self::$data, '');

        $this->assertInstanceOf('Kunnu\Dropbox\Models\FileMetadata', $class->getMetadata());

        $this->assertEquals($class->getMetadata(), new Kunnu\Dropbox\Models\FileMetadata(self::$data));
    }

    public function testGetContents()
    {
        // mock DropboxFile
        $contentsObject = $this->getMockBuilder('Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        $contentsObject->expects($this->once())
        ->method('getContents')
        ->will(
            $this->returnValue('anothervalue')
        );

        // invoke constructor
        // with mock of Kunnu\Dropbox\DropboxFile as second parameter
        $class = new self::$testedClass(self::$data, $contentsObject);

        $this->assertEquals($class->getContents(), 'anothervalue');

        // invoke constructor
        // with string as second parameter
        $contentsString = 'hello world';

        $class = new self::$testedClass(self::$data, $contentsString);

        $this->assertEquals($class->getContents(), $contentsString);
    }
}
