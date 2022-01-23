<?php

namespace Dontdrinkandroot\Path;

use Exception;
use InvalidArgumentException;

class DirectoryPath extends AbstractChildPath implements ParentPath
{
    /**
     * {@inheritdoc}
     */
    public function appendDirectory(string $name): DirectoryPath
    {
        PathUtils::assertValidName($name);

        return new DirectoryPath($name, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function appendFile(string $name): FilePath
    {
        PathUtils::assertValidName($name);

        return new FilePath($name, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function toRelativeString(string $separator = '/'): string
    {
        return $this->parent->toRelativeString() . $this->name . $separator;
    }

    /**
     * {@inheritdoc}
     */
    public function toAbsoluteString(string $separator = '/'): string
    {
        return $this->parent->toAbsoluteString() . $this->name . $separator;
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(DirectoryPath $path): DirectoryPath
    {
        $directoryPath = DirectoryPath::parse($path->toAbsoluteString() . $this->toAbsoluteString());
        assert($directoryPath instanceof DirectoryPath);

        return $directoryPath;
    }

    /**
     * @param string $pathString
     * @param string $separator
     *
     * @return ParentPath
     * @throws InvalidArgumentException
     */
    public static function parse(string $pathString, string $separator = '/'): ParentPath
    {
        if ('' === $pathString) {
            throw new InvalidArgumentException('Path String must not be empty');
        }

        if (!(PathUtils::getLastChar($pathString) === $separator)) {
            throw new InvalidArgumentException('Path String must end with ' . $separator);
        }

        return self::parseDirectoryPath($pathString, new RootPath(), $separator);
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

        $directoryPath = self::parseDirectoryPath($directoryPart, $lastPath);

        if (null !== $filePart) {
            return new FilePath($filePart, $directoryPath);
        }

        return $directoryPath;
    }

    /**
     * @param string|null            $pathString
     * @param DirectoryPath|RootPath $rootPath
     * @param string                 $separator
     *
     * @return ParentPath
     * @throws Exception
     */
    protected static function parseDirectoryPath(
        ?string $pathString,
        ParentPath $rootPath,
        string $separator = '/'
    ): ParentPath {
        if (null === $pathString) {
            return $rootPath;
        }

        $lastPath = $rootPath;
        $parts = explode($separator, $pathString);
        foreach ($parts as $part) {
            $trimmedPart = trim($part);
            if ($trimmedPart === '..') {
                if (!($lastPath instanceof DirectoryPath)) {
                    throw new Exception('Exceeding root level');
                }
                $lastPath = $lastPath->getParent();
            } elseif ($trimmedPart !== "" && $trimmedPart !== '.') {
                $directoryPath = new DirectoryPath($trimmedPart, $lastPath);
                $lastPath = $directoryPath;
            }
        }

        return $lastPath;
    }

    public function withParent(RootPath|DirectoryPath $parent): DirectoryPath
    {
        return new DirectoryPath($this->name, $parent);
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): PathType
    {
        return PathType::DIRECTORY;
    }
}
