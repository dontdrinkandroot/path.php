<?php

namespace Dontdrinkandroot\Path;

abstract class AbstractChildPath extends AbstractPath
{
    public function __construct(
        public readonly string $name,
        public readonly RootPath|DirectoryPath $parent = new RootPath()
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function isRootPath(): bool
    {
        return false;
    }
}
