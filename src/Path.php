<?php

namespace Dontdrinkandroot\Path;

use InvalidArgumentException;
use Override;

abstract class Path implements PathInterface
{
    #[Override]
    public function __toString(): string
    {
        return $this->toAbsoluteString();
    }

    #[Override]
    public function toAbsoluteFileSystemString(): string
    {
        return $this->toAbsoluteString(DIRECTORY_SEPARATOR);
    }

    #[Override]
    public function toRelativeFileSystemString(): string
    {
        return $this->toRelativeString(DIRECTORY_SEPARATOR);
    }

    #[Override]
    public function toAbsoluteUrlString(): string
    {
        return $this->toAbsoluteString('/');
    }

    #[Override]
    public function toRelativeUrlString(): string
    {
        return $this->toRelativeString('/');
    }

    #[Override]
    public function diff(PathInterface $other, string $separator = '/'): string
    {
        $fromDirectoryPath = $this->resolveNearestDirectoryPath();
        $toDirectoryPath = $other->resolveNearestDirectoryPath();

        $pathDiff = self::getDirectoryPathDiff($fromDirectoryPath, $toDirectoryPath, $separator);
        if ($other instanceof FilePath) {
            $pathDiff .= $other->name;
        }

        return $pathDiff;
    }

    public static function root(): RootDirectoryPath
    {
        return new RootDirectoryPath();
    }

    public static function parse(string $pathString): PathInterface
    {
        if ($pathString === '' || $pathString === '/') {
            return new RootDirectoryPath();
        }

        if (str_ends_with($pathString, '/')) {
            return DirectoryPath::parse($pathString);
        }

        return FilePath::parse($pathString);
    }

    private static function getDirectoryPathDiff(
        DirectoryPathInterface $fromPath,
        DirectoryPathInterface $toPath,
        string $separator = '/'
    ): string {
        $fromParts = self::getDirectoryPathParts($fromPath);
        $toParts = self::getDirectoryPathParts($toPath);

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
    private static function getDirectoryPathParts(DirectoryPathInterface $path): array
    {
        $currentPath = $path;
        $parts = [];
        while ($currentPath instanceof ChildPathInterface) {
            $parts[] = $currentPath->getName();
            $currentPath = $currentPath->getParent();
        }

        return array_reverse($parts);
    }

    public static function assertValidName(string $name): void
    {
        if ('' === $name) {
            throw new InvalidArgumentException('Name must not be empty');
        }

        if (str_contains($name, '/')) {
            throw new InvalidArgumentException('Name must not contain /');
        }
    }
}
