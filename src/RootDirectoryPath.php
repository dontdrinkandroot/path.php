<?php

namespace Dontdrinkandroot\Path;

use Override;

class RootDirectoryPath extends DirectoryPath
{
    #[Override]
    public function appendDirectory(string $name): ChildDirectoryPath
    {
        self::assertValidName($name);

        return new ChildDirectoryPath($name, $this->clone());
    }

    #[Override]
    public function appendFile(string $name): FilePath
    {
        self::assertValidName($name);

        return new FilePath($name, $this->clone());
    }

    #[Override]
    public function toAbsoluteString(string $separator = '/'): string
    {
        return $separator;
    }

    #[Override]
    public function toRelativeString(string $separator = '/'): string
    {
        return '';
    }

    #[Override]
    public function collectPaths(): array
    {
        return [$this];
    }

    #[Override]
    public function prepend(DirectoryPath $path): DirectoryPath
    {
        return $path->clone();
    }

    #[Override]
    public function getName(): ?string
    {
        return null;
    }

    #[Override]
    public function getParent(): ?DirectoryPath
    {
        return null;
    }

    #[Override]
    public function clone(): RootDirectoryPath
    {
        return new RootDirectoryPath();
    }
}
