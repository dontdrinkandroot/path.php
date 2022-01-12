<?php


namespace Dontdrinkandroot\Path;

use Exception;
use PHPUnit\Framework\TestCase;

class DirectoryPathTest extends TestCase
{
    public function testInvalid(): void
    {
        try {
            $path = DirectoryPath::parse('bla');
            $this->fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            $this->assertEquals('Path String must end with /', $e->getMessage());
        }

        try {
            $path = new DirectoryPath('bla/bla');
            $this->fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            $this->assertEquals('Name must not contain /', $e->getMessage());
        }

        try {
            $path = DirectoryPath::parse('/../');
            $this->fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            $this->assertEquals('Exceeding root level', $e->getMessage());
        }

        try {
            $path = new DirectoryPath('asdf');
            $path->appendDirectory('');
            $this->fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            $this->assertEquals('Name must not be empty', $e->getMessage());
        }

        try {
            $path = new DirectoryPath('asdf');
            $path->appendFile('');
            $this->fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            $this->assertEquals('Name must not be empty', $e->getMessage());
        }

        try {
            $path = new DirectoryPath('asdf');
            $path->appendDirectory('bla/bla');
            $this->fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            $this->assertEquals('Name must not contain /', $e->getMessage());
        }
    }

    public function testRoot(): void
    {
        $path = DirectoryPath::parse('/');
        $this->assertInstanceOf(RootPath::class, $path);

        $path = new RootPath();
        $this->assertInstanceOf(RootPath::class, $path);
    }

    public function testFirstLevel(): void
    {
        $path = DirectoryPath::parse('/sub/');
        $this->assertFirstLevel($path);

        $path = DirectoryPath::parse('sub/');
        $this->assertFirstLevel($path);

        $path = DirectoryPath::parse('/sub//');
        $this->assertFirstLevel($path);

        $path = new DirectoryPath('sub');
        $this->assertFirstLevel($path);
    }

    protected function assertFirstLevel(DirectoryPath $path): void
    {
        $this->assertEquals('sub', $path->name);
        $this->assertEquals('/sub/', $path->toAbsoluteUrlString());
        $this->assertInstanceOf(RootPath::class, $path->parent);
    }

    public function testSecondLevel(): void
    {
        $path = DirectoryPath::parse('/sub/subsub/');
        $this->assertSecondLevel($path);

        $path = DirectoryPath::parse('/sub/subsub//');
        $this->assertSecondLevel($path);

        $path = DirectoryPath::parse('sub/subsub//');
        $this->assertSecondLevel($path);
    }

    protected function assertSecondLevel(DirectoryPath $path)
    {
        $this->assertEquals('subsub', $path->name);
        $this->assertEquals('/sub/subsub/', $path->toAbsoluteUrlString());
        $this->assertInstanceOf(DirectoryPath::class, $path->parent);
        $this->assertInstanceOf(RootPath::class, $path->parent->parent);
    }

    public function testAppend(): void
    {
        $newPath = new DirectoryPath('sub');
        $this->assertFirstLevel($newPath);

        $newPath = $newPath->appendDirectory('subsub');
        $this->assertSecondLevel($newPath);

        $filePath = $newPath->appendFile('index.md');
        $this->assertEquals('index.md', $filePath->name);
        $this->assertEquals('index', $filePath->getFileName());
        $this->assertEquals('md', $filePath->getExtension());
    }

    public function testCollectPaths()
    {
        $path = DirectoryPath::parse("/sub/subsub/");
        $paths = $path->collectPaths();
        $this->assertCount(3, $paths);
        $this->assertInstanceOf(RootPath::class, $paths[0]);
        $this->assertEquals('sub', $paths[1]->name);
        $this->assertEquals('subsub', $paths[2]->name);
    }

    public function testToStrings(): void
    {
        $path = DirectoryPath::parse("/sub/subsub/");
        $this->assertEquals('/sub/subsub/', $path->toAbsoluteUrlString());
        $this->assertEquals(
            DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'subsub' . DIRECTORY_SEPARATOR,
            $path->toAbsoluteFileSystemString()
        );
        $this->assertEquals(
            DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'subsub' . DIRECTORY_SEPARATOR,
            $path->toAbsoluteFileSystemString()
        );

        $this->assertEquals('sub/subsub/', $path->toRelativeUrlString());
        $this->assertEquals(
            'sub' . DIRECTORY_SEPARATOR . 'subsub' . DIRECTORY_SEPARATOR,
            $path->toRelativeFileSystemString()
        );
        $this->assertEquals(
            'sub' . DIRECTORY_SEPARATOR . 'subsub' . DIRECTORY_SEPARATOR,
            $path->toRelativeFileSystemString()
        );

        $this->assertEquals('/sub/subsub/', (string)$path);
    }

    public function testComplicatedPath()
    {
        $path = DirectoryPath::parse('/sub/./bla//../subsub/');
        $this->assertSecondLevel($path);
    }

    public function testPrepend()
    {
        $path1 = DirectoryPath::parse("/sub/");
        $path2 = DirectoryPath::parse("/subsub/");
        $mergedPath = $path2->prepend($path1);
        $this->assertSecondLevel($mergedPath);
    }

    public function testAppendPathString(): void
    {
        $path = DirectoryPath::parse('/sub/');

        $directoryPath = $path->appendPathString('subsub/');
        $this->assertSecondLevel($directoryPath);

        $filePath = $path->appendPathString('subsub/index.md');
        $this->assertInstanceOf(FilePath::class, $filePath);
        $this->assertSecondLevel($filePath->parent);
        $this->assertEquals('index.md', $filePath->name);
        $this->assertEquals('index', $filePath->getFileName());
        $this->assertEquals('md', $filePath->getExtension());

        $filePath = $directoryPath->appendPathString('index.md');
        $this->assertInstanceOf(FilePath::class, $filePath);
        $this->assertSecondLevel($filePath->parent);
        $this->assertEquals('index.md', $filePath->name);
        $this->assertEquals('index', $filePath->getFileName());
        $this->assertEquals('md', $filePath->getExtension());

        $path = DirectoryPath::parse('/sub/bla/');
        $filePath = $path->appendPathString('./../subsub/index.md');
        $this->assertInstanceOf(FilePath::class, $filePath);
        $this->assertSecondLevel($filePath->parent);
        $this->assertEquals('index.md', $filePath->name);
        $this->assertEquals('index', $filePath->getFileName());
        $this->assertEquals('md', $filePath->getExtension());
    }

    public function testParse(): void
    {
        $this->assertEquals('/sub/subsub/', DirectoryPath::parse("/sub/subsub/")->toAbsoluteString());
        $this->assertEquals('/sub/subsub/', DirectoryPath::parse('\sub\subsub\\', '\\')->toAbsoluteString());
    }
}
