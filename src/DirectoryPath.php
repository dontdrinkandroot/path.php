<?php

namespace Dontdrinkandroot\Path;

use Exception;
use InvalidArgumentException;
use Override;

abstract class DirectoryPath extends Path
{
    /**
     * @param string $pathString
     * @param DirectoryPath $relativeToPath
     * @param non-empty-string $separator
     *
     * @return DirectoryPath
     * @throws Exception
     */
    public static function parseDirectoryPath(
        string $pathString,
        DirectoryPath $relativeToPath,
        string $separator = '/'
    ): DirectoryPath {
        $lastPath = $relativeToPath->clone();
        $parts = explode($separator, $pathString);
        foreach ($parts as $part) {
            $trimmedPart = trim($part);
            if ($trimmedPart === '..') {
                if (!($lastPath instanceof ChildDirectoryPath)) {
                    throw new Exception('Exceeding root level');
                }
                $lastPath = $lastPath->getParent();
            } elseif ($trimmedPart !== "" && $trimmedPart !== '.') {
                $directoryPath = new ChildDirectoryPath($trimmedPart, $lastPath);
                $lastPath = $directoryPath;
            }
        }

        return $lastPath;
    }

    /**
     * @param string $pathString
     * @param non-empty-string $separator
     *
     * @return DirectoryPath
     * @throws InvalidArgumentException
     */
    #[Override]
    public static function parse(string $pathString, string $separator = '/'): DirectoryPath
    {
        if ('' === $pathString) {
            throw new InvalidArgumentException('Path String must not be empty');
        }

        if (!(str_ends_with($pathString, $separator))) {
            throw new InvalidArgumentException('Path String must end with ' . $separator);
        }

        return self::parseDirectoryPath($pathString, new RootDirectoryPath(), $separator);
    }

    public function appendDirectory(string $name): ChildDirectoryPath
    {
        return new ChildDirectoryPath($name, $this->clone());
    }

    public function appendFile(string $name): FilePath
    {
        return new FilePath($name, $this->clone());
    }

    #[Override]
    public function getType(): PathType
    {
        return PathType::DIRECTORY;
    }

    /**
     * @throws Exception
     */
    public function appendPathString(string $pathString): Path
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
            return new FilePath($filePart, $directoryPath);
        }

        return $directoryPath;
    }

    #[Override]
    abstract public function clone(): DirectoryPath;
}
