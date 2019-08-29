<?php
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Kunnu\Dropbox\Dropbox
 */
class DropboxTest extends TestCase
{
    /** @var string $testedClass name of tested class*/
    private static $testedClass;

    /** @var Kunnu\Dropbox\Dropbox $testedClassLoaded */
    private static $testedClassLoaded;

    /** @var Kunnu\Dropbox\DropboxApp $mockDropboxApp */
    private static $mockDropboxApp;

    protected function setUp()
    {
        self::$mockDropboxApp = $this->getMockBuilder('Kunnu\Dropbox\DropboxApp')
        ->disableOriginalConstructor()
        ->getMock();

        self::$testedClass = 'Kunnu\Dropbox\Dropbox';

        self::$testedClassLoaded = new self::$testedClass(self::$mockDropboxApp);
    }

    /**
     * @covers ::getAuthHelper
     */
    public function testGetAuthHelper()
    {
        $class = self::$testedClassLoaded;

        $this->assertInstanceOf('Kunnu\Dropbox\Authentication\DropboxAuthHelper', $class->getAuthHelper());
    }

    /**
     * @covers ::getOAuth2Client
     */
    public function testGetOAuth2Client()
    {
        $mockDropboxApp = $this->getMockBuilder('Kunnu\Dropbox\DropboxApp')
        ->disableOriginalConstructor()
        ->getMock();

        $mockOAuth2Client = $this->getMockBuilder('Kunnu\Dropbox\Authentication\OAuth2Client')
        ->disableOriginalConstructor()
        ->getMock();

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceArgs([$mockDropboxApp]);

        $method = $reflectedClass->getMethod('getOAuth2Client');

        // oAuth2Client is not declared
        $return = $method->invoke($instance);

        $this->assertInstanceOf('Kunnu\Dropbox\Authentication\OAuth2Client', $return);

        // oAuth2Client is declared
        $property = $reflectedClass->getProperty('oAuth2Client');
        $property->setAccessible(true);
        $property->setValue($instance, $mockOAuth2Client);

        $return = $method->invoke($instance);

        $this->assertInstanceOf('Kunnu\Dropbox\Authentication\OAuth2Client', $return);
    }

    /**
     * @covers ::getApp
     */
    public function testGetApp()
    {
        $class = self::$testedClassLoaded;
        $this->assertSame($class->getApp(), self::$mockDropboxApp);
    }

    /**
     * @covers ::getClient
     */
    public function testGetClient()
    {
        $class = self::$testedClassLoaded;
        $this->assertInstanceOf('\Kunnu\Dropbox\DropboxClient', $class->getClient());
    }

    /**
     * @covers ::getRandomStringGenerator
     */
    public function testGetRandomStringGenerator()
    {
        $class = self::$testedClassLoaded;
        $instanceType = '\Kunnu\Dropbox\Security\RandomStringGeneratorInterface';

        $this->assertInstanceOf($instanceType, $class->getRandomStringGenerator());
    }

    /**
     * @covers ::getPersistentDataStore
     */
    public function testGetPersistentDataStore()
    {
        $class = self::$testedClassLoaded;
        $instanceType = '\Kunnu\Dropbox\Store\PersistentDataStoreInterface';

        $this->assertInstanceOf($instanceType, $class->getPersistentDataStore());
    }

    /**
     * @covers ::getMetadata
     */
    public function testGetMetadata()
    {
        $path = 'path/file';

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'path' => $path
        ];

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/get_metadata'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $mockTestedClass->expects($this->once())
        ->method('makeModelFromResponse')
        ->with(
            $this->equalTo($mockDropboxResponse)
        )
        ->will(
            $this->returnValue('ok')
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('getMetadata');

        $method->invoke($mockTestedClass, $path);


        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Metadata for the root folder is unsupported.');

        $method->invoke($mockTestedClass, '/');
    }

