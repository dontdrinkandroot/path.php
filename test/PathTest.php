<?php

namespace Dontdrinkandroot\Path;

use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    public function testParseRootPath(): void
    {
        $path = Path::parse('');
        self::assertInstanceOf(RootDirectoryPath::class, $path);

        $path = Path::parse('/');
        self::assertInstanceOf(RootDirectoryPath::class, $path);
    }

    public function testParseDirectoryPath(): void
    {
        $path = Path::parse('test/');
        self::assertInstanceOf(ChildDirectoryPath::class, $path);
        self::assertEquals('test', $path->name);
        self::assertInstanceOf(RootDirectoryPath::class, $path->parent);
    }

    public function testParseFilePath(): void
    {
        $path = Path::parse('test/file.md');
        self::assertInstanceOf(FilePath::class, $path);
        self::assertEquals('file.md', $path->name);
        self::assertInstanceOf(ChildDirectoryPath::class, $path->parent);
        self::assertEquals('test', $path->parent->name);
        self::assertInstanceOf(RootDirectoryPath::class, $path->parent->parent);
    }

    public function testClone(): void
    {
        $path = FilePath::parse('/directory/file.extension');
        $clonedPath = $path->clone();
        self::assertNotSame($path, $clonedPath);
        self::assertNotSame($path->getParent(), $clonedPath->getParent());
        self::assertNotSame($path->getParent()->getParent(), $clonedPath->getParent()->getParent());
    }
}
