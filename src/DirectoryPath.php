<?php

namespace Dontdrinkandroot\Path;

use Exception;
use InvalidArgumentException;

class DirectoryPath extends AbstractChildPath
{
    /**
     * @throws InvalidArgumentException Thrown if name contains invalid characters.
     */
    public function __construct(
        string $name,
        RootPath|DirectoryPath $parent = new RootPath()
    ) {
        parent::__construct($name, $parent);
        if ('' === $this->name) {
            throw new InvalidArgumentException('Name must not be empty');
        }

        if (str_contains($name, '/')) {
            throw new InvalidArgumentException('Name must not contain /');
        }
    }

    /**
     * @param string $name
     *
     * @return DirectoryPath
     * @throws InvalidArgumentException Thrown if appending directory name fails.
     */
    public function appendDirectory(string $name): DirectoryPath
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Name must not be empty');
        }

        if (str_contains($name, '/')) {
            throw new InvalidArgumentException('Name must not contain /');
        }

        return new DirectoryPath($name, $this);
    }

    /**
     * @param string $name
     *
     * @return FilePath
     * @throws InvalidArgumentException Thrown if appending file name fails.
     */
    public function appendFile(string $name): FilePath
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Name must not be empty');
        }

        if (str_contains($name, '/')) {
            throw new InvalidArgumentException('Name must not contain /');
        }

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
    public function prepend(DirectoryPath $path): Path
    {
        return DirectoryPath::parse($path->toAbsoluteString() . $this->toAbsoluteString());
    }

    /**
     * @param string $pathString
     * @param string $separator
     *
     * @return DirectoryPath|RootPath
     * @throws InvalidArgumentException
     */
    public static function parse(string $pathString, string $separator = '/'): DirectoryPath|RootPath
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
     * @return DirectoryPath|RootPath
     * @throws Exception
     */
    protected static function parseDirectoryPath(
        ?string $pathString,
        DirectoryPath|RootPath $rootPath,
        string $separator = '/'
    ): DirectoryPath|RootPath {
        if (null === $pathString) {
            return $rootPath;
        }

        $lastPath = $rootPath;
        $parts = explode($separator, $pathString);
        foreach ($parts as $part) {
            $trimmedPart = trim($part);
            if ($trimmedPart === '..') {
                if ($lastPath instanceof RootPath) {
                    throw new Exception('Exceeding root level');
                }
                $lastPath = $lastPath->parent;
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
