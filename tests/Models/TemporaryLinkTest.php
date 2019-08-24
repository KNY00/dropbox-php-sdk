<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class TemporaryLinkTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var Kunnu\Dropbox\Models\TemporaryLink $testedClassLoaded */
    protected static $testedClassLoaded;

    /** @var array $data */
    protected static $data;

    protected function setUp()
    {
        $data = new Test\Models\TestHelpers\DataProvider();

        $getTemporaryLink = $data->getTemporaryLink();

        self::$data = $getTemporaryLink;

        self::$testedClass = 'Kunnu\Dropbox\Models\TemporaryLink';

        self::$testedClassLoaded = new self::$testedClass(self::$data);
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        $cst = $constructor->invoke($instance,  self::$data);

        $this->assertNull($cst);
    }

    public function testGetMetadata()
    {
        $this->assertEquals(self::$testedClassLoaded->getMetadata(), new kunnu\Dropbox\Models\FileMetadata(self::$data['metadata']));
    }

    public function testGetLink()
    {
        $this->assertEquals(self::$testedClassLoaded->getLink(), self::$data['link']);
    }
}
