<?php

namespace Dontdrinkandroot\Path;

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
