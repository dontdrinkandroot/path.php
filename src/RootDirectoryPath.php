<?php

namespace Dontdrinkandroot\Path;

class RootDirectoryPath extends DirectoryPath
{
    /**
     * {@inheritdoc}
     */
    public function appendDirectory(string $name): ChildDirectoryPath
    {
        PathUtils::assertValidName($name);

        return new ChildDirectoryPath($name, clone $this);
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
    public function collectPaths(): array
    {
        return [$this];
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ChildDirectoryPath $path): ChildDirectoryPath
    {
        return clone $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?DirectoryPath
    {
        return null;
    }
}
