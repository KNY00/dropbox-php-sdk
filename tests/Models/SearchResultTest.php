<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class SearchResultTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var Kunnu\Dropbox\Models\SearchResult $testedClassLoaded */
    protected static $testedClassLoaded;

    /** @var array $data */
    protected static $data;

    protected function setUp()
    {
        $data = new Test\Models\TestHelpers\DataProvider();

        $getSearchResult = $data->getSearchResult();

        self::$data = $getSearchResult;

        self::$testedClass = 'Kunnu\Dropbox\Models\SearchResult';

        self::$testedClassLoaded = new self::$testedClass(self::$data);
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
        $data = new Test\Models\TestHelpers\DataProvider();

        $getSearchResult = $data->getSearchResult();

        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        $cst = $constructor->invoke($instance,  $getSearchResult);

        $this->assertNull($cst);
    }


    public function testGetMatchType()
    {
        $propertyName = 'matchType';

        $matchType = $this->accessProtectedProperties($propertyName);

        $this->assertEquals(self::$testedClassLoaded->getMatchType(), $matchType);
    }


    public function testGetMetadata()
    {
        $propertyName = 'metadata';

        $metadata = $this->accessProtectedProperties($propertyName);

        $this->assertEquals(self::$testedClassLoaded->getMetadata(), $metadata);
    }
}
