<?php

namespace Dontdrinkandroot\Path;

class RootDirectoryPath extends DirectoryPath
{
    /**
     * {@inheritdoc}
     */
    public function appendDirectory(string $name): ChildDirectoryPath
    {
        self::assertValidName($name);

        return new ChildDirectoryPath($name, $this->clone());
    }

    /**
     * {@inheritdoc}
     */
    public function appendFile(string $name): FilePath
    {
        self::assertValidName($name);

        return new FilePath($name, $this->clone());
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
    public function prepend(DirectoryPath $path): DirectoryPath
    {
        return $path->clone();
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

    /**
     * {@inheritdoc}
     */
    public function clone(): RootDirectoryPath
    {
        return new RootDirectoryPath();
    }
}
