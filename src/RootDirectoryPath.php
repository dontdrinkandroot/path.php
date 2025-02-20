<?php

namespace Dontdrinkandroot\Path;

use Override;

class RootDirectoryPath extends Path implements DirectoryPathInterface
{
    use DirectoryPathTrait;

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
    public function prepend(DirectoryPathInterface $path): PathInterface
    {
        return $path->clone();
    }

    #[Override]
    public function clone(): RootDirectoryPath
    {
        return new RootDirectoryPath();
    }

    #[Override]
    public function isChildPath(): bool
    {
        return false;
    }
}
