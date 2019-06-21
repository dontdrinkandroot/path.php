<?php


namespace Dontdrinkandroot\Path;

use Dontdrinkandroot\Utils\StringUtils;
use PHPUnit\Framework\TestCase;

class PathUtilsTest extends TestCase
{
    public function testDiff()
    {
        $from = new DirectoryPath();
        $to = new DirectoryPath();
        $result = PathUtils::getPathDiff($from, $to);
        $this->assertEquals('', $result);
        $this->assertEquals($to, $from->appendPathString($result));
        $this->assertEquals($to->__toString(), $from->appendPathString($result)->__toString());

        $from = DirectoryPath::parse('a/b/c/');
        $to = DirectoryPath::parse('a/b/d/');
        $result = PathUtils::getPathDiff($from, $to);
        $this->assertEquals('../d/', $result);
        $this->assertEquals($to, $from->appendPathString($result));
        $this->assertEquals($to->__toString(), $from->appendPathString($result)->__toString());

        $from = DirectoryPath::parse('a/b/c/');
        $to = DirectoryPath::parse('d/e/f/');
        $result = PathUtils::getPathDiff($from, $to);
        $this->assertEquals('../../../d/e/f/', $result);
        $this->assertEquals($to, $from->appendPathString($result));
        $this->assertEquals($to->__toString(), $from->appendPathString($result)->__toString());

        $this->assertEquals('../', PathUtils::getPathDiff(new DirectoryPath('test'), new DirectoryPath()));
        $this->assertEquals('test/', PathUtils::getPathDiff(new DirectoryPath(), new DirectoryPath('test')));
        $this->assertEquals('../b/', PathUtils::getPathDiff(new DirectoryPath('a'), new DirectoryPath('b')));
        $this->assertEquals('', PathUtils::getPathDiff(new FilePath('a.txt'), new DirectoryPath()));
        $this->assertEquals('a.txt', PathUtils::getPathDiff(new DirectoryPath(), new FilePath('a.txt')));
        $this->assertEquals('b.txt', PathUtils::getPathDiff(new FilePath('a.txt'), new FilePath('b.txt')));
        $this->assertEquals('c/b.txt', PathUtils::getPathDiff(new FilePath('a.txt'), FilePath::parse('c/b.txt')));

        $from = FilePath::parse('c/a.txt');
        $to = new FilePath('b.txt');
        $this->assertEquals('..\b.txt', PathUtils::getPathDiff($from, $to, '\\'));
    }

    public function testEndsWith()
    {
        $this->assertTrue(PathUtils::endsWith('bla', ''));
        $this->assertTrue(PathUtils::endsWith('bla', 'la'));
        $this->assertfalse(PathUtils::endsWith('bla', 'bl'));
    }

    public function testGetLastChar()
    {
        $this->assertFalse(PathUtils::getLastChar(''));
        $this->assertEquals('a', PathUtils::getLastChar('bla'));
    }
}
