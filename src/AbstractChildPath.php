<?php

namespace Dontdrinkandroot\Path;

abstract class AbstractChildPath extends AbstractPath implements ChildPath
{
    public function __construct(
        public readonly string $name,
        public readonly RootPath|DirectoryPath $parent = new RootPath()
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): RootPath|DirectoryPath
    {
        return $this->parent;
    }
}
