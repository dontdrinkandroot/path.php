<?php

namespace Dontdrinkandroot\Path;

enum PathType: string
{
    case DIRECTORY = 'directory';
    case FILE = 'file';
}
