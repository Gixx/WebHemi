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

namespace WebHemi\Ftp\ServiceAdapter\Base;

use RuntimeException;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Ftp\ServiceInterface;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter implements ServiceInterface
{
    /** @var resource */
    private $connectionId = null;
    /** @var string */
    private $localPath = __DIR__;
    /** @var array */
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
     * Disconnected by garbage collection.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Connect and login to remote host.
     *
     * @return ServiceInterface
     */
    public function connect() : ServiceInterface
    {
        if ($this->getOption('secure', true)) {
            $this->connectionId = @ftp_ssl_connect($this->getOption('host'));
        } else {
            $this->connectionId = @ftp_connect($this->getOption('host'));
        }

        if (!$this->connectionId) {
            throw new RuntimeException(
                sprintf('Cannot establish connection to server: %s', $this->getOption('host')),
                1000
            );
        }

        $loginResult = @ftp_login($this->connectionId, $this->getOption('username'), $this->getOption('password'));

        if (!$loginResult) {
            throw new RuntimeException('Cannot connect to remote host: invalid credentials', 1001);
        }

        return $this;
    }

    /**
     * Disconnect from remote host.
     *
     * @return ServiceInterface
     */
    public function disconnect() : ServiceInterface
    {
        if (!empty($this->connectionId)) {
            ftp_close($this->connectionId);
            $this->connectionId = null;
        }
    }

    /**
     * Sets an option data.
     *
     * @param string $key
     * @param mixed $value
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
     * @param array $options
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
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Toggles connection security level.
     *
     * @param bool $state
     * @return ServiceInterface
     */
    public function setSecureConnection(bool $state) : ServiceInterface
    {
        $this->setOption('secure', (bool) $state);

        if (!empty($this->connectionId)) {
            ftp_close($this->connectionId);
            $this->connectionId = null;
            $this->connect();
        }

        return $this;
    }

    /**
     * Toggles connection passive mode.
     *
     * @param bool $state
     * @return ServiceInterface
     */
    public function setPassiveMode(bool $state) : ServiceInterface
    {
        ftp_pasv($this->connectionId, (bool) $state);
        return $this;
    }

    /**
     * Sets remote path.
     *
     * @param string $path
     * @return ServiceInterface
     */
    public function setRemotePath(string $path) : ServiceInterface
    {
        if (trim($this->getRemotePath(), '/') == trim($path, '/')) {
            return $this;
        }

        if (strpos($path, '/') !== 0) {
            $path = $this->getRemotePath().$path;
        }

        $chdirResult = @ftp_chdir($this->connectionId, $path);

        if (!$chdirResult) {
            throw new RuntimeException(sprintf('No such directory on remote host: %s', $path), 1002);
        }

        return $this;
    }

    /**
     * Gets remote path.
     *
     * @return string
     */
    public function getRemotePath() : string
    {
        return ftp_pwd($this->connectionId).'/';
    }

    /**
     * Sets local path.
     *
     * @param string $path
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

        if (!is_readable($path)) {
            throw new RuntimeException(sprintf('Cannot read directory: %s; Permission denied.', $path), 1004);
        }

        if (!is_writable($path)) {
            throw new RuntimeException(
                sprintf('Cannot write data into directory: %s; Permission denied.', $path),
                1005
            );
        }

        $this->localPath = $path;

        return $this;
    }

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
     * @param null|string $path
     * @param bool|null $changeToDirectory
     * @return array
     */
    public function getRemoteFileList(? string $path, ? bool $changeToDirectory) : array
    {
        $fileList = [];

        if (!empty($path) && $changeToDirectory) {
            $this->setRemotePath($path);
            $path = '.';
        }

        $result = @ftp_rawlist($this->connectionId, $path);

        if (!is_array($result)) {
            throw new RuntimeException('Cannot retrieve file list', 1006);
        }

        foreach ($result as $fileRawData) {
            $fileData = [];

            preg_match(
                '/^(?P<rights>(?P<type>(-|d))[^\s]+)\s+(?P<symlinks>\d+)\s+(?P<user>[^\s]+)\s+(?P<group>[^\s]+)\s+'
                    .'(?P<size>\d+)\s+(?P<date>(?P<month>[^\s]+)\s+(?P<day>[^\s]+)\s+'
                    .'(?P<time>[^\s]+))\s+(?P<filename>.+)$/',
                $fileRawData,
                $fileData
            );
            $fileInfo = pathinfo($fileData['filename']);

            $fileList[] = [
                'type' => $this->getFileType($fileData['type']),
                'chmod' => $this->getOctalChmod($fileData['rights']),
                'symlinks' => $fileData['symlinks'],
                'user' => $fileData['user'],
                'group' => $fileData['group'],
                'size' => $fileData['size'],
                'date' => $this->getFileDate($fileData),
                'basename' => $fileInfo['basename'],
                'filename' => $fileInfo['filename'],
                'extension' => $fileInfo['extension'] ?? '',
            ];
        }

        return $fileList;
    }

    /**
     * @param string $fileData
     * @return string
     */
    private function getFileType(string $fileData) : string
    {
        switch ($fileData) {
            case 'd':
                $fileType = 'directory';
                break;

            case 'l':
                $fileType = 'symlink';
                break;

            default:
                $fileType = 'file';
        }

        return $fileType;
    }

    /**
     * @param array $fileData
     * @return string
     */
    private function getFileDate(array $fileData) : string
    {
        if (strpos($fileData['time'], ':') !== false) {
            $date = $fileData['month'].' '.$fileData['day'].' '.date('Y').' '.$fileData['time'];
        } else {
            $date = $fileData['date'].' 12:00:00';
        }

        $time = strtotime($date);

        return date('Y-m-d H:i:s', $time);
    }

    /**
     * Converts file rights string into octal value.
     *
     * @param string $permissions The UNIX-style permission string, e.g.: 'drwxr-xr-x'
     * @return string
     */
    private function getOctalChmod(string $permissions) : string
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
            $mode += $mapper[$i][$permissions[$i]] ?? 0;
        }

        return (string) $mode;
    }

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
    ) : ServiceInterface {
        $this->checkLocalFile($sourceFileName);
        $this->checkRemoteFile($destinationFileName);

        if (!file_exists($this->localPath.'/'.$sourceFileName)) {
            throw new RuntimeException(sprintf('File not found: %s', $this->localPath.'/'.$sourceFileName), 1007);
        }

        $uploadResult = @ftp_put(
            $this->connectionId,
            $destinationFileName,
            $this->localPath.'/'.$sourceFileName,
            $fileMode
        );

        if (!$uploadResult) {
            throw new RuntimeException(sprintf('There was a problem while uploading file: %s', $sourceFileName), 1008);
        }

        return $this;
    }

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
    ) : ServiceInterface {
        $this->checkRemoteFile($remoteFileName);
        $this->checkLocalFile($localFileName, true);

        $downloadResult = @ftp_get(
            $this->connectionId,
            $this->localPath.'/'.$localFileName,
            $remoteFileName,
            $fileMode
        );

        if (!$downloadResult) {
            throw new RuntimeException(
                sprintf('There was a problem while downloading file: %s', $remoteFileName),
                1010
            );
        }

        return $this;
    }

    /**
     * Checks local file, and generates new unique name if necessary.
     *
     * @param string $localFileName
     * @param bool $forceUnique
     * @throws RuntimeException
     */
    private function checkLocalFile(string&$localFileName, bool $forceUnique = false) : void
    {
        $pathInfo = pathinfo($localFileName);

        if ($pathInfo['dirname'] != '.') {
            $this->setLocalPath($pathInfo['dirname']);
            $localFileName = $pathInfo['basename'];
        }

        if (!$forceUnique) {
            return;
        }

        $variant = 0;

        while (file_exists($this->localPath.'/'.$localFileName) && $variant++ < 20) {
            $fileNameParts = [
                $pathInfo['filename'],
                '('.$variant.')',
                $pathInfo['extension']
            ];

            // remove empty parts (e.g.: when there's no extension)
            $fileNameParts = array_filter($fileNameParts);

            $localFileName = implode('.', $fileNameParts);
        }

        if ($variant >= 20) {
            throw new RuntimeException(
                sprintf('Too many similar files in folder %s, please cleanup first.', $this->localPath),
                1009
            );
        }
    }

    /**
     * Check remote file name.
     *
     * @param string $remoteFileName
     */
    private function checkRemoteFile(string&$remoteFileName) : void
    {
        $pathInfo = pathinfo($remoteFileName);

        if ($pathInfo['dirname'] != '.') {
            $this->setRemotePath($pathInfo['dirname']);
            $remoteFileName = $pathInfo['basename'];
        }
    }

    /**
     * Moves file on remote host.
     *
     * @param string $currentPath
     * @param string $newPath
     * @return ServiceInterface
     */
    public function moveRemoteFile(string $currentPath, string $newPath) : ServiceInterface
    {
        $result = @ftp_rename($this->connectionId, $currentPath, $newPath);

        if (!$result) {
            throw new RuntimeException(
                sprintf('Unable to move/rename file from %s to %s', $currentPath, $newPath),
                1011
            );
        }

        return $this;
    }

    /**
     * Deletes file on remote host.
     *
     * @param string $path
     * @return ServiceInterface
     */
    public function deleteRemoteFile(string $path) : ServiceInterface
    {
        $result = @ftp_delete($this->connectionId, $path);

        if (!$result) {
            throw new RuntimeException(sprintf('Unable to delete file on remote host: %s', $path), 1012);
        }

        return $this;
    }
}
