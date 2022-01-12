<?php

namespace Dontdrinkandroot\Path;

use InvalidArgumentException;

class FilePath extends AbstractChildPath
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $name,
        RootPath|DirectoryPath $parent = new RootPath()
    ) {
        parent::__construct($name, $parent);
        if (empty($name)) {
            throw new InvalidArgumentException('Name must not be empty');
        }

        if (str_contains($name, '/')) {
            throw new InvalidArgumentException('Name must not contain /');
        }
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

    /**
     * {@inheritdoc}
     */
    public function toRelativeString(string $separator = '/'): string
    {
        return $this->parent->toRelativeString($separator) . $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function toAbsoluteString(string $separator = '/'): string
    {
        return $this->parent->toAbsoluteString($separator) . $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(DirectoryPath $path): Path
    {
        return self::parse($path->toAbsoluteString() . $this->toAbsoluteString());
    }

    /**
     * @param string $pathString
     * @param string $separator
     *
     * @return FilePath
     * @throws InvalidArgumentException
     */
    public static function parse(string $pathString, string $separator = '/'): FilePath
    {
        if ('' === $pathString) {
            throw new InvalidArgumentException('Path String must not be empty');
        }

        if (PathUtils::getLastChar($pathString) === $separator) {
            throw new InvalidArgumentException('Path String must not end with ' . $separator);
        }

        $directoryPart = null;
        $filePart = $pathString;
        $lastSlashPos = strrpos($pathString, $separator);
        if (false !== $lastSlashPos) {
            $directoryPart = substr($pathString, 0, $lastSlashPos + 1);
            $filePart = substr($pathString, $lastSlashPos + 1);
        }

        $filePath = new FilePath($filePart);

        if (null !== $directoryPart) {
            return $filePath->withParent(DirectoryPath::parse($directoryPart, $separator));
        }

        return $filePath;
    }

    public function withParent(RootPath|DirectoryPath $parent): FilePath
    {
        return new FilePath($this->name, $parent);
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): PathType
    {
        return PathType::FILE;
    }
}
