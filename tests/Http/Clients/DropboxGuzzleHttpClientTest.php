<?php
use PHPUnit\Framework\TestCase;

class DropboxGuzzleHttpClientTest extends TestCase
{
    /** @var string $testedClass */
    public static $testedClass = 'Kunnu\Dropbox\Http\Clients\DropboxGuzzleHttpClient';

    public function testConstructorCallsInternalMethods()
    {
        $client = new GuzzleHttp\Client;

        $reflectedClass = new ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceWithoutConstructor();

        // access protected property
        $clientProp = $reflectedClass->getProperty('client');
        $clientProp->setAccessible(true);

        $constructor = $reflectedClass->getConstructor();

        // invoke and save constructor's return
        $outputConstructor = $constructor->invoke($instance, $client);

        // save protected property value
        $property = $clientProp->getValue($instance);

        $this->assertNull($outputConstructor);
        $this->assertSame($client, $property);
    }

    public function testGetClient()
    {
        $clientMock = $this->getMockBuilder('GuzzleHttp\Client')
        ->disableOriginalConstructor()
        ->getMock();

        $class = new self::$testedClass($clientMock);

        $this->assertSame($class->getClient(), $clientMock);
    }

    public function testSetRequest()
    {
        $method  = 'GET';
        $url     = 'url';
        $headers = [
            'access' => 1
        ];
        $body    = 'myBody';

        $clientMock = $this->getMockBuilder('GuzzleHttp\Client')
        ->disableOriginalConstructor()
        ->getMock();

        $class = new self::$testedClass($clientMock);

        $this->assertInstanceOf('GuzzleHttp\Psr7\Request', $class->setRequest($method, $url, $headers, $body));
    }

