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
        $parent = $path->getParent();
        $clonedParent = $clonedPath->getParent();
        self::assertNotSame($parent, $clonedParent);
        self::assertInstanceOf(ChildPathInterface::class, $parent);
        self::assertInstanceOf(ChildPathInterface::class, $clonedParent);
        self::assertNotSame($parent->getParent(), $clonedParent->getParent());
    }

    public function testDiff(): void
    {
        $from = new RootDirectoryPath();
        $to = new RootDirectoryPath();
        $result = $from->diff($to);
        self::assertEquals('', $result);
        self::assertEquals($to, $from->appendPathString($result));
        self::assertEquals($to->__toString(), $from->appendPathString($result)->__toString());

        $from = DirectoryPath::parse('a/b/c/');
        $to = DirectoryPath::parse('a/b/d/');
        $result = $from->diff($to);
        self::assertEquals('../d/', $result);
        self::assertEquals($to, $from->appendPathString($result));
        self::assertEquals($to->__toString(), $from->appendPathString($result)->__toString());

        $from = DirectoryPath::parse('a/b/c/');
        $to = DirectoryPath::parse('d/e/f/');
        $result = $from->diff($to);
        self::assertEquals('../../../d/e/f/', $result);
        self::assertEquals($to, $from->appendPathString($result));
        self::assertEquals($to->__toString(), $from->appendPathString($result)->__toString());

        self::assertEquals('../', (new ChildDirectoryPath('test'))->diff(new RootDirectoryPath()));
        self::assertEquals('test/', (new RootDirectoryPath())->diff(new ChildDirectoryPath('test')));
        self::assertEquals('../b/', (new ChildDirectoryPath('a'))->diff(new ChildDirectoryPath('b')));
        self::assertEquals('', (new FilePath('a.txt'))->diff(new RootDirectoryPath()));
        self::assertEquals('a.txt', (new RootDirectoryPath())->diff(new FilePath('a.txt')));
        self::assertEquals('b.txt', (new FilePath('a.txt'))->diff(new FilePath('b.txt')));
        self::assertEquals('c/b.txt', (new FilePath('a.txt'))->diff(FilePath::parse('c/b.txt')));

        $from = FilePath::parse('c/a.txt');
        $to = new FilePath('b.txt');
        self::assertEquals('..\b.txt', $from->diff($to, '\\'));
    }
}
