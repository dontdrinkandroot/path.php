<?php

namespace Dontdrinkandroot\Path;

enum PathType: string
{
    case ROOT = 'root';
    case DIRECTORY = 'directory';
    case FILE = 'file';
}
