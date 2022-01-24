<?php

namespace Dontdrinkandroot\Path;

class ChildDirectoryPath extends DirectoryPath implements ChildPath
{
    public function __construct(
        public readonly string $name,
        public readonly DirectoryPath $parent = new RootDirectoryPath()
    ) {
        PathUtils::assertValidName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function appendDirectory(string $name): ChildDirectoryPath
    {
        PathUtils::assertValidName($name);

        return new ChildDirectoryPath($name, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function appendFile(string $name): FilePath
    {
        PathUtils::assertValidName($name);

        return new FilePath($name, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function toRelativeString(string $separator = '/'): string
    {
        return $this->parent->toRelativeString() . $this->name . $separator;
    }

    /**
     * {@inheritdoc}
     */
    public function toAbsoluteString(string $separator = '/'): string
    {
        return $this->parent->toAbsoluteString() . $this->name . $separator;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getParent(): DirectoryPath
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function collectPaths(): array
    {
        return [...$this->parent->collectPaths(), $this];
    }

    /**
     * {@inheritdoc}
     */
    public function clone(): ChildDirectoryPath
    {
        return new ChildDirectoryPath($this->name, $this->parent->clone());
    }
}
