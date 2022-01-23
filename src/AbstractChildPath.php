<?php

namespace Dontdrinkandroot\Path;

abstract class AbstractChildPath extends AbstractPath implements ChildPath
{
    public function __construct(
        public readonly string $name,
        public readonly ParentPath $parent = new RootPath()
    ) {
        PathUtils::assertValidName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ParentPath
    {
        return $this->parent;
    }
}
