<?php

namespace Dontdrinkandroot\Path;

interface ChildPath extends Path
{
    public function getParent(): ParentPath;

    public function prepend(DirectoryPath $path): Path;
}
