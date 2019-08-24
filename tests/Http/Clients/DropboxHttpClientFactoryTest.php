<?php
use PHPUnit\Framework\TestCase;

class DropboxHttpClientFactoryTest extends TestCase
{

    public function testMake()
    {
        $hadlerMock = $this->getMockBuilder('Kunnu\Dropbox\Http\Clients\DropboxHttpClientInterface')->getMock();

        $class = new Kunnu\Dropbox\Http\Clients\DropboxHttpClientFactory;

        $this->assertInstanceOf('Kunnu\Dropbox\Http\Clients\DropboxGuzzleHttpClient', $class->make(null));

        $this->assertSame($hadlerMock, $class->make($hadlerMock));

        $hadlerMock = $this->getMockBuilder('GuzzleHttp\Client')->getMock();

        $this->assertInstanceOf('Kunnu\Dropbox\Http\Clients\DropboxGuzzleHttpClient', $class->make($hadlerMock));

        $hadlerMock = $this->getMockBuilder('Kunnu\Dropbox\Http\DropboxRawResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $this->expectException('InvalidArgumentException');

        $class->make($hadlerMock);
    }
}
