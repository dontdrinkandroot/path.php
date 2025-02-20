<?php

namespace Dontdrinkandroot\Path;

use Stringable;

interface PathInterface extends Stringable
{
    public function toAbsoluteString(string $separator = '/'): string;

    public function toRelativeString(string $separator = '/'): string;

    public function toAbsoluteUrlString(): string;

    public function toRelativeUrlString(): string;

    public function toAbsoluteFileSystemString(): string;

    public function toRelativeFileSystemString(): string;

    public function diff(PathInterface $other, string $separator = '/'): string;

    public function prepend(DirectoryPathInterface $path): PathInterface;

    /** @return list<PathInterface> */
    public function collectPaths(): array;

    public function resolveNearestDirectoryPath(): DirectoryPathInterface;

    public function isDirectoryPath(): bool;

    public function isChildPath(): bool;

    public function getType(): PathType;

    public function clone(): PathInterface;
}
