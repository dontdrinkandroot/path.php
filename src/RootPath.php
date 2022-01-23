<?php

namespace Dontdrinkandroot\Path;

class RootPath extends AbstractPath implements ParentPath
{
    /**
     * {@inheritdoc}
     */
    public function appendDirectory(string $name): DirectoryPath
    {
        PathUtils::assertValidName($name);

        return new DirectoryPath($name, clone $this);
    }

    /**
     * {@inheritdoc}
     */
    public function appendFile(string $name): FilePath
    {
        PathUtils::assertValidName($name);

        return new FilePath($name, clone $this);
    }

    /**
     * {@inheritdoc}
     */
    public function toAbsoluteString(string $separator = '/'): string
    {
        return $separator;
    }

    /**
     * {@inheritdoc}
     */
    public function toRelativeString(string $separator = '/'): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): PathType
    {
        return PathType::ROOT;
    }
}