    /**
     * @covers ::postToAPI
     */
    public function testPostToAPI()
    {
        $post = 'POST';
        $api = 'api';

        $endpoint = 'http://xps.net';
        $params = [];
        $accessToken = null;

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass->expects($this->once())
        ->method('sendRequest')
        ->with(
            $this->equalTo($post),
            $this->equalTo($endpoint),
            $this->equalTo($api),
            $this->equalTo($params),
            $this->equalTo($accessToken)
        )
        ->will(
            $this->returnValue('reponse')
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('postToAPI');

        $method->invoke($mockTestedClass, $endpoint, $params, $accessToken);
    }

    /**
     * @covers ::sendRequest
     */
    public function testSendRequest()
    {
        $method = 'POST';
        $endpoint = 'XPI';
        $endpointType = 'api';
        $params = [];
        $accessToken = null;
        $responseFile = null;

        $accessTokenReturned = '123abcxyz';


        $mockDropboxClient = $this->getMockBuilder('\Kunnu\Dropbox\DropboxClient')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxClient->expects($this->once())
        ->method('sendRequest')
        ->will(
            $this->returnValue('uuu')
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass->expects($this->exactly(2))
        ->method('getAccessToken')
        ->will(
            $this->returnValue($accessTokenReturned)
        );

        $mockTestedClass->expects($this->once())
        ->method('getClient')
        ->will(
            $this->returnValue($mockDropboxClient)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('sendRequest');

        $return = $method->invoke($mockTestedClass, $method, $endpoint, $endpointType, $params, $accessToken, $responseFile);
    }

    /**
     * @covers ::getAccessToken
     */
    public function testGetAccessToken()
    {
        $class = self::$testedClassLoaded;

        $return = $class->getAccessToken();

        $this->assertNull($return);
    }

    /**
     * @covers ::setAccessToken
     */
    public function testSetAccessToken()
    {
        $class = self::$testedClassLoaded;

        $return = $class->setAccessToken('123abc');

        $this->assertInstanceOf('Kunnu\Dropbox\Dropbox', $return);
    }

    public function testMakeModelFromResponse()
    {
        $mockResponse = $this->getMockBuilder('Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockResponse->expects($this->once())
        ->method('getDecodedBody')
        ->will(
            $this->returnValue(null)
        );

        $class = self::$testedClassLoaded;

        $return = $class->makeModelFromResponse($mockResponse);

        $this->assertInstanceOf('\Kunnu\Dropbox\Models\ModelInterface', $return);
    }

    /**
     * TODO: update documentation
     * DeletedMetadata != MetadataCollection
     */
    public function testListFolder($path = null, array $params = [])
    {
        $path = '/';
        $params = [];

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled['path'] = '';

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/list_folder'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $mockTestedClass->expects($this->once())
        ->method('makeModelFromResponse')
        ->with(
            $this->equalTo($mockDropboxResponse)
        )
        ->will(
            $this->returnValue('ok')
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('listFolder');

        $method->invoke($mockTestedClass, $path, $params);
    }

    public function testListFolderContinue()
    {

        $cursor = 'xxxx';

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'cursor' => $cursor
        ];

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/list_folder/continue'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $mockTestedClass->expects($this->once())
        ->method('makeModelFromResponse')
        ->with(
            $this->equalTo($mockDropboxResponse)
        )
        ->will(
            $this->returnValue('ok')
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('listFolderContinue');

        $method->invoke($mockTestedClass, $cursor);
    }

    public function testListFolderLatestCursor()
    {
        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $body['cursor'] = 'x';
        $body1['notcursor'] = 'y';

        $mockDropboxResponse->expects($this->exactly(2))
        ->method('getDecodedBody')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue($body),
                $this->returnValue($body1)
            )
        );

        $path = '/';

        $params['path'] = '';

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass->expects($this->exactly(2))
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/list_folder/get_latest_cursor'),
            $this->equalTo($params)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        // reflect class
        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('listFolderLatestCursor');

        $return = $method->invoke($mockTestedClass, $path);

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Could not retrieve cursor. Something went wrong.');

        $method->invoke($mockTestedClass, $path);
    }

    public function testListRevisions()
    {
        $path = '/xyz';
        $params = [];
        $body['entries'] = [[]];

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxResponse->expects($this->once())
        ->method('getDecodedBody')
        ->will(
            $this->returnValue($body)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled['path'] = $path;

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/list_revisions'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('listRevisions');

        $method->invoke($mockTestedClass, $path, $params);
    }

    public function testSearch()
    {
        $path = '/';
        $params = [];
        $query = 'ssss';

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled['path'] = '';
        $paramsCalled['query'] = $query;

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/search'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $mockTestedClass->expects($this->once())
        ->method('makeModelFromResponse')
        ->with(
            $this->equalTo($mockDropboxResponse)
        )
        ->will(
            $this->returnValue('ok')
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('search');

        $method->invoke($mockTestedClass, $path, $query, $params);
    }

    public function testCreateFolder()
    {
        $path = '/xyz';
        $body = [];
        $autorename = false;

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxResponse->expects($this->once())
        ->method('getDecodedBody')
        ->will(
            $this->returnValue($body)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'path' => $path,
            'autorename' => $autorename
        ];

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/create_folder'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('createFolder');

        $method->invoke($mockTestedClass, $path, $autorename);


        // on $path is null
        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Path cannot be null.');

        $method->invoke($mockTestedClass, null, $autorename);
    }

    public function testDeleteA()
    {
        $class = self::$testedClassLoaded;

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Path cannot be null.');

        $class->delete(null);
    }

    public function testDeleteB()
    {
        $path = '/xyz';
        $body['metadata'] = [];

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        // first call returns expected Metadata
        // second one returns null
        // that doesn't match either side of the condtions
        $mockDropboxResponse->expects($this->exactly(2))
        ->method('getDecodedBody')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue($body),
                $this->returnValue(null)
            )
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'path' => $path
        ];

        $mockTestedClass->expects($this->exactly(2))
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/delete_v2'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        // metadata match expectations
        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('delete');

        $method->invoke($mockTestedClass, $path);

        // metadata doesn't match expectations
        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('delete');

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Invalid Response.');

        $method->invoke($mockTestedClass, $path);
    }

    public function testMove()
    {
        $fromPath = '/xyz';
        $toPath = '/jdg';

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'from_path' => $fromPath,
            'to_path' => $toPath
        ];

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/move'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $mockTestedClass->expects($this->once())
        ->method('makeModelFromResponse')
        ->with(
            $this->equalTo($mockDropboxResponse)
        )
        ->will(
            $this->returnValue('ok')
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('move');

        $method->invoke($mockTestedClass, $fromPath, $toPath);

        // either or both paths are null
        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('From and To paths cannot be null.');

        $method->invoke($mockTestedClass, null, $toPath);
    }

    public function testCopy()
    {
        $fromPath = '/xyz';
        $toPath = '/jdg';

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'from_path' => $fromPath,
            'to_path' => $toPath
        ];

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/copy'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $mockTestedClass->expects($this->once())
        ->method('makeModelFromResponse')
        ->with(
            $this->equalTo($mockDropboxResponse)
        )
        ->will(
            $this->returnValue('ok')
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('copy');

        $method->invoke($mockTestedClass, $fromPath, $toPath);

        // either or both paths are null
        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('From and To paths cannot be null.');

        $method->invoke($mockTestedClass, null, $toPath);
    }

    public function testRestore()
    {
        list($path, $rev) = [
            '/xi',
            '333'
        ];

        $body = [];

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxResponse->expects($this->once())
        ->method('getDecodedBody')
        ->will(
            $this->returnValue($body)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'path' => $path,
            'rev' => $rev
        ];

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/restore'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('restore');

        $method->invoke($mockTestedClass, $path, $rev);

        // either or both paths are null
        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Path and Revision cannot be null.');

        $method->invoke($mockTestedClass, null, $rev);
    }

    public function testGetCopyReference()
    {
        $path = '/xi';
        $body = [];

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxResponse->expects($this->once())
        ->method('getDecodedBody')
        ->will(
            $this->returnValue($body)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = ['path' => $path];

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/copy_reference/get'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('getCopyReference');

        $method->invoke($mockTestedClass, $path);

        // either or both paths are null
        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Path cannot be null.');

        $method->invoke($mockTestedClass, null);
    }

    public function testSaveCopyReferenceA()
    {
        $path = '/xi';
        $copyReference = '123hyi';
        $body['metadata'] = [];

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxResponse->expects($this->exactly(2))
        ->method('getDecodedBody')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue($body),
                $this->returnValue([])
            )
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'path' => $path,
            'copy_reference' => $copyReference
        ];

        $mockTestedClass->expects($this->exactly(2))
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/copy_reference/save'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('saveCopyReference');

        $method->invoke($mockTestedClass, $path, $copyReference);

        // either or both paths are null
        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Invalid Response.');

        $method->invoke($mockTestedClass, $path, $copyReference);
    }

    public function testSaveCopyReferenceB()
    {
        $class = self::$testedClassLoaded;

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Path and Copy Reference cannot be null.');

        $class->saveCopyReference(null, null);
    }

    public function testGetTemporaryLink()
    {
        $path = '/xi';

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'path' => $path
        ];

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/get_temporary_link'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('getTemporaryLink');

        $method->invoke($mockTestedClass, $path);

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Path cannot be null.');

        $method->invoke($mockTestedClass, null);
    }

    public function testSaveUrlA()
    {
        $path = '/loprem';
        $url = 'https://url.com';
        $body['async_job_id'] = 'sync';

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxResponse->expects($this->exactly(2))
        ->method('getDecodedBody')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue($body),
                $this->returnValue([])
            )
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'path' => $path,
            'url' => $url
        ];

