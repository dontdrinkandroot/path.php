<?php

namespace Dontdrinkandroot\Path;

interface ChildPathInterface extends PathInterface
{
    public function getName(): string;

    public function getParent(): DirectoryPathInterface;
}
