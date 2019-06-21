<?php


namespace Dontdrinkandroot\Path;

use PHPUnit\Framework\TestCase;

class FilePathTest extends TestCase
{
    public function testBasic()
    {
        $path = FilePath::parse('/index.md');
        $this->assertEquals('index.md', $path->getName());
        $this->assertEquals('md', $path->getExtension());
        $this->assertEquals('index', $path->getFileName());

        $path = FilePath::parse('index.md');
        $this->assertEquals('index.md', $path->getName());
        $this->assertEquals('md', $path->getExtension());
        $this->assertEquals('index', $path->getFileName());

        $path = FilePath::parse('/sub/subsub/index.md');
        $this->assertEquals('index.md', $path->getName());
        $this->assertEquals('md', $path->getExtension());
        $this->assertEquals('index', $path->getFileName());

        $this->assertEquals('/sub/subsub/', $path->getParentPath()->toAbsoluteUrlString());

        $path = FilePath::parse('sub/subsub/index.md');
        $this->assertEquals('index.md', $path->getName());
        $this->assertEquals('md', $path->getExtension());
        $this->assertEquals('index', $path->getFileName());

        $this->assertEquals('/sub/subsub/', $path->getParentPath()->toAbsoluteUrlString());
    }

    public function testNoExtension()
    {
        $path = FilePath::parse('/index');
        $this->assertEquals('index', $path->getName());
        $this->assertNull($path->getExtension());
        $this->assertEquals('index', $path->getFileName());

        $path = FilePath::parse('/sub/index');
        $this->assertEquals('index', $path->getName());
        $this->assertNull($path->getExtension());
        $this->assertEquals('index', $path->getFileName());

        $this->assertEquals('/sub/', $path->getParentPath()->toAbsoluteUrlString());
    }

    public function testDotFile()
    {
        $path = FilePath::parse('/.index');
        $this->assertEquals('.index', $path->getName());
        $this->assertNull($path->getExtension());
        $this->assertEquals('.index', $path->getFileName());

        $path = FilePath::parse('/sub/.index');
        $this->assertEquals('.index', $path->getName());
        $this->assertNull($path->getExtension());
        $this->assertEquals('.index', $path->getFileName());

        $this->assertEquals('/sub/', $path->getParentPath()->toAbsoluteUrlString());
    }

    public function testInvalidPath()
    {
        try {
            $path = new FilePath(null);
            $this->fail('Exception expected');
        } catch (\Throwable $e) {
            /* Expected */
            $this->assertInstanceOf(\TypeError::class, $e);
        }

        try {
            $path = new FilePath('bla/bla');
            $this->fail('Exception expected');
        } catch (\Exception $e) {
            /* Expected */
            $this->assertEquals('Name must not contain /', $e->getMessage());
        }

        try {
            $path = FilePath::parse('');
            $this->fail('Exception expected');
        } catch (\Exception $e) {
            /* Expected */
            $this->assertEquals('Path String must not be empty', $e->getMessage());
        }

        try {
            $path = FilePath::parse('/');
            $this->fail('Exception expected');
        } catch (\Exception $e) {
            /* Expected */
            $this->assertEquals('Path String must not end with /', $e->getMessage());
        }
    }

    public function testCollectPaths()
    {
        $path = FilePath::parse("/sub/subsub/index.md");
        $paths = $path->collectPaths();
        $this->assertCount(4, $paths);
        $this->assertEquals(null, $paths[0]->getName());
        $this->assertEquals('sub', $paths[1]->getName());
        $this->assertEquals('subsub', $paths[2]->getName());
        $this->assertEquals('index', $paths[3]->getFilename());
    }

    public function testToStrings()
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
