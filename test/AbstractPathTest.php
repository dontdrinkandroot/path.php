<?php

namespace Dontdrinkandroot\Path;

use PHPUnit\Framework\TestCase;

class AbstractPathTest extends TestCase
{
    public function testParseRootPath(): void
    {
        $path = AbstractPath::parse('');
        self::assertInstanceOf(RootPath::class, $path);

        $path = AbstractPath::parse('/');
        self::assertInstanceOf(RootPath::class, $path);
    }

    public function testParseDirectoryPath(): void
    {
        $path = AbstractPath::parse('test/');
        self::assertInstanceOf(DirectoryPath::class, $path);
        self::assertEquals('test', $path->name);
        self::assertInstanceOf(RootPath::class, $path->parent);
    }

    public function testParseFilePath(): void
    {
        $path = AbstractPath::parse('test/file.md');
        self::assertInstanceOf(FilePath::class, $path);
        self::assertEquals('file.md', $path->name);
        self::assertInstanceOf(DirectoryPath::class, $path->parent);
        self::assertEquals('test', $path->parent->name);
        self::assertInstanceOf(RootPath::class, $path->parent->parent);
    }
}
