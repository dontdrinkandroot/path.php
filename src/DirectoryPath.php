<?php

namespace Dontdrinkandroot\Path;

use Exception;
use InvalidArgumentException;

abstract class DirectoryPath extends Path
{
    /**
     * @param string|null   $pathString
     * @param DirectoryPath $rootPath
     * @param string        $separator
     *
     * @return DirectoryPath
     * @throws Exception
     */
    public static function parseDirectoryPath(
        ?string $pathString,
        DirectoryPath $rootPath,
        string $separator = '/'
    ): DirectoryPath {
        if (null === $pathString) {
            return $rootPath;
        }

        $lastPath = $rootPath;
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
     * @param string $separator
     *
     * @return DirectoryPath
     * @throws InvalidArgumentException
     */
    public static function parse(string $pathString, string $separator = '/'): DirectoryPath
    {
        if ('' === $pathString) {
            throw new InvalidArgumentException('Path String must not be empty');
        }

        if (!(PathUtils::getLastChar($pathString) === $separator)) {
            throw new InvalidArgumentException('Path String must end with ' . $separator);
        }

        return self::parseDirectoryPath($pathString, new RootDirectoryPath(), $separator);
    }

    public function appendDirectory(string $name): ChildDirectoryPath
    {
        return new ChildDirectoryPath($name, clone $this);
    }

    public function appendFile(string $name): FilePath
    {
        return new FilePath($name, clone $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): PathType
    {
        return PathType::DIRECTORY;
    }

    /**
     * @param string $pathString
     *
     * @return Path
     * @throws Exception
     */
    public function appendPathString(string $pathString): Path
    {
        if ('' === $pathString) {
            return clone $this;
        }

        $lastPath = $this;

        $filePart = null;
        $lastSlashPos = strrpos($pathString, '/');
        $directoryPart = $pathString;
        if ($lastSlashPos === false) {
            $directoryPart = null;
        }
        if (!PathUtils::endsWith($pathString, '/')) {
            $filePart = $pathString;
            if (false !== $lastSlashPos) {
                $directoryPart = substr($pathString, 0, $lastSlashPos + 1);
                $filePart = substr($pathString, $lastSlashPos + 1);
            }
        }

        $directoryPath = DirectoryPath::parseDirectoryPath($directoryPart, $lastPath);

        if (null !== $filePart) {
            return new FilePath($filePart, $directoryPath);
        }

        return $directoryPath;
    }
}
