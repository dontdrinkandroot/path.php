<?php

namespace Dontdrinkandroot\Path;

use InvalidArgumentException;
use Override;

/**
 * @phpstan-require-implements DirectoryPathInterface
 */
trait DirectoryPathTrait
{
    #[Override]
    public function appendDirectory(string $name): DirectoryPathInterface&ChildPathInterface
    {
        Path::assertValidName($name);

        return new ChildDirectoryPath($name, $this->clone());
    }

    #[Override]
    public function appendFile(string $name): FilePath
    {
        Path::assertValidName($name);

        return new FilePath($name, $this->clone());
    }

    #[Override]
    public function isDirectoryPath(): bool
    {
        return true;
    }

    #[Override]
    public function getType(): PathType
    {
        return PathType::DIRECTORY;
    }

    #[Override]
    public function resolveNearestDirectoryPath(): DirectoryPathInterface
    {
        return $this;
    }

    #[Override]
    public function appendPathString(string $pathString): PathInterface
    {
        if ('' === $pathString) {
            return $this->clone();
        }

        $filePart = null;
        $lastSlashPos = strrpos($pathString, '/');
        $directoryPart = $pathString;
        if ($lastSlashPos === false) {
            $directoryPart = null;
        }
        if (!str_ends_with($pathString, '/')) {
            $filePart = $pathString;
            if (false !== $lastSlashPos) {
                $directoryPart = substr($pathString, 0, $lastSlashPos + 1);
                $filePart = substr($pathString, $lastSlashPos + 1);
            }
        }

        $directoryPath = null === $directoryPart
            ? $this->clone()
            : DirectoryPath::parseDirectoryPath($directoryPart, $this);

        if (null !== $filePart) {
            if ('' === $filePart) {
                throw new InvalidArgumentException('Cannot append empty file name');
            }
            return new FilePath($filePart, $directoryPath);
        }

        return $directoryPath;
    }
}
