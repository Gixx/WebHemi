<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Ftp\ServiceAdapter;

use RuntimeException;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Ftp\ServiceInterface;

/**
 * Class AbstractServiceAdapter
 */
abstract class AbstractServiceAdapter implements ServiceInterface
{
    /**
     * @var string
     */
    protected $localPath = __DIR__;
    /**
     * @var array
     */
    protected $options = [];

    /**
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->setOptions($configuration->getData('ftp'));
    }

    /**
     * Connect and login to remote host.
     *
     * @return ServiceInterface
     */
    abstract public function connect() : ServiceInterface;

    /**
     * Disconnect from remote host.
     *
     * @return ServiceInterface
     */
    abstract public function disconnect() : ServiceInterface;

    /**
     * Sets an option data.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return ServiceInterface
     */
    public function setOption(string $key, $value) : ServiceInterface
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * Sets a group of options.
     *
     * @param  array $options
     * @return ServiceInterface
     */
    public function setOptions(array $options) : ServiceInterface
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * Gets a specific option data.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Toggles connection security level.
     *
     * @param  bool $state
     * @return ServiceInterface
     */
    abstract public function setSecureConnection(bool $state) : ServiceInterface;

    /**
     * Toggles connection passive mode.
     *
     * @param  bool $state
     * @return ServiceInterface
     */
    abstract public function setPassiveMode(bool $state) : ServiceInterface;

    /**
     * Sets remote path.
     *
     * @param  string $path
     * @return ServiceInterface
     */
    abstract public function setRemotePath(string $path) : ServiceInterface;

    /**
     * Gets remote path.
     *
     * @return string
     */
    abstract public function getRemotePath() : string;

    /**
     * Sets local path.
     *
     * @param  string $path
     * @return ServiceInterface
     */
    public function setLocalPath(string $path) : ServiceInterface
    {
        // if it's not an absolute path, we take it relative to the current folder
        if (strpos($path, '/') !== 0) {
            $path = __DIR__.'/'.$path;
        }

        if (!realpath($path) || !is_dir($path)) {
            throw new RuntimeException(sprintf('No such directory: %s', $path), 1003);
        }

        // @codeCoverageIgnoreStart
        // docker is root. Can't test these cases...
        if (!is_readable($path)) {
            throw new RuntimeException(sprintf('Cannot read directory: %s; Permission denied.', $path), 1004);
        }

        if (!is_writable($path)) {
            throw new RuntimeException(
                sprintf('Cannot write data into directory: %s; Permission denied.', $path),
                1005
            );
        }
        // @codeCoverageIgnoreEnd

        $this->localPath = $path;

        return $this;
    }

    /**
     * Checks local file, and generates new unique name if necessary.
     *
     * @param  string $localFileName
     * @param  bool   $forceUnique
     * @throws RuntimeException
     */
    protected function checkLocalFile(string&$localFileName, bool $forceUnique = false) : void
    {
        $pathInfo = pathinfo($localFileName);

        if ($pathInfo['dirname'] != '.') {
            $this->setLocalPath($pathInfo['dirname']);
        }

        $localFileName = $pathInfo['basename'];

        if (!$forceUnique) {
            return;
        }

        $variant = 0;
        $fileName = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        while (file_exists($this->localPath.'/'.$fileName.'.'.$extension) && $variant++ < 20) {
            $fileName = $pathInfo['filename'].'.('.($variant).')';
        }

        if (preg_match('/\.\(20\)$/', $fileName)) {
            throw new RuntimeException(
                sprintf('Too many similar files in folder %s, please cleanup first.', $this->localPath),
                1009
            );
        }

        $localFileName = $fileName.'.'.$extension;
    }

    /**
     * Converts file rights string into octal value.
     *
     * @param  string $permissions The UNIX-style permission string, e.g.: 'drwxr-xr-x'
     * @return string
     */
    protected function getOctalChmod(string $permissions) : string
    {
        $mode = 0;
        $mapper = [
            0 => [], // type like d as directory, l as link etc.
            // Owner
            1 => ['r' => 0400],
            2 => ['w' => 0200],
            3 => [
                'x' => 0100,
                's' => 04100,
                'S' => 04000
            ],
            // Group
            4 => ['r' => 040],
            5 => ['w' => 020],
            6 => [
                'x' => 010,
                's' => 02010,
                'S' => 02000
            ],
            // World
            7 => ['r' => 04],
            8 => ['w' => 02],
            9 => [
                'x' => 01,
                't' => 01001,
                'T' => 01000
            ],
        ];

        for ($i = 1; $i <= 9; $i++) {
            $mode += $mapper[$i][$permissions[$i]] ?? 00;
        }

        return '0'.decoct($mode);
    }

    /**
     * Check remote file name.
     *
     * @param string $remoteFileName
     */
    abstract protected function checkRemoteFile(string&$remoteFileName) : void;

    /**
     * Gets local path.
     *
     * @return string
     */
    public function getLocalPath() : string
    {
        return $this->localPath;
    }

    /**
     * Lists remote path.
     *
     * @param  null|string $path
     * @param  bool|null   $changeToDirectory
     * @return array
     */
    abstract public function getRemoteFileList(? string $path, ? bool $changeToDirectory) : array;

    /**
     * Uploads file to remote host.
     *
     * @see self::setRemotePath
     * @see self::setLocalPath
     *
     * @param  string $sourceFileName
     * @param  string $destinationFileName
     * @param  int    $fileMode
     * @return ServiceInterface
     */
    abstract public function upload(
        string $sourceFileName,
        string $destinationFileName,
        int $fileMode = self::FILE_MODE_BINARY
    ) : ServiceInterface;

    /**
     * Downloads file from remote host.
     *
     * @see self::setRemotePath
     * @see self::setLocalPath
     *
     * @param  string $remoteFileName
     * @param  string $localFileName
     * @param  int    $fileMode
     * @return ServiceInterface
     */
    abstract public function download(
        string $remoteFileName,
        string&$localFileName,
        int $fileMode = self::FILE_MODE_BINARY
    ) : ServiceInterface;

    /**
     * Moves file on remote host.
     *
     * @param  string $currentPath
     * @param  string $newPath
     * @return ServiceInterface
     */
    abstract public function moveRemoteFile(string $currentPath, string $newPath) : ServiceInterface;

    /**
     * Deletes file on remote host.
     *
     * @param  string $path
     * @return ServiceInterface
     */
    abstract public function deleteRemoteFile(string $path) : ServiceInterface;
}
