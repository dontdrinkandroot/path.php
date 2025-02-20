<?php

namespace Dontdrinkandroot\Path;

use Override;

class ChildDirectoryPath extends DirectoryPath implements ChildPath
{
    public function __construct(
        public readonly string $name,
        public readonly DirectoryPath $parent = new RootDirectoryPath()
    ) {
        self::assertValidName($name);
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[Override]
    public function appendDirectory(string $name): ChildDirectoryPath
    {
        self::assertValidName($name);

        return new ChildDirectoryPath($name, $this);
    }

    #[Override]
    public function appendFile(string $name): FilePath
    {
        self::assertValidName($name);

        return new FilePath($name, $this);
    }

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
    public function prepend(DirectoryPath $path): ChildDirectoryPath
    {
        $directoryPath = DirectoryPath::parse($path->toAbsoluteString() . $this->toAbsoluteString());
        assert($directoryPath instanceof ChildDirectoryPath);

        return $directoryPath;
    }

    public function withParent(RootDirectoryPath|ChildDirectoryPath $parent): ChildDirectoryPath
    {
        return new ChildDirectoryPath($this->name, $parent);
    }

    #[Override]
    public function getParent(): DirectoryPath
    {
        return $this->parent;
    }

    #[Override]
    public function collectPaths(): array
    {
        return [...$this->parent->collectPaths(), $this];
    }

    #[Override]
    public function clone(): ChildDirectoryPath
    {
        return new ChildDirectoryPath($this->name, $this->parent->clone());
    }
}
