<?php

namespace Dontdrinkandroot\Path;

use RuntimeException;

abstract class AbstractPath implements Path
{
    /**
     * {@inheritdoc}
     */
    public function collectPaths(): array
    {
        if ($this instanceof RootPath) {
            return [$this];
        }

        if (($this instanceof AbstractChildPath)) {
            return [...$this->parent->collectPaths(), $this];
        }

        throw new RuntimeException('Invalid path implementation');
    }

    /**
     * {@inheritdoc}
     */
    public function toAbsoluteUrlString(): string
    {
        return $this->toAbsoluteString('/');
    }

    /**
     * {@inheritdoc}
     */
    public function toRelativeUrlString(): string
    {
        return $this->toRelativeString('/');
    }

    /**
     * {@inheritdoc}
     */
    public function toAbsoluteFileSystemString(): string
    {
        return $this->toAbsoluteString(DIRECTORY_SEPARATOR);
    }

    /**
     * {@inheritdoc}
     */
    public function toRelativeFileSystemString(): string
    {
        return $this->toRelativeString(DIRECTORY_SEPARATOR);
    }

    public function __toString(): string
    {
        return $this->toAbsoluteString();
    }
}
