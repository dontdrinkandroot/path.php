<?php

namespace Dontdrinkandroot\Path;

/**
 * MMarker interface indicating whether the path can have children.
 */
interface ParentPath extends Path
{
    public function appendDirectory(string $name): DirectoryPath;

    public function appendFile(string $name): FilePath;
}
