<?php

namespace Dontdrinkandroot\Path;

class PathUtils
{
    public static function getPathDiff(
        RootPath|DirectoryPath|FilePath $fromPath,
        RootPath|DirectoryPath|FilePath $toPath,
        string $separator = '/'
    ): string {
        $fromDirectoryPath = ($fromPath instanceof FilePath) ? $fromPath->parent : $fromPath;
        $toDirectoryPath = ($toPath instanceof FilePath) ? $toPath->parent : $toPath;

        $pathDiff = static::getDirectoryPathDiff($fromDirectoryPath, $toDirectoryPath, $separator);
        if ($toPath instanceof FilePath) {
            $pathDiff .= $toPath->name;
        }

        return $pathDiff;
    }

    public static function getDirectoryPathDiff(
        RootPath|DirectoryPath $fromPath,
        RootPath|DirectoryPath $toPath,
        string $separator = '/'
    ): string {
        $fromParts = static::getDirectoryPathParts($fromPath);
        $toParts = static::getDirectoryPathParts($toPath);

        $fromDepth = count($fromParts);
        $toDepth = count($toParts);

        $equalUpToIndex = 0;
        while ($fromDepth > $equalUpToIndex
            && $toDepth > $equalUpToIndex
            && $fromParts[$equalUpToIndex] === $toParts[$equalUpToIndex]
        ) {
            $equalUpToIndex++;
        }

        $moveUp = $fromDepth - $equalUpToIndex;
        $result = str_repeat('..' . $separator, $moveUp);

        for ($i = $equalUpToIndex; $i < $toDepth; $i++) {
            $result .= $toParts[$i] . $separator;
        }

        return $result;
    }

    /**
     * @param RootPath|DirectoryPath $path
     *
     * @return string[]
     */
    public static function getDirectoryPathParts(RootPath|DirectoryPath $path): array
    {
        $currentPath = $path;
        $parts = [];
        while ($currentPath instanceof DirectoryPath) {
            $parts[] = $currentPath->name;
            $currentPath = $currentPath->parent;
        }

        return array_reverse($parts);
    }

    /**
     * Checks if a string ends with another string.
     *
     * @param string $haystack The string to search in.
     * @param string $needle   The string to search.
     *
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle): bool
    {
        return $needle === "" || str_ends_with($haystack, $needle);
    }

    /**
     * Get the last character of a string.
     *
     * @param string false The string to get the last character of.
     *
     * @return string|null The last character or null if not found.
     */
    public static function getLastChar(string $str): ?string
    {
        if ($str === '') {
            return null;
        }
        return substr($str, -1);
    }
}
