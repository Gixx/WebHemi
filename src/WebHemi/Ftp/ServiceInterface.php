<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Ftp;

use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    public const FILE_MODE_TEXT = FTP_ASCII;
    public const FILE_MODE_BINARY = FTP_BINARY;

    /**
     * ServiceInterface constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration);

    /**
     * Connect and login to remote host.
     *
     * @return ServiceInterface
     */
    public function connect() : ServiceInterface;

    /**
     * Disconnect from remote host.
     *
     * @return ServiceInterface
     */
    public function disconnect() : ServiceInterface;

    /**
     * Sets an option data.
     *
     * @param string $key
     * @param mixed $value
     * @return ServiceInterface
     */
    public function setOption(string $key, $value) : ServiceInterface;

    /**
     * Sets a group of options.
     *
     * @param array $options
     * @return ServiceInterface
     */
    public function setOptions(array $options) : ServiceInterface;

    /**
     * Gets a specific option data.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default = null);

    /**
     * Toggles connection security level.
     *
     * @param bool $state
     * @return ServiceInterface
     */
    public function setSecureConnection(bool $state) : ServiceInterface;

    /**
     * Toggles connection passive mode.
     *
     * @param bool $state
     * @return ServiceInterface
     */
    public function setPassiveMode(bool $state) : ServiceInterface;

    /**
     * Sets remote path.
     *
     * @param string $path
     * @return ServiceInterface
     */
    public function setRemotePath(string $path) : ServiceInterface;

    /**
     * Gets remote path.
     *
     * @return string
     */
    public function getRemotePath() : string;

    /**
     * Sets local path.
     *
     * @param string $path
     * @return ServiceInterface
     */
    public function setLocalPath(string $path) : ServiceInterface;

    /**
     * Gets local path.
     *
     * @return string
     */
    public function getLocalPath() : string;

    /**
     * Lists remote path.
     *
     * @param null|string $path
     * @param bool|null $changeToDirectory
     * @return array
     */
    public function getRemoteFileList(? string $path, ? bool $changeToDirectory) : array;

    /**
     * Uploads file to remote host.
     *
     * @see self::setRemotePath
     * @see self::setLocalPath
     *
     * @param string $sourceFileName
     * @param string $destinationFileName
     * @param int $fileMode
     * @return mixed
     */
    public function upload(
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
     * @param string $remoteFileName
     * @param string $localFileName
     * @param int $fileMode
     * @return mixed
     */
    public function download(
        string $remoteFileName,
        string&$localFileName,
        int $fileMode = self::FILE_MODE_BINARY
    ) : ServiceInterface;

    /**
     * Moves file on remote host.
     *
     * @param string $currentPath
     * @param string $newPath
     * @return ServiceInterface
     */
    public function moveRemoteFile(string $currentPath, string $newPath) : ServiceInterface;

    /**
     * Deletes file on remote host.
     *
     * @param string $path
     * @return ServiceInterface
     */
    public function deleteRemoteFile(string $path) : ServiceInterface;
}
