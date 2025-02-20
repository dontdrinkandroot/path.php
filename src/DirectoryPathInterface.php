<?php

namespace Dontdrinkandroot\Path;

use Override;

interface DirectoryPathInterface extends PathInterface
{
    /**
     * @param non-empty-string $name
     */
    public function appendDirectory(string $name): DirectoryPathInterface&ChildPathInterface;

    /**
     * @param non-empty-string $name
     */
    public function appendFile(string $name): FilePathInterface;

    public function appendPathString(string $pathString): PathInterface;

    #[Override]
    public function clone(): DirectoryPathInterface;
}
