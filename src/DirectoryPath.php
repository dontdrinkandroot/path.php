<?php

namespace Dontdrinkandroot\Path;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class DirectoryPath extends AbstractPath
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     *
     * @throws \Exception Thrown if name contains invalid characters.
     */
    public function __construct(string $name = null)
    {
        if (strpos($name, '/') !== false) {
            throw new \Exception('Name must not contain /');
        }

        if (!empty($name)) {
            $this->name = $name;
            $this->parentPath = new DirectoryPath();
        }
    }

    /**
     * @param string $name
     *
     * @return DirectoryPath
     * @throws \Exception Thrown if appending directory name fails.
     */
    public function appendDirectory(string $name): DirectoryPath
    {
        if (empty($name)) {
            throw new \Exception('Name must not be empty');
        }

        if (strpos($name, '/') !== false) {
            throw new \Exception('Name must not contain /');
        }

        $directoryPath = new DirectoryPath($name);
        $directoryPath->setParentPath($this);

        return $directoryPath;
    }

    /**
     * @param string $name
     *
     * @return FilePath
     * @throws \Exception Thrown if appending file name fails.
     */
    public function appendFile(string $name): FilePath
    {
        if (empty($name)) {
            throw new \Exception('Name must not be empty');
        }

        if (strpos($name, '/') !== false) {
            throw new \Exception('Name must not contain /');
        }

        $filePath = new FilePath($name);
        $filePath->setParentPath($this);

        return $filePath;
    }

    /**
     * {@inheritdoc}
     */
    public function toRelativeString(string $separator = '/'): string
    {
        if (null === $this->parentPath) {
            return '';
        }

        return $this->parentPath->toRelativeString() . $this->name . $separator;
    }

    /**
     * {@inheritdoc}
     */
    public function toAbsoluteString(string $separator = '/'): string
    {
        if (null === $this->parentPath) {
            return $separator;
        }

        return $this->parentPath->toAbsoluteString() . $this->name . $separator;
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(DirectoryPath $path): Path
    {
        return DirectoryPath::parse($path->toAbsoluteString() . $this->toAbsoluteString());
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return null === $this->parentPath && null === $this->name;
    }

    /**
     * @param string $pathString
     * @param string $separator
     *
     * @return DirectoryPath
     * @throws \Exception
     */
    public static function parse($pathString, $separator = '/')
    {
        if (empty($pathString)) {
            return new DirectoryPath();
        }

        if (!(PathUtils::getLastChar($pathString) === $separator)) {
            throw new \Exception('Path String must end with ' . $separator);
        }

        return self::parseDirectoryPath($pathString, new DirectoryPath(), $separator);
    }

    /**
     * {@inheritdoc}
     */
    public function isDirectoryPath(): bool
    {
        return true;
    }

    /**
     * @param string $pathString
     *
     * @return DirectoryPath|FilePath
     * @throws \Exception
     */
    public function appendPathString(string $pathString): Path
    {
        if (empty($pathString)) {
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
            $filePath = new FilePath($filePart);
            $filePath->setParentPath($directoryPath);

            return $filePath;
        }

        return $directoryPath;
    }

    /**
     * @param string|null   $pathString
     * @param DirectoryPath $rootPath
     * @param string        $separator
     *
     * @return DirectoryPath
     * @throws \Exception
     */
    protected static function parseDirectoryPath(
        ?string $pathString,
        DirectoryPath $rootPath,
        string $separator = '/'
    ): DirectoryPath {
        $lastPath = $rootPath;
        if (null !== $pathString) {
            $parts = explode($separator, $pathString);
            foreach ($parts as $part) {
                $trimmedPart = trim($part);
                if ($trimmedPart === '..') {
                    if (!$lastPath->hasParentPath()) {
                        throw new \Exception('Exceeding root level');
                    }
                    $lastPath = $lastPath->getParentPath();
                } else {
                    if ($trimmedPart !== "" && $trimmedPart !== '.') {
                        $directoryPath = new DirectoryPath($trimmedPart);
                        if (null !== $lastPath) {
                            $directoryPath->setParentPath($lastPath);
                        }
                        $lastPath = $directoryPath;
                    }
                }
            }
        }

        return $lastPath;
    }
}
