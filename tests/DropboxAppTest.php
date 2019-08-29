<?php
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Kunnu\Dropbox\DropboxApp
 */
class DropboxAppTest extends TestCase
{
    /** @var string $testedClass */
    private static $testedClass;

    /** @var string $testedClassLoaded */
    private static $testedClassLoaded;

    /** @var string $clientId */
    protected static $clientId;

    /** @var string $clientSecret */
    protected static $clientSecret;

    /** @var string $accessToken */

    protected static $accessToken;


    protected function setUp()
    {
        self::$clientId = '123';

        self::$clientSecret = 'abc';

        self::$accessToken = 'xyz564';

        self::$testedClass = 'Kunnu\Dropbox\DropboxApp';

        self::$testedClassLoaded = new self::$testedClass(self::$clientId, self::$clientSecret, self::$accessToken);
    }

    public function testGetClientId()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame(self::$clientId, $class->getClientId());
    }

    public function testGetSecret()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame(self::$clientSecret, $class->getClientSecret());
    }

    public function testGetAccessToken()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame(self::$accessToken, $class->getAccessToken());
    }
}
