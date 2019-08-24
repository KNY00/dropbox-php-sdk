<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class SearchResultsTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\Models\SearchResults';
    }

    public function testProcessItems()
    {
        $data = new Test\Models\TestHelpers\DataProvider();

        $getSearchResults = $data->getSearchResults()['matches'];

        $processedItems = [];

        foreach ($getSearchResults as $entry) {
            if (isset($entry['metadata']) && is_array($entry['metadata'])) {
                $processedItems[] = new Kunnu\Dropbox\Models\SearchResult($entry);
            }
        }

        $functionPush = new Kunnu\Dropbox\Models\ModelCollection($processedItems);

        $reflectedClass = new ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceArgs([$getSearchResults]);

        $processItems = $reflectedClass->getMethod('processItems');
        $processItems->setAccessible(true);
        $output = $processItems->invoke($instance, $getSearchResults);

        $property = $reflectedClass->getProperty('items');
        $property->setAccessible(true);

        $this->assertEquals($property->getValue($instance), $functionPush);
    }
}
