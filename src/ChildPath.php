<?php

namespace Dontdrinkandroot\Path;

interface ChildPath extends Path
{
    public function getParent(): RootPath|DirectoryPath;
}
