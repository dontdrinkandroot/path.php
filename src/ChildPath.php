<?php

namespace Dontdrinkandroot\Path;

interface ChildPath
{
    public function getName(): string;

    public function getParent(): DirectoryPath;
}
