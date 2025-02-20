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
            $path = new ChildDirectoryPath('bla/bla');
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
            $path = new ChildDirectoryPath('asdf');
            /** @phpstan-ignore argument.type */
            $path->appendDirectory('');
            $this->fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            $this->assertEquals('Name must not be empty', $e->getMessage());
        }

        try {
            $path = new ChildDirectoryPath('asdf');
            /** @phpstan-ignore argument.type */
            $path->appendFile('');
            $this->fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            $this->assertEquals('Name must not be empty', $e->getMessage());
        }

        try {
            $path = new ChildDirectoryPath('asdf');
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
        $this->assertInstanceOf(RootDirectoryPath::class, $path);

        $path = new RootDirectoryPath();
        $this->assertInstanceOf(RootDirectoryPath::class, $path);
    }

    public function testFirstLevel(): void
    {
        $path = DirectoryPath::parse('/sub/');
        $this->assertInstanceOf(ChildPathInterface::class, $path);
        $this->assertFirstLevel($path);

        $path = DirectoryPath::parse('sub/');
        $this->assertInstanceOf(ChildPathInterface::class, $path);
        $this->assertFirstLevel($path);

        $path = DirectoryPath::parse('/sub//');
        $this->assertInstanceOf(ChildPathInterface::class, $path);
        $this->assertFirstLevel($path);

        $path = new ChildDirectoryPath('sub');
        $this->assertFirstLevel($path);
    }

    protected function assertFirstLevel(ChildPathInterface $path): void
    {
        $this->assertEquals('sub', $path->getName());
        $this->assertEquals('/sub/', $path->toAbsoluteUrlString());
        $this->assertInstanceOf(RootDirectoryPath::class, $path->getParent());
    }

    public function testSecondLevel(): void
    {
        $path = DirectoryPath::parse('/sub/subsub/');
        $this->assertInstanceOf(ChildPathInterface::class, $path);
        $this->assertSecondLevel($path);

        $path = DirectoryPath::parse('/sub/subsub//');
        $this->assertInstanceOf(ChildPathInterface::class, $path);
        $this->assertSecondLevel($path);

        $path = DirectoryPath::parse('sub/subsub//');
        $this->assertInstanceOf(ChildPathInterface::class, $path);
        $this->assertSecondLevel($path);
    }

    protected function assertSecondLevel(ChildPathInterface $path): void
    {
        $this->assertEquals('subsub', $path->getName());
        $this->assertEquals('/sub/subsub/', $path->toAbsoluteUrlString());
        $parent = $path->getParent();
        $this->assertInstanceOf(ChildDirectoryPath::class, $parent);
        $this->assertInstanceOf(RootDirectoryPath::class, $parent->getParent());
    }

    public function testAppend(): void
    {
        $newPath = new ChildDirectoryPath('sub');
        $this->assertFirstLevel($newPath);

        $newPath = $newPath->appendDirectory('subsub');
        $this->assertSecondLevel($newPath);

        $filePath = $newPath->appendFile('index.md');
        $this->assertEquals('index.md', $filePath->getName());
        $this->assertEquals('index', $filePath->getFileName());
        $this->assertEquals('md', $filePath->getExtension());
    }

    public function testCollectPaths(): void
    {
        $path = DirectoryPath::parse("/sub/subsub/");
        $paths = $path->collectPaths();
        $this->assertCount(3, $paths);
        $this->assertInstanceOf(RootDirectoryPath::class, $paths[0]);
        $path1 = $paths[1];
        $this->assertInstanceOf(ChildPathInterface::class, $path1);
        $this->assertEquals('sub', $path1->getName());
        $path2 = $paths[2];
        $this->assertInstanceOf(ChildPathInterface::class, $path2);
        $this->assertEquals('subsub', $path2->getName());
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

    public function testComplicatedPath(): void
    {
        $path = DirectoryPath::parse('/sub/./bla//../subsub/');
        $this->assertInstanceOf(ChildPathInterface::class, $path);
        $this->assertSecondLevel($path);
    }

    public function testPrepend(): void
    {
        $path1 = DirectoryPath::parse("/sub/");
        $path2 = DirectoryPath::parse("/subsub/");
        $mergedPath = $path2->prepend($path1);
        $this->assertInstanceOf(ChildPathInterface::class, $mergedPath);
        $this->assertSecondLevel($mergedPath);
    }

    public function testAppendPathString(): void
    {
        $path = DirectoryPath::parse('/sub/');

        $directoryPath = $path->appendPathString('subsub/');
        $this->assertInstanceOf(ChildPathInterface::class, $directoryPath);
        $this->assertInstanceOf(DirectoryPathInterface::class, $directoryPath);
        $this->assertSecondLevel($directoryPath);

        $filePath = $path->appendPathString('subsub/index.md');
        $this->assertInstanceOf(FilePath::class, $filePath);
        $this->assertInstanceOf(ChildPathInterface::class, $filePath->getParent());
        $this->assertSecondLevel($filePath->getParent());
        $this->assertEquals('index.md', $filePath->name);
        $this->assertEquals('index', $filePath->getFileName());
        $this->assertEquals('md', $filePath->getExtension());

        $filePath = $directoryPath->appendPathString('index.md');
        $this->assertInstanceOf(FilePath::class, $filePath);
        $this->assertInstanceOf(ChildPathInterface::class, $filePath->getParent());
        $this->assertSecondLevel($filePath->getParent());
        $this->assertEquals('index.md', $filePath->name);
        $this->assertEquals('index', $filePath->getFileName());
        $this->assertEquals('md', $filePath->getExtension());

        $path = DirectoryPath::parse('/sub/bla/');
        $filePath = $path->appendPathString('./../subsub/index.md');
        $this->assertInstanceOf(FilePath::class, $filePath);
        $this->assertInstanceOf(ChildPathInterface::class, $filePath->getParent());
        $this->assertSecondLevel($filePath->getParent());
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
