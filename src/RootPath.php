<?php

namespace Dontdrinkandroot\Path;

class RootPath extends AbstractPath
{
    /**
     * {@inheritdoc}
     */
    public function toAbsoluteString(string $separator = '/'): string
    {
        return $separator;
    }

    /**
     * {@inheritdoc}
     */
    public function toRelativeString(string $separator = '/'): string
    {
        return '';
    }
}
