<?php

namespace Dontdrinkandroot\Path;

use InvalidArgumentException;
use RuntimeException;

abstract class Path
{
    /**
     * {@inheritdoc}
     */
    public function toAbsoluteUrlString(): string
    {
        return $this->toAbsoluteString();
    }

    /**
     * {@inheritdoc}
     */
    public function toRelativeUrlString(): string
    {
        return $this->toRelativeString();
    }

    /**
     * {@inheritdoc}
     */
    public function toAbsoluteFileSystemString(): string
    {
        return $this->toAbsoluteString(DIRECTORY_SEPARATOR);
    }

    /**
     * {@inheritdoc}
     */
    public function toRelativeFileSystemString(): string
    {
        return $this->toRelativeString(DIRECTORY_SEPARATOR);
    }

    public function __toString(): string
    {
        return $this->toAbsoluteString();
    }

    public static function parse(string $pathString): Path
    {
        if ($pathString === '' || $pathString === '/') {
            return new RootDirectoryPath();
        }

        if (str_ends_with($pathString, '/')) {
            return DirectoryPath::parse($pathString);
        }

        return FilePath::parse($pathString);
    }

    public function diff(Path $other, string $separator = '/'): string
    {
        $fromDirectoryPath = $this->resolveNearestDirectoryPath();
        $toDirectoryPath = $other->resolveNearestDirectoryPath();

        $pathDiff = static::getDirectoryPathDiff($fromDirectoryPath, $toDirectoryPath, $separator);
        if ($other instanceof FilePath) {
            $pathDiff .= $other->name;
        }

        return $pathDiff;
    }

    private static function getDirectoryPathDiff(
        DirectoryPath $fromPath,
        DirectoryPath $toPath,
        string $separator = '/'
    ): string {
        $fromParts = self::getDirectoryPathParts($fromPath);
        $toParts = static::getDirectoryPathParts($toPath);

        $fromDepth = count($fromParts);
        $toDepth = count($toParts);

        $equalUpToIndex = 0;
        while ($fromDepth > $equalUpToIndex
            && $toDepth > $equalUpToIndex
            && $fromParts[$equalUpToIndex] === $toParts[$equalUpToIndex]
        ) {
            $equalUpToIndex++;
        }

        $moveUp = $fromDepth - $equalUpToIndex;
        $result = str_repeat('..' . $separator, $moveUp);

        for ($i = $equalUpToIndex; $i < $toDepth; $i++) {
            $result .= $toParts[$i] . $separator;
        }

        return $result;
    }

    /** @return list<string> */
    private static function getDirectoryPathParts(DirectoryPath $path): array
    {
        $currentPath = $path;
        $parts = [];
        while ($currentPath instanceof ChildDirectoryPath) {
            $parts[] = $currentPath->name;
            $currentPath = $currentPath->parent;
        }

        return array_reverse($parts);
    }

    public function resolveNearestDirectoryPath(): DirectoryPath
    {
        if ($this instanceof FilePath) {
            return $this->parent;
        }

        if ($this instanceof DirectoryPath) {
            return $this;
        }

        throw new RuntimeException('Could not resolve parent Path');
    }

    protected static function assertValidName(string $name): void
    {
        if ('' === $name) {
            throw new InvalidArgumentException('Name must not be empty');
        }

        if (str_contains($name, '/')) {
            throw new InvalidArgumentException('Name must not contain /');
        }
    }

    abstract public function getName(): ?string;

    abstract public function getParent(): ?DirectoryPath;

    /** @return list<Path> */
    abstract public function collectPaths(): array;

    abstract public function toAbsoluteString(string $separator = '/'): string;

    abstract public function toRelativeString(string $separator = '/'): string;

    abstract public function getType(): PathType;

    abstract public function prepend(DirectoryPath $path): Path;

    abstract public function clone(): Path;
}
