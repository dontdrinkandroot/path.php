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
            self::fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            self::assertEquals('Path String must end with /', $e->getMessage());
        }

        try {
            $path = new ChildDirectoryPath('bla/bla');
            self::fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            self::assertEquals('Name must not contain /', $e->getMessage());
        }

        try {
            $path = DirectoryPath::parse('/../');
            self::fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            self::assertEquals('Exceeding root level', $e->getMessage());
        }

        try {
            $path = new ChildDirectoryPath('asdf');
            /** @phpstan-ignore argument.type */
            $path->appendDirectory('');
            self::fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            self::assertEquals('Name must not be empty', $e->getMessage());
        }

        try {
            $path = new ChildDirectoryPath('asdf');
            /** @phpstan-ignore argument.type */
            $path->appendFile('');
            self::fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            self::assertEquals('Name must not be empty', $e->getMessage());
        }

        try {
            $path = new ChildDirectoryPath('asdf');
            $path->appendDirectory('bla/bla');
            self::fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            self::assertEquals('Name must not contain /', $e->getMessage());
        }
    }

    public function testRoot(): void
    {
        $path = DirectoryPath::parse('/');
        self::assertInstanceOf(RootDirectoryPath::class, $path);

        $path = new RootDirectoryPath();
        self::assertInstanceOf(RootDirectoryPath::class, $path);
    }

    public function testFirstLevel(): void
    {
        $path = DirectoryPath::parse('/sub/');
        self::assertInstanceOf(ChildPathInterface::class, $path);
        self::assertFirstLevel($path);

        $path = DirectoryPath::parse('sub/');
        self::assertInstanceOf(ChildPathInterface::class, $path);
        self::assertFirstLevel($path);

        $path = DirectoryPath::parse('/sub//');
        self::assertInstanceOf(ChildPathInterface::class, $path);
        self::assertFirstLevel($path);

        $path = new ChildDirectoryPath('sub');
        self::assertFirstLevel($path);
    }

    protected function assertFirstLevel(ChildPathInterface $path): void
    {
        self::assertEquals('sub', $path->getName());
        self::assertEquals('/sub/', $path->toAbsoluteUrlString());
        self::assertInstanceOf(RootDirectoryPath::class, $path->getParent());
    }

    protected static function testSecondLevel(): void
    {
        $path = DirectoryPath::parse('/sub/subsub/');
        self::assertInstanceOf(ChildPathInterface::class, $path);
        self::assertSecondLevel($path);

        $path = DirectoryPath::parse('/sub/subsub//');
        self::assertInstanceOf(ChildPathInterface::class, $path);
        self::assertSecondLevel($path);

        $path = DirectoryPath::parse('sub/subsub//');
        self::assertInstanceOf(ChildPathInterface::class, $path);
        self::assertSecondLevel($path);
    }

    protected static function assertSecondLevel(ChildPathInterface $path): void
    {
        self::assertEquals('subsub', $path->getName());
        self::assertEquals('/sub/subsub/', $path->toAbsoluteUrlString());
        $parent = $path->getParent();
        self::assertInstanceOf(ChildDirectoryPath::class, $parent);
        self::assertInstanceOf(RootDirectoryPath::class, $parent->getParent());
    }

    public function testAppend(): void
    {
        $newPath = new ChildDirectoryPath('sub');
        self::assertFirstLevel($newPath);

        $newPath = $newPath->appendDirectory('subsub');
        self::assertSecondLevel($newPath);

        $filePath = $newPath->appendFile('index.md');
        self::assertEquals('index.md', $filePath->getName());
        self::assertEquals('index', $filePath->getFileName());
        self::assertEquals('md', $filePath->getExtension());
    }

    public function testCollectPaths(): void
    {
        $path = DirectoryPath::parse("/sub/subsub/");
        $paths = $path->collectPaths();
        self::assertCount(3, $paths);
        self::assertInstanceOf(RootDirectoryPath::class, $paths[0]);
        $path1 = $paths[1];
        self::assertInstanceOf(ChildPathInterface::class, $path1);
        self::assertEquals('sub', $path1->getName());
        $path2 = $paths[2];
        self::assertInstanceOf(ChildPathInterface::class, $path2);
        self::assertEquals('subsub', $path2->getName());
    }

    public function testToStrings(): void
    {
        $path = DirectoryPath::parse("/sub/subsub/");
        self::assertEquals('/sub/subsub/', $path->toAbsoluteUrlString());
        self::assertEquals(
            DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'subsub' . DIRECTORY_SEPARATOR,
            $path->toAbsoluteFileSystemString()
        );
        self::assertEquals(
            DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'subsub' . DIRECTORY_SEPARATOR,
            $path->toAbsoluteFileSystemString()
        );

        self::assertEquals('sub/subsub/', $path->toRelativeUrlString());
        self::assertEquals(
            'sub' . DIRECTORY_SEPARATOR . 'subsub' . DIRECTORY_SEPARATOR,
            $path->toRelativeFileSystemString()
        );
        self::assertEquals(
            'sub' . DIRECTORY_SEPARATOR . 'subsub' . DIRECTORY_SEPARATOR,
            $path->toRelativeFileSystemString()
        );

        self::assertEquals('/sub/subsub/', (string)$path);
    }

    public function testComplicatedPath(): void
    {
        $path = DirectoryPath::parse('/sub/./bla//../subsub/');
        self::assertInstanceOf(ChildPathInterface::class, $path);
        self::assertSecondLevel($path);
    }

    public function testPrepend(): void
    {
        $path1 = DirectoryPath::parse("/sub/");
        $path2 = DirectoryPath::parse("/subsub/");
        $mergedPath = $path2->prepend($path1);
        self::assertInstanceOf(ChildPathInterface::class, $mergedPath);
        self::assertSecondLevel($mergedPath);
    }

    public function testAppendPathString(): void
    {
        $path = DirectoryPath::parse('/sub/');

        $directoryPath = $path->appendPathString('subsub/');
        self::assertInstanceOf(ChildPathInterface::class, $directoryPath);
        self::assertInstanceOf(DirectoryPathInterface::class, $directoryPath);
        self::assertSecondLevel($directoryPath);

        $filePath = $path->appendPathString('subsub/index.md');
        self::assertInstanceOf(FilePath::class, $filePath);
        self::assertInstanceOf(ChildPathInterface::class, $filePath->getParent());
        self::assertSecondLevel($filePath->getParent());
        self::assertEquals('index.md', $filePath->name);
        self::assertEquals('index', $filePath->getFileName());
        self::assertEquals('md', $filePath->getExtension());

        $filePath = $directoryPath->appendPathString('index.md');
        self::assertInstanceOf(FilePath::class, $filePath);
        self::assertInstanceOf(ChildPathInterface::class, $filePath->getParent());
        self::assertSecondLevel($filePath->getParent());
        self::assertEquals('index.md', $filePath->name);
        self::assertEquals('index', $filePath->getFileName());
        self::assertEquals('md', $filePath->getExtension());

        $path = DirectoryPath::parse('/sub/bla/');
        $filePath = $path->appendPathString('./../subsub/index.md');
        self::assertInstanceOf(FilePath::class, $filePath);
        self::assertInstanceOf(ChildPathInterface::class, $filePath->getParent());
        self::assertSecondLevel($filePath->getParent());
        self::assertEquals('index.md', $filePath->name);
        self::assertEquals('index', $filePath->getFileName());
        self::assertEquals('md', $filePath->getExtension());
    }

    public function testParse(): void
    {
        self::assertEquals('/sub/subsub/', DirectoryPath::parse("/sub/subsub/")->toAbsoluteString());
        self::assertEquals('/sub/subsub/', DirectoryPath::parse('\sub\subsub\\', '\\')->toAbsoluteString());
    }
}
