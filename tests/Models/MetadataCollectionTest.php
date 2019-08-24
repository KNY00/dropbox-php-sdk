<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

/**
 * TODO: rework tests
 */
class MetadataCollectionTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var Kunnu\Dropbox\Models\MetadataCollection $testedClassLoaded */
    protected static $testedClassLoaded;

    /** @var array $data */
    protected static $data;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\Models\MetadataCollection';

        $class = new  Test\Models\TestHelpers\DataProvider;

        self::$data = $class->getMetadataCollection();

        self::$testedClassLoaded = new self::$testedClass (self::$data);
    }

    /**
     * Get value of protected or private property
     *
     * @param  string $propertyName
     *
     * @return string|boolean|array
     */
    public function accessProtectedProperties($propertyName)
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([self::$data]);

        $reflectionProperty = $reflectedClass->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($instance);
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        $this->assertNull($constructor->invoke($instance,  self::$data));
    }

    public function testGetCollectionItemsKey()
    {
        $this->assertEquals(self::$testedClassLoaded->getCollectionItemsKey(), 'entries');
    }

    public function testGetCollectionHasMoreItemsKey()
    {
        $this->assertEquals(self::$testedClassLoaded->getCollectionHasMoreItemsKey(), 'has_more');
    }

    public function testGetCollectionCursorKey()
    {
        $this->assertEquals(self::$testedClassLoaded->getCollectionCursorKey(), 'cursor');
    }

    public function testGetItems()
    {
        $this->assertEquals(self::$testedClassLoaded->getItems(), $this->accessProtectedProperties('items'));
    }

    public function testGetCursor()
    {
        $this->assertEquals(self::$testedClassLoaded->getCursor(), $this->accessProtectedProperties('cursor'));
    }


    public function testHasMoreItems()
    {
        $this->assertEquals(self::$testedClassLoaded->hasMoreItems(), $this->accessProtectedProperties('hasMoreItems'));
    }


}