        $mockTestedClass->expects($this->exactly(2))
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/save_url'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('saveUrl');

        $method->invoke($mockTestedClass, $path, $url);

        // either or both paths are null
        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Could not retrieve Async Job ID.');

        $method->invoke($mockTestedClass, $path, $url);
    }

    public function testSaveUrlB()
    {
        $class = self::$testedClassLoaded;

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Path and URL cannot be null.');

        $class->saveUrl(null, null);
    }

    public function testCheckJobStatus()
    {
        $asyncJobId = 'jik';
        $body['.tag'] = 'complete';

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxResponse->expects($this->exactly(2))
        ->method('getDecodedBody')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue($body),
                $this->returnValue([])
            )
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'async_job_id' => $asyncJobId
        ];

        $mockTestedClass->expects($this->exactly(2))
        ->method('postToAPI')
        ->with(
            $this->equalTo('/files/save_url/check_job_status'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('checkJobStatus');

        $method->invoke($mockTestedClass, $asyncJobId);

        $method->invoke($mockTestedClass, $asyncJobId);

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Async Job ID cannot be null.');

        $method->invoke($mockTestedClass, null);
    }

    public function testUpload()
    {
        $path = 'xy';
        $params = [];
        $autoChunkedUploadThreshold = 9000000;

        $mockDropboxFile = $this->getMockBuilder('\Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxFile->expects($this->exactly(2))
        ->method('getSize')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue($autoChunkedUploadThreshold),
                $this->returnValue(7000000)
            )
        );

        $dropboxFile = $mockDropboxFile;

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass->expects($this->exactly(2))
        ->method('makeDropboxFile')
        ->with(
            $this->equalTo($mockDropboxFile)
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

        // mock method uploadChunked
        $mockTestedClass->expects($this->once())
        ->method('uploadChunked')
        ->with(
            $this->equalTo($dropboxFile),
            $this->equalTo($path),
            $this->equalTo(null),
            $this->equalTo(null),
            $this->equalTo($params)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('upload');

        $method->invoke($mockTestedClass, $dropboxFile, $path, $params);

        $method->invoke($mockTestedClass, $dropboxFile, $path, $params);
    }

    public function testMakeDropboxFile()
    {
        $offset = 30;
        $maxLength = 800;
        $modeRead = 'r';
        $modeWrite = 'w';
        $dropboxFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'DropboxResponseToFileTest.php';

        $mockDropboxFile = $this->getMockBuilder('\Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        // getMode will return a different mode
        $mockDropboxFile->expects($this->exactly(2))
        ->method('getMode')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue($modeRead),
                $this->returnValue($modeRead)
            )
        );

        $mockDropboxFile->expects($this->once())
        ->method('setOffset')
        ->with(
            $this->equalTo($offset)
        );

        $mockDropboxFile->expects($this->once())
        ->method('setMaxLength')
        ->with(
            $this->equalTo($maxLength)
        );

        $dropboxFile = $mockDropboxFile;

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceArgs([self::$mockDropboxApp]);

        $method = $reflectedClass->getMethod('makeDropboxFile');

        // is instance from \Kunnu\Dropbox\DropboxFile
        // has same mode
        $method->invoke($instance, $dropboxFile, $maxLength, $offset, $modeRead);

        // is instance from \Kunnu\Dropbox\DropboxFile
        // has different mode
        $method->invoke($instance, $dropboxFile, $maxLength, $offset, $modeWrite);

        // It's only a file path
        $method->invoke($instance, $dropboxFilePath, $maxLength, $offset, $modeWrite);
    }

    public function testUploadChunkedA()
    {
        $fileSizeReturned = 3000000;
        $filePathReturned = 'xxyy/uuoo';

        // chunkSize returned after first condition
        // is_null($chunkSize)
        $chunkSizeReturned = 4000000;
        // second condition
        // $fileSize <= $chunkSize
        $chunkSizeReturnedB = intval($fileSizeReturned/2);

        $uploadedInFunction = $chunkSizeReturnedB;

        $chunkSize = 4000000;
        $sessionId = '123';

        $path = 'xxyy/uuoo';
        $fileSize = null;
        $chunkSize = null;
        $params = [];

        $mockDropboxFile = $this->getMockBuilder('\Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxFile->expects($this->once())
        ->method('getSize')
        ->will(
            $this->returnValue($fileSizeReturned)
        );

        $mockDropboxFile->expects($this->once())
        ->method('getFilePath')
        ->will(
            $this->returnValue($filePathReturned)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass->expects($this->once())
        ->method('makeDropboxFile')
        ->with(
            $this->equalTo($mockDropboxFile)
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

         // mock method uploadChunked
         $mockTestedClass->expects($this->once())
         ->method('startUploadSession')
         ->with(
             $this->equalTo($filePathReturned),
             $this->equalTo($chunkSizeReturnedB)
         )
         ->will(
             $this->returnValue($sessionId)
         );

         $mockTestedClass->expects($this->once())
         ->method('finishUploadSession')
         ->with(
             $this->equalTo($mockDropboxFile),
             $this->equalTo($sessionId),
             $this->equalTo($uploadedInFunction),
             $this->equalTo(1500000),
             $this->equalTo($path),
             $this->equalTo($params)
         )
         ->will(
             $this->returnValue('iii')
         );

         $reflectedClass = new \ReflectionClass(self::$testedClass);

         $method = $reflectedClass->getMethod('uploadChunked');

         $method->invoke($mockTestedClass, $mockDropboxFile, $path, $fileSize, $chunkSize, $params);
    }

    public function testUploadChunkedB()
    {
        $filePathReturned = 'xxyy/uuoo';

        // chunkSize returned after first condition
        // is_null($chunkSize)
        $chunkSizeReturned = 4000000;

        $uploadedInFunction = $chunkSizeReturned;

        $sessionId = '123';

        $path = 'xxyy/uuoo';
        $fileSize = 10000000;
        $chunkSize = 4000000;
        $params = [];

        $mockDropboxFile = $this->getMockBuilder('\Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxFile->expects($this->once())
        ->method('getFilePath')
        ->will(
            $this->returnValue($filePathReturned)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass->expects($this->once())
        ->method('makeDropboxFile')
        ->with(
            $this->equalTo($mockDropboxFile)
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

         // mock method uploadChunked
         $mockTestedClass->expects($this->once())
         ->method('startUploadSession')
         ->with(
             $this->equalTo($filePathReturned),
             $this->equalTo($chunkSizeReturned)
         )
         ->will(
             $this->returnValue($sessionId)
         );

         $mockTestedClass->expects($this->once())
         ->method('appendUploadSession')
         ->with(
             $this->equalTo($mockDropboxFile),
             $this->equalTo($sessionId),
             $this->equalTo($uploadedInFunction),
             $this->equalTo($chunkSizeReturned)
         )
         ->will(
             $this->returnValue($sessionId)
         );

         $mockTestedClass->expects($this->once())
         ->method('finishUploadSession')
         ->with(
             $this->equalTo($mockDropboxFile),
             $this->equalTo($sessionId),
             $this->equalTo($chunkSizeReturned + $chunkSizeReturned),
             $this->equalTo($fileSize - 2 * $chunkSizeReturned),
             $this->equalTo($path),
             $this->equalTo($params)
         )
         ->will(
             $this->returnValue('iii')
         );

         $reflectedClass = new \ReflectionClass(self::$testedClass);

         $method = $reflectedClass->getMethod('uploadChunked');

         $method->invoke($mockTestedClass, $mockDropboxFile, $path, $fileSize, $chunkSize, $params);
    }

    public function testStartUploadSession()
    {
        $chunkSize = -1;
        $close = false;

        $mockDropboxFile = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $body['session_id'] = 'x';

        $mockDropboxFile->expects($this->exactly(2))
        ->method('getDecodedBody')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue($body),
                $this->returnValue([])
            )
        );

        $params = [
            'close' => $close ? true : false,
            'file' => $mockDropboxFile
        ];

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass->expects($this->exactly(2))
        ->method('makeDropboxFile')
        ->with(
            $this->equalTo($mockDropboxFile),
            $this->equalTo($chunkSize)
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

        $mockTestedClass->expects($this->exactly(2))
        ->method('postToContent')
        ->with(
            $this->equalTo('/files/upload_session/start'),
            $this->equalTo($params)
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

        // reflect class
        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('startUploadSession');

        $return = $method->invoke($mockTestedClass, $mockDropboxFile, $chunkSize, $close);

        $this->assertSame($body['session_id'], $return);

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Could not retrieve Session ID.');

        $method->invoke($mockTestedClass, $mockDropboxFile, $chunkSize, $close);
    }

    public function testPostToContent()
    {
        $post = 'POST';
        $endpoint = 'abc';
        $params = [];
        $accessToken = null;
        $responseFile = null;

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass->expects($this->once())
        ->method('sendRequest')
        ->with(
            $this->equalTo($post),
            $this->equalTo($endpoint),
            $this->equalTo('content'),
            $this->equalTo($params),
            $this->equalTo($accessToken),
            $this->equalTo($responseFile)
        )
        ->will(
            $this->returnValue('reponse')
        );

        // reflect class
        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('postToContent');

        $return = $method->invoke($mockTestedClass, $endpoint, $params, $accessToken, $responseFile);
    }

    public function testAppendUploadSession()
    {
        $sessionId = '156k';
        $offset = 32;
        $chunkSize = 4000000;
        $close = false;


        $mockDropboxFile = $this->getMockBuilder('\Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass->expects($this->exactly(2))
        ->method('makeDropboxFile')
        ->with(
            $this->equalTo($mockDropboxFile)
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

        $params['file'] = $mockDropboxFile;
        $params['cursor'] = [
            'session_id' => $sessionId,
            'offset' => $offset
        ];
        $params['close'] = $close ? true : false;
        $params['validateResponse'] = false;

        $mockTestedClass->expects($this->once())
        ->method('postToContent')
        ->with(
            $this->equalTo('/files/upload_session/append_v2'),
            $this->equalTo($params)
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('appendUploadSession');

        $method->invoke($mockTestedClass, $mockDropboxFile, $sessionId, $offset, $chunkSize, $close);

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Session ID, offset and chunk size cannot be null');

        $method->invoke($mockTestedClass, $mockDropboxFile, null, $offset, $chunkSize, $close);
    }

    public function testFinishUploadSession()
    {
        $sessionId = '123AA';
        $offset = 500;
        $remaining = 630;
        $path = '123/aaa/bb';
        $params = [];

        $body = [];

        $mockDropboxFile = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxFile->expects($this->once())
        ->method('getDecodedBody')
        ->will(
            $this->returnValue($body)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $queryParams['file'] = $mockDropboxFile;
        $queryParams['cursor'] = [
            'session_id' => $sessionId,
            'offset' => $offset
        ];

        $params['path'] = $path;

        $queryParams['commit'] = $params;

        $mockTestedClass->expects($this->once())
        ->method('postToContent')
        ->with(
            $this->equalTo('/files/upload_session/finish'),
            $this->equalTo($queryParams)
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

        $mockTestedClass->expects($this->exactly(2))
        ->method('makeDropboxFile')
        ->with(
            $this->equalTo($mockDropboxFile),
            $this->equalTo($remaining),
            $this->equalTo($offset)
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('finishUploadSession');

        $method->invoke($mockTestedClass, $mockDropboxFile, $sessionId, $offset, $remaining, $path, $params);

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Session ID, offset, remaining and path cannot be null');

        $method->invoke($mockTestedClass, $mockDropboxFile, null, $offset, $remaining, $path, $params);
    }

    public function testsimpleUpload()
    {
        $path = '123/aaa/bb';
        $params = [];

        $body = [];

        $mockDropboxFile = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxFile->expects($this->once())
        ->method('getDecodedBody')
        ->will(
            $this->returnValue($body)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $queryParams['path'] = $path;
        $queryParams['file'] = $mockDropboxFile;

        $mockTestedClass->expects($this->once())
        ->method('postToContent')
        ->with(
            $this->equalTo('/files/upload'),
            $this->equalTo($queryParams)
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

        $mockTestedClass->expects($this->once())
        ->method('makeDropboxFile')
        ->with(
            $this->equalTo($mockDropboxFile)
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('simpleUpload');

        $method->invoke($mockTestedClass, $mockDropboxFile, $path, $params);
    }

    public function testGetThumbnailA()
    {
        $path = 'abc/123/efg';
        $size = 'small';
        $format = 'jpeg';

        $body = [];
        $sizeReturned = 15;

        $mockDropboxFile = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxFile->expects($this->once())
        ->method('getBody')
        ->will(
            $this->returnValue($body)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->setMethods(['getThumbnailSize', 'postToContent', 'getMetadataFromResponseHeaders'])
        ->getMock();

        $queryParams = [
            'path' => $path,
            'format' => $format,
            'size' => $sizeReturned
        ];

        $mockTestedClass->expects($this->once())
        ->method('postToContent')
        ->with(
            $this->equalTo('/files/get_thumbnail'),
            $this->equalTo($queryParams)
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

        $mockTestedClass->expects($this->once())
        ->method('getThumbnailSize')
        ->with(
            $this->equalTo($size)
        )
        ->will(
            $this->returnValue($sizeReturned)
        );

        $mockTestedClass->expects($this->once())
        ->method('getMetadataFromResponseHeaders')
        ->with(
            $this->equalTo($mockDropboxFile)
        )
        ->will(
            $this->returnValue([])
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('getThumbnail');

        $method->invoke($mockTestedClass, $path, $size, $format);

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Path cannot be null.');

        $method->invoke($mockTestedClass, null, $size, $format);
    }

    public function testGetThumbnailB()
    {
        $path = 'abc/123/efg';
        $size = 'small';
        $format = 'bmp';

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Invalid format. Must either be \'jpeg\' or \'png\'.');

        $class = self::$testedClassLoaded;
        $class->getThumbnail($path, $size, $format);
    }

    public function testGetThumbnailSize()
    {
        $size = 'tooBigToShowIt';

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([self::$mockDropboxApp]);

        $method = $reflectedClass->getMethod('getThumbnailSize');
        $method->setAccessible(true);

        $return = $method->invoke($instance, $size);

        $this->assertSame($return, 'w64h64');
    }

    public function testGetMetadataFromResponseHeaders()
    {
        $headers['dropbox-api-result'] = [json_encode([])];

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxResponse->expects($this->once())
        ->method('getHeaders')
        ->will(
            $this->returnValue($headers)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([self::$mockDropboxApp]);

        $method = $reflectedClass->getMethod('getMetadataFromResponseHeaders');
        $method->setAccessible(true);

        $return = $method->invoke($instance, $mockDropboxResponse);
    }

    public function testDownload()
    {
        $path = 'path/file';

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxFile = $this->getMockBuilder('\Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->setMethods(['postToContent', 'getMetadataFromResponseHeaders', 'makeDropboxFile'])
        ->getMock();

        $queryParams = [
            'path' => $path
        ];

        $mockTestedClass->expects($this->once())
        ->method('postToContent')
        ->with(
            $this->equalTo('/files/download'),
            $this->equalTo($queryParams),
            $this->equalTo(null),
            $this->equalTo($mockDropboxFile)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );

        $mockTestedClass->expects($this->once())
        ->method('getMetadataFromResponseHeaders')
        ->with(
            $this->equalTo($mockDropboxResponse)
        )
        ->will(
            $this->returnValue([])
        );

        $mockTestedClass->expects($this->exactly(2))
        ->method('makeDropboxFile')
        ->with(
            $this->logicalOr(
                [
                    $this->equalTo($mockDropboxFile),
                    $this->equalTo(null),
                    $this->equalTo(null),
                    $this->equalTo('w')
                ],
                    $this->equalTo($mockDropboxFile)
            )
        )
        ->will(
            $this->returnValue($mockDropboxFile)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([self::$mockDropboxApp]);

        $method = $reflectedClass->getMethod('download');

        $return = $method->invoke($mockTestedClass, $path, $mockDropboxFile);

        $this->expectException('Kunnu\Dropbox\Exceptions\DropboxClientException');
        $this->expectExceptionMessage('Path cannot be null.');

        $return = $method->invoke($mockTestedClass, null, $mockDropboxFile);
    }

    public function testGetCurrentAccount()
    {
        // empty array
        $decodedBody = [];

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxResponse->expects($this->once())
        ->method('getDecodedBody')
        ->will(
            $this->returnValue($decodedBody)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [];

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/users/get_current_account'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );


        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([self::$mockDropboxApp]);

        $method = $reflectedClass->getMethod('getCurrentAccount');
        $return = $method->invoke($mockTestedClass);

        $this->assertInstanceOf('Kunnu\Dropbox\Models\Account', $return);
    }

    public function testGetAccount()
    {
        // empty array
        $decodedBody = [];

        $account_id = '22OP';

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxResponse->expects($this->once())
        ->method('getDecodedBody')
        ->will(
            $this->returnValue($decodedBody)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'account_id' => $account_id
        ];

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/users/get_account'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );


        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([self::$mockDropboxApp]);

        $method = $reflectedClass->getMethod('getAccount');
        $return = $method->invoke($mockTestedClass, $account_id);

        $this->assertInstanceOf('Kunnu\Dropbox\Models\Account', $return);
    }

    public function testGetAccounts()
    {
        // empty array
        $decodedBody = [];

        $account_ids = [];

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxResponse->expects($this->once())
        ->method('getDecodedBody')
        ->will(
            $this->returnValue($decodedBody)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [
            'account_ids' => $account_ids
        ];

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/users/get_account_batch'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );


        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([self::$mockDropboxApp]);

        $method = $reflectedClass->getMethod('getAccounts');
        $return = $method->invoke($mockTestedClass, $account_ids);

        $this->assertInstanceOf('Kunnu\Dropbox\Models\AccountList', $return);
    }

    public function testGetSpaceUsage()
    {
        // empty array
        $decodedBody = [];

        $mockDropboxResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->getMock();

        $mockDropboxResponse->expects($this->once())
        ->method('getDecodedBody')
        ->will(
            $this->returnValue($decodedBody)
        );

        $mockTestedClass = $this->getMockBuilder('Kunnu\Dropbox\Dropbox')
        ->disableOriginalConstructor()
        ->getMock();

        $paramsCalled = [];

        $mockTestedClass->expects($this->once())
        ->method('postToAPI')
        ->with(
            $this->equalTo('/users/get_space_usage'),
            $this->equalTo($paramsCalled)
        )
        ->will(
            $this->returnValue($mockDropboxResponse)
        );


        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([self::$mockDropboxApp]);

        $method = $reflectedClass->getMethod('getSpaceUsage');
        $return = $method->invoke($mockTestedClass);

        $this->assertSame([], $return);
    }
}
