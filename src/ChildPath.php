<?php

namespace Dontdrinkandroot\Path;

use Override;

abstract class ChildPath extends Path implements ChildPathInterface
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        public readonly string $name,
        public readonly DirectoryPathInterface $parent = new RootDirectoryPath()
    ) {
        self::assertValidName($name);
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[Override]
    public function getParent(): DirectoryPathInterface
    {
        return $this->parent;
    }

    #[Override]
    public function collectPaths(): array
    {
        return [...$this->parent->collectPaths(), $this];
    }

    #[Override]
    public function isChildPath(): bool
    {
        return true;
    }
}
