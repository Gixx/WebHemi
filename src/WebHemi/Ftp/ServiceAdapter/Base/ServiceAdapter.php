<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Ftp\ServiceAdapter\Base;

use RuntimeException;
use WebHemi\Ftp\ServiceInterface;
use WebHemi\Ftp\ServiceAdapter\AbstractServiceAdapter;

/**
 * Class ServiceAdapter.
 *
 * @codeCoverageIgnore - don't test third party library.
 */
class ServiceAdapter extends AbstractServiceAdapter
{
    /**
     * @var resource
     */
    private $connectionId = null;

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
     * Toggles connection security level.
     *
     * @param  bool $state
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
     * @param  bool $state
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
     * @param  string $path
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
     * Lists remote path.
     *
     * @param  null|string $path
     * @param  bool|null   $changeToDirectory
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
     * @param  string $remoteFileName
     * @param  string $localFileName
     * @param  int    $fileMode
     * @return ServiceInterface
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
     * Check remote file name.
     *
     * @param string $remoteFileName
     */
    protected function checkRemoteFile(string&$remoteFileName) : void
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
     * @param  string $currentPath
     * @param  string $newPath
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
     * @param  string $path
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
