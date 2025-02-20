<?php

namespace Dontdrinkandroot\Path;

use Exception;
use InvalidArgumentException;
use Override;

abstract class DirectoryPath extends Path implements DirectoryPathInterface
{
    /**
     * @param non-empty-string $separator
     * @throws Exception
     */
    public static function parseDirectoryPath(
        string $pathString,
        DirectoryPathInterface $relativeToPath,
        string $separator = '/'
    ): DirectoryPathInterface {
        $lastPath = $relativeToPath->clone();
        $parts = explode($separator, $pathString);
        foreach ($parts as $part) {
            $trimmedPart = trim($part);
            if ($trimmedPart === '..') {
                if (!($lastPath instanceof ChildDirectoryPath)) {
                    throw new Exception('Exceeding root level');
                }
                $lastPath = $lastPath->getParent();
            } elseif ($trimmedPart !== "" && $trimmedPart !== '.') {
                $directoryPath = new ChildDirectoryPath($trimmedPart, $lastPath);
                $lastPath = $directoryPath;
            }
        }

        return $lastPath;
    }

    /**
     * @param non-empty-string $separator
     */
    #[Override]
    public static function parse(string $pathString, string $separator = '/'): DirectoryPathInterface
    {
        if ('' === $pathString) {
            throw new InvalidArgumentException('Path String must not be empty');
        }

        if (!(str_ends_with($pathString, $separator))) {
            throw new InvalidArgumentException('Path String must end with ' . $separator);
        }

        return self::parseDirectoryPath($pathString, new RootDirectoryPath(), $separator);
    }

    #[Override]
    public function getType(): PathType
    {
        return PathType::DIRECTORY;
    }

    #[Override]
    abstract public function clone(): DirectoryPath;
}
