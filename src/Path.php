<?php

namespace Dontdrinkandroot\Path;

interface Path
{
    /** @return list<Path> */
    public function collectPaths(): array;

    public function toAbsoluteUrlString(): string;

    public function toRelativeUrlString(): string;

    public function toAbsoluteFileSystemString(): string;

    public function toRelativeFileSystemString(): string;

    public function toAbsoluteString(string $separator = '/'): string;

    public function toRelativeString(string $separator = '/'): string;
}
