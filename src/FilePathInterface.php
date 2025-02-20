<?php

namespace Dontdrinkandroot\Path;

use Override;

interface FilePathInterface extends ChildPathInterface
{
    public function getFileName(): string;

    public function getExtension(): ?string;

    #[Override]
    public function clone(): FilePathInterface;
}
