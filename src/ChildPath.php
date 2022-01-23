<?php

namespace Dontdrinkandroot\Path;

interface ChildPath
{
    public function getParent(): DirectoryPath;
}
