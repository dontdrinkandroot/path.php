<?php

namespace Dontdrinkandroot\Path;

use InvalidArgumentException;
use Override;

class FilePath extends Path implements ChildPath
{
    public function __construct(
        public readonly string $name,
        public readonly DirectoryPath $parent = new RootDirectoryPath()
    ) {
        self::assertValidName($name);
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    public function getFileName(): string
    {
        $lastDotPos = strrpos($this->name, '.');
        if (false !== $lastDotPos && $lastDotPos > 0) {
            return substr($this->name, 0, $lastDotPos);
        }

        return $this->name;
    }

    public function getExtension(): ?string
    {
        $lastDotPos = strrpos($this->name, '.');
        if (false !== $lastDotPos && $lastDotPos > 0) {
            return substr($this->name, $lastDotPos + 1);
        }

        return null;
    }

    #[Override]
    public function toRelativeString(string $separator = '/'): string
    {
        return $this->parent->toRelativeString($separator) . $this->name;
    }

    #[Override]
    public function toAbsoluteString(string $separator = '/'): string
    {
        return $this->parent->toAbsoluteString($separator) . $this->name;
    }

    #[Override]
    public function prepend(DirectoryPath $path): FilePath
    {
        return self::parse($path->toAbsoluteString() . $this->toAbsoluteString());
    }

    /**
     * @param non-empty-string $separator
     *
     * @throws InvalidArgumentException
     */
    #[Override]
    public static function parse(string $pathString, string $separator = '/'): FilePath
    {
        if ('' === $pathString) {
            throw new InvalidArgumentException('Path String must not be empty');
        }

        if (str_ends_with($pathString, $separator)) {
            throw new InvalidArgumentException('Path String must not end with ' . $separator);
        }

        $directoryPart = null;
        $filePart = $pathString;
        $lastSlashPos = strrpos($pathString, $separator);
        if (false !== $lastSlashPos) {
            $directoryPart = substr($pathString, 0, $lastSlashPos + 1);
            $filePart = substr($pathString, $lastSlashPos + 1);
        }

        return new FilePath(
            $filePart,
            null !== $directoryPart ? DirectoryPath::parse($directoryPart, $separator) : new RootDirectoryPath()
        );
    }

    public function withParent(RootDirectoryPath|ChildDirectoryPath $parent): FilePath
    {
        return new FilePath($this->name, $parent);
    }

    #[Override]
    public function getType(): PathType
    {
        return PathType::FILE;
    }

    #[Override]
    public function getParent(): DirectoryPath
    {
        return $this->parent;
    }

    #[Override]
    public function collectPaths(): array
    {
        return [...$this->parent->collectPaths(), $this];
    }

    #[Override]
    public function clone(): FilePath
    {
        return new FilePath($this->name, $this->parent->clone());
    }
}
