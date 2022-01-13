<?php

namespace Dontdrinkandroot\Path;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class PathUtils
{
    public static function getPathDiff(Path $fromPath, Path $toPath, string $separator = '/'): string
    {
        /** @var DirectoryPath $fromDirectoryPath */
        $fromDirectoryPath = $fromPath;
        if ($fromPath->isFilePath()) {
            $fromDirectoryPath = $fromPath->getParentPath();
        }

        /** @var DirectoryPath $toDirectoryPath */
        $toDirectoryPath = $toPath;
        if ($toPath->isFilePath()) {
            $toDirectoryPath = $toPath->getParentPath();
        }

        $pathDiff = static::getDirectoryPathDiff($fromDirectoryPath, $toDirectoryPath, $separator);
        if ($toPath->isFilePath()) {
            $pathDiff .= $toPath->getName();
        }

        return $pathDiff;
    }

    public static function getDirectoryPathDiff(
        DirectoryPath $fromPath,
        DirectoryPath $toPath,
        string $separator = '/'
    ): string {
        $result = '';

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
        for ($i = 0; $i < $moveUp; $i++) {
            $result .= '..' . $separator;
        }

        for ($i = $equalUpToIndex; $i < $toDepth; $i++) {
            $result .= $toParts[$i] . $separator;
        }

        return $result;
    }

    /**
     * @param DirectoryPath $path
     *
     * @return list<string>
     */
    public static function getDirectoryPathParts(DirectoryPath $path): array
    {
        $currentPath = $path;
        $parts = [];
        while ($currentPath->hasParentPath()) {
            if (null !== ($name = $currentPath->getName())) {
                $parts[] = $name;
                $currentPath = $currentPath->getParentPath();
            }
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
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * Get the last character of a string.
     *
     * @param string false The string to get the last character of.
     *
     * @return string|false The last character or false if not found.
     */
    public static function getLastChar($str)
    {
        $length = strlen($str);
        if ($length === 0) {
            return false;
        }

        return substr($str, $length - 1, 1);
    }
}
