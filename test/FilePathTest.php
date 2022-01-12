<?php


namespace Dontdrinkandroot\Path;

use Exception;
use PHPUnit\Framework\TestCase;
use Throwable;
use TypeError;

class FilePathTest extends TestCase
{
    public function testBasic(): void
    {
        $path = FilePath::parse('/index.md');
        $this->assertEquals('index.md', $path->name);
        $this->assertEquals('md', $path->getExtension());
        $this->assertEquals('index', $path->getFileName());

        $path = FilePath::parse('index.md');
        $this->assertEquals('index.md', $path->name);
        $this->assertEquals('md', $path->getExtension());
        $this->assertEquals('index', $path->getFileName());

        $path = FilePath::parse('/sub/subsub/index.md');
        $this->assertEquals('index.md', $path->name);
        $this->assertEquals('md', $path->getExtension());
        $this->assertEquals('index', $path->getFileName());

        $this->assertEquals('/sub/subsub/', $path->parent->toAbsoluteUrlString());

        $path = FilePath::parse('sub/subsub/index.md');
        $this->assertEquals('index.md', $path->name);
        $this->assertEquals('md', $path->getExtension());
        $this->assertEquals('index', $path->getFileName());

        $this->assertEquals('/sub/subsub/', $path->parent->toAbsoluteUrlString());
    }

    public function testNoExtension(): void
    {
        $path = FilePath::parse('/index');
        $this->assertEquals('index', $path->name);
        $this->assertNull($path->getExtension());
        $this->assertEquals('index', $path->getFileName());

        $path = FilePath::parse('/sub/index');
        $this->assertEquals('index', $path->name);
        $this->assertNull($path->getExtension());
        $this->assertEquals('index', $path->getFileName());

        $this->assertEquals('/sub/', $path->parent->toAbsoluteUrlString());
    }

    public function testDotFile(): void
    {
        $path = FilePath::parse('/.index');
        $this->assertEquals('.index', $path->name);
        $this->assertNull($path->getExtension());
        $this->assertEquals('.index', $path->getFileName());

        $path = FilePath::parse('/sub/.index');
        $this->assertEquals('.index', $path->name);
        $this->assertNull($path->getExtension());
        $this->assertEquals('.index', $path->getFileName());

        $this->assertEquals('/sub/', $path->parent->toAbsoluteUrlString());
    }

    public function testInvalidPath(): void
    {
        try {
            $path = new FilePath(null);
            $this->fail('Exception expected');
        } catch (Throwable $e) {
            /* Expected */
            $this->assertInstanceOf(TypeError::class, $e);
        }

        try {
            $path = new FilePath('bla/bla');
            $this->fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            $this->assertEquals('Name must not contain /', $e->getMessage());
        }

        try {
            $path = FilePath::parse('');
            $this->fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            $this->assertEquals('Path String must not be empty', $e->getMessage());
        }

        try {
            $path = FilePath::parse('/');
            $this->fail('Exception expected');
        } catch (Exception $e) {
            /* Expected */
            $this->assertEquals('Path String must not end with /', $e->getMessage());
        }
    }

    public function testCollectPaths(): void
    {
        $path = FilePath::parse("/sub/subsub/index.md");
        $paths = $path->collectPaths();
        $this->assertCount(4, $paths);
        $this->assertInstanceOf(RootPath::class, $paths[0]);
        $this->assertInstanceOf(DirectoryPath::class, $paths[1]);
        $this->assertEquals('sub', $paths[1]->name);
        $this->assertInstanceOf(DirectoryPath::class, $paths[2]);
        $this->assertEquals('subsub', $paths[2]->name);
        $this->assertInstanceOf(FilePath::class, $paths[3]);
        $this->assertEquals('index.md', $paths[3]->name);
    }

    public function testToStrings(): void
    {
        $path = FilePath::parse("/sub/subsub/index.md");
        $this->assertEquals('/sub/subsub/index.md', $path->toAbsoluteString());
        $this->assertEquals(
            DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'subsub' . DIRECTORY_SEPARATOR . 'index.md',
            $path->toAbsoluteString(DIRECTORY_SEPARATOR)
        );
        $this->assertEquals('sub/subsub/index.md', $path->toRelativeString());
        $this->assertEquals(
            'sub' . DIRECTORY_SEPARATOR . 'subsub' . DIRECTORY_SEPARATOR . 'index.md',
            $path->toRelativeString(DIRECTORY_SEPARATOR)
        );
    }

    public function testParse()
    {
        $this->assertEquals('/sub/subsub/index.md', FilePath::parse("/sub/subsub/index.md")->toAbsoluteString());
        $this->assertEquals('/sub/subsub/index.md', FilePath::parse('\sub\subsub\index.md', '\\')->toAbsoluteString());
    }

    public function testPrepend()
    {
        $path1 = DirectoryPath::parse("/sub/");
        $path2 = FilePath::parse("/subsub/index.md");
        $mergedPath = $path2->prepend($path1);
        $this->assertEquals('/sub/subsub/index.md', $mergedPath->toAbsoluteUrlString());
    }
}
