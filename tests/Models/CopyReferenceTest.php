<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class CopyReferenceTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var array $data */
    protected static $data;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\Models\CopyReference';

        $class = new Test\Models\TestHelpers\DataProvider;

        self::$data = $class->getDataFile();
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        // instantiate class with $data array
        $cst = $constructor->invoke($instance, self::$data);

        $this->assertNull($cst);
    }

    public function testGetExpirationDate()
    {
        $class = new self::$testedClass(self::$data);

        $this->assertEquals($class->getExpirationDate(), new DateTime(self::$data['expires']) );
    }

    public function testGetMetadata()
    {
        $modelFactory = new Kunnu\Dropbox\Models\ModelFactory;

        $class = new self::$testedClass(self::$data);

        $this->assertEquals(
            $class->getMetadata(),
            $modelFactory::make(self::$data['metadata'])
        );
    }

    public function testGetReference()
    {
        $modelFactory = new Kunnu\Dropbox\Models\ModelFactory;

        $class = new self::$testedClass(self::$data);
        $this->assertEquals(
            $class->getReference(),
            self::$data['copy_reference']
        );
    }
}
