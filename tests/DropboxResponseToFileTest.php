<?php
use PHPUnit\Framework\TestCase;

class DropboxResponseToFileTest extends TestCase
{
    /** @var string $testedClass */
    public static $testedClass;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\DropboxResponseToFile';
    }

    public function testGetBody()
    {
        $mockRequest = $this->getMockBuilder('Kunnu\Dropbox\DropboxRequest')
        ->disableOriginalConstructor()
        ->getMock();

        $mockFile = $this->getMockBuilder('Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        $mockFile->expects($this->once())
        ->method('getContents')
        ->will(
            $this->returnValue('content')
        );

        $request = $mockRequest;
        $file = $mockFile;
        $httpStatusCode = null;
        $headers = [];

        $class = new self::$testedClass($request, $file, $httpStatusCode, $headers);

        $this->assertEquals($class->getBody(), 'content');
    }

    public function testGetFilePath()
    {
        $filePath = '/path/file/';

        $mockRequest = $this->getMockBuilder('Kunnu\Dropbox\DropboxRequest')
        ->disableOriginalConstructor()
        ->getMock();

        $mockFile = $this->getMockBuilder('Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        $mockFile->expects($this->once())
        ->method('getFilePath')
        ->will(
            $this->returnValue($filePath)
        );

        $request = $mockRequest;
        $file = $mockFile;
        $httpStatusCode = null;
        $headers = [];

        $class = new self::$testedClass($request, $file, $httpStatusCode, $headers);

        $this->assertEquals($class->getFilePath(), $filePath);
    }
}