    public function testSend()
    {
        $url = 'url';
        $methodHtml = 'GET';
        $body = 'myBody';
        $headers = [
            'access' => 1
        ];
        $options = [
            'sink' => 'link'
        ];

        /** @var Psr\Http\Message\RequestInterface $requestInterfaceMock */
        $requestInterfaceMock = $this->getMockBuilder('Psr\Http\Message\RequestInterface')
        ->disableOriginalConstructor()
        ->setMethods(['getStatusCode'])
        ->getMockForAbstractClass();

        // called two times per function execution
        $requestInterfaceMock->expects($this->exactly(5))
        ->method('getStatusCode')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue(200),
                $this->returnValue(200),
                $this->returnValue(200),
                $this->returnValue(200),
                $this->returnValue(401) // error stops execution here
            )
        );

        /** @var GuzzleHttp\Client $clientMock */
        $clientMock = $this->getMockBuilder('GuzzleHttp\Client')
        ->disableOriginalConstructor()
        ->getMock();

        $clientMock->method('send')
        ->will(
            $this->returnValue($requestInterfaceMock)
        );

        // mock tested class
        // methods getClient(), setRequest(), getResponseBody()
        $testedClassMock = $this->getMockBuilder(self::$testedClass)
        ->setMethods(['setRequest', 'getClient', 'getResponseBody'])
        ->getMock();

        $testedClassMock->method('getClient')
        ->will(
            $this->returnValue($clientMock)
        );

        $testedClassMock->method('setRequest')
        ->with(
            $this->equalTo($methodHtml),
            $this->equalTo($url),
            $this->equalTo($headers),
            $this->equalTo($body)
        )
        ->will(
            $this->returnValue($requestInterfaceMock)
        );

        $testedClassMock->method('getResponseBody')
        ->with(
            $this->equalTo($requestInterfaceMock)
        )
        ->will(
            $this->returnValue('')
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $reflectedClass->newInstanceArgs([$clientMock]);

        $method = $reflectedClass->getMethod('send');

        // with sink
        $method->invoke($testedClassMock, $url, $methodHtml, $body, $headers, $options);

        // without sink
        $method->invoke($testedClassMock, $url, $methodHtml, $body, $headers, []);

        // with getStatusCode return 401 must throw and error
        $this->expectException('\Kunnu\Dropbox\Exceptions\DropboxClientException');

        $method->invoke($testedClassMock, $url, $methodHtml, $body, $headers, []);
    }

    public function testSendB()
    {
        $url = 'url';
        $methodHtml = 'GET';
        $body = 'myBody';
        $headers = [
            'access' => 1
        ];
        $options = [
            'sink' => 'link'
        ];

        /** @var Psr\Http\Message\RequestInterface $requestInterfaceMock */
        $requestInterfaceMock = $this->getMockBuilder('Psr\Http\Message\RequestInterface')
        ->disableOriginalConstructor()
        ->setMethods(['getMessage', 'getCode'])
        ->getMockForAbstractClass();

        $requestInterfaceMock->method('getMessage')
        ->will(
            $this->returnValue('Message')
        );

        $requestInterfaceMock->method('getCode')
        ->will(
            $this->returnValue('11aa22')
        );

        /** @var Psr\Http\Message\RequestInterface $errorMock */
        $errorMock = $this->getMockBuilder('Psr\Http\Message\RequestInterface')
        ->disableOriginalConstructor()
        ->setMethods(['getResponse'])
        ->getMockForAbstractClass();

        $errorMock->method('getResponse')
        ->will(
            $this->returnValue($requestInterfaceMock)
        );

        /** @var GuzzleHttp\Client $errorMock */
        $clientMock = $this->getMockBuilder('GuzzleHttp\Client')
        ->disableOriginalConstructor()
        ->getMock();

        $clientMock->method('send')
        ->with(
            $this->equalTo($requestInterfaceMock),
            $this->equalTo($options)
        )
        ->will(
            $this->throwException(new GuzzleHttp\Exception\RequestException('xx', $errorMock) )
        );

        // mock tested class
        // methods getClient(), setRequest()
        $testedClassMock = $this->getMockBuilder(self::$testedClass)
        ->setMethods(['setRequest', 'getClient', 'getResponseBody'])
        ->getMock();

        // set method setRequest()
        $testedClassMock->method('getClient')
        ->will(
            $this->returnValue($clientMock)
        );

        $testedClassMock->method('setRequest')
        ->with(
            $this->equalTo($methodHtml),
            $this->equalTo($url),
            $this->equalTo($headers),
            $this->equalTo($body)
        )
        ->will(
            $this->returnValue($requestInterfaceMock)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $reflectedClass->newInstanceArgs([$clientMock]);

        $method = $reflectedClass->getMethod('send');

        // with $rawResponse throw error
        $this->expectException(\Kunnu\Dropbox\Exceptions\DropboxClientException::class);

        $method->invoke($testedClassMock, $url, $methodHtml, $body, $headers, $options);
    }

    public function testSendC()
    {
        $url = 'url';
        $methodHtml = 'GET';
        $body = 'myBody';
        $headers = [
            'access' => 1
        ];
        $options = [
            'sink' => 'link'
        ];

        /** @var Psr\Http\Message\ResponseInterface $responseInterfaceMock */
        $responseInterfaceMock = $this->getMockBuilder('\Psr\Http\Message\ResponseInterface')
        ->getMockForAbstractClass();

        /** @var Psr\Http\Message\RequestInterface $requestInterface */
        $requestInterface = $this->getMockBuilder('\Psr\Http\Message\RequestInterface')
        ->setMethods(['getBody'])
        ->getMockForAbstractClass();

        $requestInterface->method('getBody')
        ->with()
        ->will(
            $this->returnValue('')
        );

        /** @var Psr\Http\Message\RequestInterface $errorMock */
        $errorMock = $this->getMockBuilder('\Psr\Http\Message\RequestInterface')
        ->setMethods(['getResponse', 'getCode'])
        ->getMockForAbstractClass();

        $errorMock->method('getResponse')
        ->will(
            $this->returnValue($requestInterface)
        );

        $errorMock->method('getCode')
        ->will(
            $this->returnValue('11aa22')
        );

        /** @var \GuzzleHttp\Client $clientMock */
        $clientMock = $this->getMockBuilder('\GuzzleHttp\Client')
        ->disableOriginalConstructor()
        ->setMethods(['send'])
        ->getMock();

        $clientMock->method('send')
        ->with(
            $this->equalTo($requestInterface),
            $this->equalTo($options)
        )
        ->will(
            $this->throwException(
                new GuzzleHttp\Exception\BadResponseException('xx', $errorMock, $responseInterfaceMock)
            )
        );

        // mock tested class
        // methods getClient(), setRequest()
        $testedClassMock = $this->getMockBuilder(self::$testedClass)
        ->setMethods(['setRequest', 'getClient', 'getResponseBody'])
        ->getMock();

        // set method setRequest()
        $testedClassMock->method('getClient')
        ->will(
            $this->returnValue($clientMock)
        );

        $testedClassMock->method('setRequest')
        ->with(
            $this->equalTo($methodHtml),
            $this->equalTo($url),
            $this->equalTo($headers),
            $this->equalTo($body)
        )
        ->will(
            $this->returnValue($requestInterface)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $reflectedClass->newInstanceArgs([$clientMock]);

        $method = $reflectedClass->getMethod('send');

        // with $rawResponse throw error
        $this->expectException(\Kunnu\Dropbox\Exceptions\DropboxClientException::class);

        $method->invoke($testedClassMock, $url, $methodHtml, $body, $headers, $options);
    }

    public function testGetResponseBody()
    {
        // mock Psr\Http\Message\StreamInterface
        $streamInterfaceMock = $this->getMockBuilder('\Psr\Http\Message\StreamInterface')
        ->setMethods(['getContents'])
        ->getMockForAbstractClass();

        $streamInterfaceMock->method('getContents')
        ->will(
            $this->returnValue('getContents')
        );

        // mock \Psr\Http\Message\ResponseInterface
        // with method getBody
        $requestInterface = $this->getMockBuilder('\Psr\Http\Message\ResponseInterface')
        ->setMethods(['getBody'])
        ->getMockForAbstractClass();

        $requestInterface->method('getBody')
        ->will(
            $this->returnValue('getBody')
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $method = $reflectedClass->getMethod('getResponseBody');
        $method->setAccessible(true);

        $this->assertEquals($method->invoke($instance, $requestInterface), 'getBody');
        $this->assertEquals($method->invoke($instance, $streamInterfaceMock), 'getContents');
    }
}
