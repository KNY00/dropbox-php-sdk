<?php
use PHPUnit\Framework\TestCase;

class RequestBodyStreamTest extends TestCase
{
    /** @var string $testedClass */
    public static $testedClass = 'Kunnu\Dropbox\Http\RequestBodyStream';

    public function testConstructorCallsInternalMethods()
    {
        /** @var Kunnu\Dropbox\DropboxFile $dropboxFileMock mock */
        $dropboxFileMock = $this->getMockBuilder('Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        $dropboxFileMock->expects($this->once())
        ->method('getContents')
        ->will(
            $this->returnValue('abc')
        );

        $client = new GuzzleHttp\Client;

        $reflectedClass = new ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        // instantiate class with empty array
        $constructor->invoke($instance, $dropboxFileMock);

        $getBody = $reflectedClass->getMethod('getBody');

        $this->assertEquals($getBody->invoke($instance), 'abc');
    }
}
