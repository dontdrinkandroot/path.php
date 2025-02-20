<?php

namespace Dontdrinkandroot\Path;

use Override;

class ChildDirectoryPath extends ChildPath implements DirectoryPathInterface
{
    use DirectoryPathTrait;

    #[Override]
    public function toRelativeString(string $separator = '/'): string
    {
        return $this->parent->toRelativeString() . $this->name . $separator;
    }

    #[Override]
    public function toAbsoluteString(string $separator = '/'): string
    {
        return $this->parent->toAbsoluteString() . $this->name . $separator;
    }

    #[Override]
    public function prepend(DirectoryPathInterface $path): ChildPathInterface&DirectoryPathInterface
    {
        $directoryPath = DirectoryPath::parse($path->toAbsoluteString() . $this->toAbsoluteString());
        assert($directoryPath instanceof ChildPathInterface);

        return $directoryPath;
    }

    public function withParent(RootDirectoryPath|ChildDirectoryPath $parent): ChildDirectoryPath
    {
        return new ChildDirectoryPath($this->name, $parent);
    }

    #[Override]
    public function clone(): DirectoryPathInterface&ChildPathInterface
    {
        return new ChildDirectoryPath($this->name, $this->parent->clone());
    }
}
