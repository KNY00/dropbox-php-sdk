<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/DataProvider.php';

class ModelFactoryTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var Kunnu\Dropbox\Models\ModelFactory $testedClassLoaded */
    protected static $testedClassLoaded;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\Models\ModelFactory';

        self::$testedClassLoaded = new self::$testedClass;
    }

    private function accessProtectedMethod($methodName, $data)
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([]);

        $reflectionMethod = $reflectedClass->getMethod($methodName);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invoke($instance, $data);
    }

    public function testMake()
    {
        $namespace = 'Kunnu\Dropbox\Models\\';

        $data = new Test\Models\TestHelpers\DataProvider();

        $metadataFile = $data->getMetadataFile();
        $dataFolder = $data->getDataFolder();
        $temporaryLink = $data->getTemporaryLink();
        $metadataCollection = $data->getMetadataCollection();
        $searchResults = $data->getSearchResults();
        $getMetadataDeltedFile = $data->getMetadataDeltedFile();

        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([]);

        $constructor = $reflectedClass->getMethod('make');

        // instantiate with file
        $cst = $constructor->invoke($instance,  $metadataFile);

        $this->assertInstanceOf($namespace . 'FileMetadata', $cst);

        // instantiate with folder
        $cst = $constructor->invoke($instance,  $dataFolder);

        $this->assertInstanceOf($namespace . 'FolderMetadata', $cst);

        // instantiate with TemporaryLink
        $cst = $constructor->invoke($instance,  $temporaryLink);

        $this->assertInstanceOf($namespace . 'TemporaryLink', $cst);

        // instantiate with metadataCollection
        $cst = $constructor->invoke($instance,  $metadataCollection);

        $this->assertInstanceOf($namespace . 'MetadataCollection', $cst);

        // instantiate with search Results
        $cst = $constructor->invoke($instance,  $searchResults);

        $this->assertInstanceOf($namespace . 'SearchResults', $cst);

        // instantiate with deleted file
        $cst = $constructor->invoke($instance,  $getMetadataDeltedFile);

        $this->assertInstanceOf($namespace . 'DeletedMetadata', $cst);

        // instantiate with custom array
        // .tag key must not return file or folder
        // so that it won't catch any previous condition
        $dataX['.tag'] = 'anything';
        $dataX['id'] = 'id:123';
        $cst = $constructor->invoke($instance,  $dataX);

        $this->assertInstanceOf($namespace . 'BaseModel', $cst);
    }

    public function testIsFileOrFolder()
    {
        $data['id'] = 'x';
        $data['.tag'] = 'y';

        $this->assertTrue($this->accessProtectedMethod('isFileOrFolder', $data));
    }

    public function testIsFile()
    {
        $this->assertTrue($this->accessProtectedMethod('isFile', 'file'));
    }

    public function testIsFolder()
    {
        $this->assertTrue($this->accessProtectedMethod('isFolder', 'folder'));
    }

    public function testIsTemporaryLink()
    {
        $data['metadata'] = 'x';
        $data['link'] = 'y';

        $this->assertTrue($this->accessProtectedMethod('isTemporaryLink', $data));
    }

    public function testIsList()
    {
        $data['entries'] = 'x';

        $this->assertTrue($this->accessProtectedMethod('isList', $data));
    }

    public function testIsSearchResult()
    {
        $data['matches'] = 'x';

        $this->assertTrue($this->accessProtectedMethod('isSearchResult', $data));
    }

    public function testIsDeletedFileOrFolder()
    {
        $data = [];

        $this->assertTrue($this->accessProtectedMethod('isDeletedFileOrFolder', $data));
    }
}
