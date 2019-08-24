<?php
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Kunnu\Dropbox\DropboxFile
 */
class DropboxFileTest extends TestCase
{
    /** @var string $testedClass */
    public static $testedClass;

    /** @var string $testedClassLoaded */
    public static $testedClassLoaded;

    /** @var string $filePath */
    public static $filePath;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\DropboxFile';

        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'DropboxFileTest.php';

        self::$filePath = $filePath;

        self::$testedClassLoaded = new self::$testedClass($filePath);
    }

    public function testStreamFor()
    {
        $resource = 'abcdefg';

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstance(self::$filePath);

        $method = $reflectedClass->getMethod('streamFor');
        $method->setAccessible(true);

        $resource = 'abcdefg';

        $return = $method->invoke($instance, $resource);

        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $return);
    }

    public function testCreateByStream()
    {
        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstance(self::$filePath);

        $method = $reflectedClass->getMethod('createByStream');

        $resource = 'abcdefg';

        $this->assertInstanceOf(self::$testedClass ,$method->invoke($instance, self::$filePath, $resource));
    }

    public function testCreateByPath()
    {
        $MODE_READ = 'r';
        $MODE_WRITE = 'w';

        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'Files_Folder' . DIRECTORY_SEPARATOR . 'text.txt';

        $class = new self::$testedClass($filePath);

        $this->assertInstanceOf(self::$testedClass, $class->CreateByPath($filePath, $MODE_READ));
    }

    public function testClose()
    {
        $mockStream = $this->getMockBuilder('PumpStream')
        ->disableOriginalConstructor()
        ->setMethods(['close'])
        ->getMock();

        $mockStream->expects($this->exactly(2))
        ->method('close')
        ->will(
            $this->returnValue('123')
        );

        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'Files_Folder' . DIRECTORY_SEPARATOR . 'text.txt';

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([$filePath]);

        $property = $reflectedClass->getProperty('stream');
        $property->setAccessible(true);
        $property->setValue($instance, $mockStream);

        $close = $reflectedClass->getMethod('close');

        $this->assertNull($close->invoke($instance));
    }

    public function testSetOffset()
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'Files_Folder' . DIRECTORY_SEPARATOR . 'text.txt';

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([$filePath]);

        $method = $reflectedClass->getMethod('setoffset');
        $method->invoke($instance, 13);

        $property = $reflectedClass->getProperty('offset');
        $property->setAccessible(true);
        $value = $property->getValue($instance);

        $this->assertSame(13, $value);
    }

    public function testSetMaxLength()
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'Files_Folder' . DIRECTORY_SEPARATOR . 'text.txt';

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([$filePath]);

        $method = $reflectedClass->getMethod('setMaxLength');
        $method->invoke($instance, 15);

        $property = $reflectedClass->getProperty('maxLength');
        $property->setAccessible(true);
        $value = $property->getValue($instance);

        $this->assertSame(15, $value);
    }

    public function testGetContents()
    {
        $mockStream = $this->getMockBuilder('PumpStream')
        ->disableOriginalConstructor()
        ->setMethods(['seek', 'read', 'getContents', 'close'])
        ->getMock();

        $mockStream->expects($this->once())
        ->method('seek')
        ->with(
            $this->equalTo(13)
        )
        ->will(
            $this->returnValue('123')
        );

        $mockStream->expects($this->once())
        ->method('read')
        ->with(
            $this->equalTo(15)
        )
        ->will(
            $this->returnValue(15)
        );

        $mockStream->expects($this->any())
        ->method('getContents')
        ->will(
            $this->returnValue('123')
        );


        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'Files_Folder' . DIRECTORY_SEPARATOR . 'text.txt';

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([$filePath]);

        $property = $reflectedClass->getProperty('stream');
        $property->setAccessible(true);
        $property->setValue($instance, $mockStream);

        // set offset !== -1
        // The first condition will be called
        $property = $reflectedClass->getProperty('offset');
        $property->setAccessible(true);
        $property->setValue($instance, 13);

        // call method
        $method = $reflectedClass->getMethod('getContents');
        $method->invoke($instance);

        // set offset === -1
        // The first condition will be ignored
        $property = $reflectedClass->getProperty('offset');
        $property->setAccessible(true);
        $property->setValue($instance, -1);

        // maxLength !== -1
        // second condition will be called
        $property = $reflectedClass->getProperty('maxLength');
        $property->setAccessible(true);
        $property->setValue($instance, 15);

        // call method
        $method = $reflectedClass->getMethod('getContents');

        $this->assertSame(15, $method->invoke($instance));
    }

    public function testGetStream()
    {
        $mockStream = $this->getMockBuilder('PumpStream')
        ->disableOriginalConstructor()
        ->setMethods(['close'])
        ->getMock();

        $mockTestedClass = $this->getMockBuilder(self::$testedClass)
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass->expects($this->once())
        ->method('open')
        ->will(
            $this->returnValue('1a2b')
        );

        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'Files_Folder' . DIRECTORY_SEPARATOR . 'text.txt';

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([$filePath]);

        // stream = null
        $method = $reflectedClass->getMethod('getStream');
        $method->invoke($mockTestedClass);

        // stream != null
        $property = $reflectedClass->getProperty('stream');
        $property->setAccessible(true);
        $property->setValue($instance, $mockStream);

        $method = $reflectedClass->getMethod('getStream');

        $this->assertSame($mockStream, $method->invoke($instance));
    }

    public function testSetStream()
    {
        $mockStream = $this->getMockBuilder('PumpStream')
        ->disableOriginalConstructor()
        ->setMethods(['close'])
        ->getMock();

        $this->assertNull(self::$testedClassLoaded->setStream($mockStream));
    }

    public function testOpenA()
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'Files_Folder' . DIRECTORY_SEPARATOR . 'text.txt';

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $method = $reflectedClass->getMethod('open');

        $mockTestedClass = $this->getMockBuilder(self::$testedClass)
        ->setConstructorArgs([$filePath])
        ->setMethods(['isCreatedFromStream'])
        ->getMock();

        $mockTestedClass->expects($this->once())
        ->method('isCreatedFromStream')
        ->will(
            $this->returnValue(true)
        );

        $this->assertNull($method->invoke($mockTestedClass));

        $mockTestedClass = $this->getMockBuilder(self::$testedClass)
        ->setConstructorArgs([$filePath])
        ->setMethods(['isCreatedFromStream', 'isRemoteFile', 'isNotReadable'])
        ->getMock();

        // is not created from stream
        $mockTestedClass->expects($this->once())
        ->method('isCreatedFromStream')
        ->will(
            $this->returnValue(false)
        );

        // File is not a remote file
        $mockTestedClass->expects($this->once())
        ->method('isRemoteFile')
        ->will(
            $this->returnValue(false)
        );

        // File is not Readable
        $mockTestedClass->expects($this->once())
        ->method('isNotReadable')
        ->will(
            $this->returnValue(true)
        );

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Failed to create DropboxFile instance. Unable to read resource:');

        $method->invoke($mockTestedClass);
    }

    public function testOpenB()
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'Files_Folder' . DIRECTORY_SEPARATOR . 'text.txt';

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $method = $reflectedClass->getMethod('open');


        $mockTestedClass = $this->getMockBuilder(self::$testedClass)
        ->setConstructorArgs([$filePath])
        ->setMethods(['isCreatedFromStream', 'isRemoteFile', 'isNotReadable', 'isNotWritable'])
        ->getMock();

        // is not created from stream
        $mockTestedClass->expects($this->once())
        ->method('isCreatedFromStream')
        ->will(
            $this->returnValue(false)
        );

        // File is not a remote file
        $mockTestedClass->expects($this->once())
        ->method('isRemoteFile')
        ->will(
            $this->returnValue(false)
        );

        // File is Readable
        $mockTestedClass->expects($this->once())
        ->method('isNotReadable')
        ->will(
            $this->returnValue(false)
        );

        // File is not writable
        $mockTestedClass->expects($this->once())
        ->method('isNotWritable')
        ->will(
            $this->returnValue(true)
        );

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Failed to create DropboxFile instance. Unable to write resource:');

        $method->invoke($mockTestedClass);
    }

    public function testOpenC()
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'DropboxFileTest.php';

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $method = $reflectedClass->getMethod('open');


        $mockTestedClass = $this->getMockBuilder(self::$testedClass)
        ->setConstructorArgs([$filePath])
        ->setMethods(['isCreatedFromStream', 'isRemoteFile', 'streamFor'])
        ->getMock();

        // is not created from stream
        $mockTestedClass->expects($this->exactly(2))
        ->method('isCreatedFromStream')
        ->will(
            $this->returnValue(false)
        );

        // File is not a remote file
        $mockTestedClass->expects($this->exactly(2))
        ->method('isRemoteFile')
        ->will(
            $this->returnValue(true)
        );

        // returns true then false
        $mockTestedClass->expects($this->any())
        ->method('streamFor')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue(true),
                $this->returnValue(false)
            )
        );

        $method->invoke($mockTestedClass);

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Failed to create DropboxFile instance. Unable to open resource:');

        $method->invoke($mockTestedClass);
    }

    public function testIsCreatedFromStream()
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'DropboxFileTest.php';

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstance($filePath);

        $method = $reflectedClass->getMethod('isCreatedFromStream');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($instance));
    }

    public function testIsRemoteFile()
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'DropboxFileTest.php';

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstance($filePath);

        $method = $reflectedClass->getMethod('isRemoteFile');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($instance, $filePath));
    }

    public function testIsNotReadable()
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'DropboxFileTest.php';

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstance($filePath);

        $method = $reflectedClass->getMethod('isNotReadable');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($instance));
    }

    public function testIsNotWritable()
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'DropboxFileTest.php';

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstance($filePath);

        $method = $reflectedClass->getMethod('isNotWritable');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($instance));
    }

    public function testgetFileName()
    {
        $class = self::$testedClassLoaded;
        $this->assertSame($class->getFileName(), basename(self::$filePath));
    }

    public function testGetFilePath()
    {
        $class = self::$testedClassLoaded;
        $this->assertSame($class->getFilePath(), self::$filePath);
    }

    public function testGetMode()
    {
        $class = self::$testedClassLoaded;
        $this->assertSame($class->getMode(), 'r');
    }

    public function testgetSize()
    {
        $mockStream = $this->getMockBuilder('PumpStream')
        ->disableOriginalConstructor()
        ->setMethods(['close', 'getSize'])
        ->getMock();

        $mockStream->expects($this->once())
        ->method('getSize')
        ->will(
            $this->returnValue(421)
        );

        $class = self::$testedClassLoaded;
        $class->setStream($mockStream);

        $this->assertSame($class->getSize(), 421);
    }

    public function testgetMimetype()
    {
        $class = self::$testedClassLoaded;
        $this->assertSame($class->getMimetype(), 'text/plain');
    }

}
