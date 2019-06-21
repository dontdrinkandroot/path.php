<?php


namespace Dontdrinkandroot\Path;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
abstract class AbstractPath implements Path
{
    /**
     * @var DirectoryPath
     */
    protected $parentPath;

    /**
     * {@inheritdoc}
     */
    public function hasParentPath(): bool
    {
        return (null !== $this->parentPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getParentPath(): DirectoryPath
    {
        return $this->parentPath;
    }

    /**
     * {@inheritdoc}
     */
    public function collectPaths(): array
    {
        if (!$this->hasParentPath()) {
            return [$this];
        }

        return array_merge($this->getParentPath()->collectPaths(), [$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function isFilePath(): bool
    {
        return !$this->isDirectoryPath();
    }

    /**
     * {@inheritdoc}
     */
    public function toAbsoluteUrlString(): string
    {
        return $this->toAbsoluteString('/');
    }

    /**
     * {@inheritdoc}
     */
    public function toRelativeUrlString(): string
    {
        return $this->toRelativeString('/');
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

    /**
     * @param DirectoryPath $path
     */
    public function setParentPath(DirectoryPath $path)
    {
        $this->parentPath = $path;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toAbsoluteString();
    }
}
