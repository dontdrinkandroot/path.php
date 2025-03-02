<?php


namespace Dontdrinkandroot\Path;

use Exception;
use PHPUnit\Framework\TestCase;

class FilePathTest extends TestCase
{
    public function testBasic(): void
    {
        $path = FilePath::parse('/index.md');
        self::assertEquals('index.md', $path->name);
        self::assertEquals('md', $path->getExtension());
        self::assertEquals('index', $path->getFileName());

        $path = FilePath::parse('index.md');
        self::assertEquals('index.md', $path->name);
        self::assertEquals('md', $path->getExtension());
        self::assertEquals('index', $path->getFileName());

        $path = FilePath::parse('/sub/subsub/index.md');
        self::assertEquals('index.md', $path->name);
        self::assertEquals('md', $path->getExtension());
        self::assertEquals('index', $path->getFileName());

        self::assertEquals('/sub/subsub/', $path->parent->toAbsoluteUrlString());

        $path = FilePath::parse('sub/subsub/index.md');
        self::assertEquals('index.md', $path->name);
        self::assertEquals('md', $path->getExtension());
        self::assertEquals('index', $path->getFileName());

        self::assertEquals('/sub/subsub/', $path->parent->toAbsoluteUrlString());
    }

    public function testNoExtension(): void
    {
        $path = FilePath::parse('/index');
        self::assertEquals('index', $path->name);
        self::assertNull($path->getExtension());
        self::assertEquals('index', $path->getFileName());

        $path = FilePath::parse('/sub/index');
        self::assertEquals('index', $path->name);
        self::assertNull($path->getExtension());
        self::assertEquals('index', $path->getFileName());

        self::assertEquals('/sub/', $path->parent->toAbsoluteUrlString());
    }

    public function testDotFile(): void
    {
        $path = FilePath::parse('/.index');
        self::assertEquals('.index', $path->name);
        self::assertNull($path->getExtension());
        self::assertEquals('.index', $path->getFileName());

        $path = FilePath::parse('/sub/.index');
        self::assertEquals('.index', $path->name);
        self::assertNull($path->getExtension());
        self::assertEquals('.index', $path->getFileName());

        self::assertEquals('/sub/', $path->parent->toAbsoluteUrlString());
    }

    public function testInvalidPath(): void
    {
        try {
            new FilePath('bla/bla');
            self::fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            self::assertEquals('Name must not contain /', $e->getMessage());
        }

        try {
            FilePath::parse('');
            self::fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            self::assertEquals('Path String must not be empty', $e->getMessage());
        }

        try {
            FilePath::parse('/');
            self::fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            self::assertEquals('Path String must not end with /', $e->getMessage());
        }
    }

    public function testCollectPaths(): void
    {
        $path = FilePath::parse("/sub/subsub/index.md");
        $paths = $path->collectPaths();
        self::assertCount(4, $paths);
        self::assertInstanceOf(RootDirectoryPath::class, $paths[0]);
        self::assertInstanceOf(ChildDirectoryPath::class, $paths[1]);
        self::assertEquals('sub', $paths[1]->name);
        self::assertInstanceOf(ChildDirectoryPath::class, $paths[2]);
        self::assertEquals('subsub', $paths[2]->name);
        self::assertInstanceOf(FilePath::class, $paths[3]);
        self::assertEquals('index.md', $paths[3]->name);
    }

    public function testToStrings(): void
    {
        $path = FilePath::parse("/sub/subsub/index.md");
        self::assertEquals('/sub/subsub/index.md', $path->toAbsoluteString());
        self::assertEquals(
            DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'subsub' . DIRECTORY_SEPARATOR . 'index.md',
            $path->toAbsoluteString(DIRECTORY_SEPARATOR)
        );
        self::assertEquals('sub/subsub/index.md', $path->toRelativeString());
        self::assertEquals(
            'sub' . DIRECTORY_SEPARATOR . 'subsub' . DIRECTORY_SEPARATOR . 'index.md',
            $path->toRelativeString(DIRECTORY_SEPARATOR)
        );
    }

    public function testParse(): void
    {
        self::assertEquals('/sub/subsub/index.md', FilePath::parse("/sub/subsub/index.md")->toAbsoluteString());
        self::assertEquals('/sub/subsub/index.md', FilePath::parse('\sub\subsub\index.md', '\\')->toAbsoluteString());
    }

    public function testPrepend(): void
    {
        $path1 = DirectoryPath::parse("/sub/");
        $path2 = FilePath::parse("/subsub/index.md");
        $mergedPath = $path2->prepend($path1);
        self::assertEquals('/sub/subsub/index.md', $mergedPath->toAbsoluteUrlString());
    }
}
