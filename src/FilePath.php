<?php


namespace Dontdrinkandroot\Path;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class FilePath extends AbstractPath
{
    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string|null
     */
    protected $extension;

    /**
     * @param string $name
     *
     * @throws \Exception
     */
    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new \Exception('Name must not be empty');
        }

        if (strpos($name, '/') !== false) {
            throw new \Exception('Name must not contain /');
        }

        $this->fileName = $name;
        $lastDotPos = strrpos($name, '.');
        if (false !== $lastDotPos && $lastDotPos > 0) {
            $this->fileName = substr($name, 0, $lastDotPos);
            $this->extension = substr($name, $lastDotPos + 1);
        }

        $this->parentPath = new DirectoryPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        $name = $this->fileName;
        if (null !== $this->extension) {
            $name .= '.' . $this->extension;
        }

        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function toRelativeString(string $separator = '/'): string
    {
        return $this->parentPath->toRelativeString($separator) . $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function toAbsoluteString(string $separator = '/'): string
    {
        return $this->parentPath->toAbsoluteString($separator) . $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(DirectoryPath $path): Path
    {
        return FilePath::parse($path->toAbsoluteString() . $this->toAbsoluteString());
    }

    /**
     * {@inheritdoc}
     */
    public function isDirectoryPath(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $pathString
     * @param string $separator
     *
     * @return FilePath
     * @throws \Exception
     */
    public static function parse(string $pathString, string $separator = '/'): FilePath
    {
        if (empty($pathString)) {
            throw new \Exception('Path String must not be empty');
        }

        if (PathUtils::getLastChar($pathString) === $separator) {
            throw new \Exception('Path String must not end with ' . $separator);
        }

        $directoryPart = null;
        $filePart = $pathString;
        $lastSlashPos = strrpos($pathString, $separator);
        if (false !== $lastSlashPos) {
            $directoryPart = substr($pathString, 0, $lastSlashPos + 1);
            $filePart = substr($pathString, $lastSlashPos + 1);
        }

        $filePath = new FilePath($filePart);

        if (null !== $directoryPart) {
            $filePath->setParentPath(DirectoryPath::parse($directoryPart, $separator));
        }

        return $filePath;
    }
}
