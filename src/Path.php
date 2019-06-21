<?php

namespace Dontdrinkandroot\Path;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
interface Path
{
    public function getName(): ?string;

    public function hasParentPath(): bool;

    public function getParentPath(): DirectoryPath;

    public function prepend(DirectoryPath $path): Path;

    /**
     * @return Path[]
     */
    public function collectPaths(): array;

    public function toAbsoluteUrlString(): string;

    public function toRelativeUrlString(): string;

    public function toAbsoluteFileSystemString(): string;

    public function toRelativeFileSystemString(): string;

    public function toAbsoluteString(string $separator = '/'): string;

    public function toRelativeString(string $separator = '/'): string;

    public function isFilePath(): bool;

    public function isDirectoryPath(): bool;
}
