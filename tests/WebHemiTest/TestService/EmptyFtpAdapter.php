<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\TestService;

use WebHemi\Ftp\ServiceAdapter\AbstractServiceAdapter;
use WebHemi\Ftp\ServiceInterface;

/**
 * Class EmptyFtpAdapter
 */
class EmptyFtpAdapter extends AbstractServiceAdapter
{
    /** @var bool */
    public $connected = false;
    /** @var bool */
    public $secure = false;
    /** @var bool */
    public $passive = false;
    /** @var string */
    public $remotePath = '/';
    /** @var string */
    public $remoteFilename = 'test.txt';
    /** @var array */
    protected $remoteFileSystem = [];

    /**
     * Connect and login to remote host.
     *
     * @return ServiceInterface
     */
    public function connect() : ServiceInterface
    {
        $this->connected = true;

        return $this;
    }

    /**
     * Disconnect from remote host.
     *
     * @return ServiceInterface
     */
    public function disconnect() : ServiceInterface
    {
        $this->connected = false;

        return $this;
    }

    /**
     * Toggles connection security level.
     *
     * @param bool $state
     * @return ServiceInterface
     */
    public function setSecureConnection(bool $state) : ServiceInterface
    {
        $this->secure = $state;

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
        $this->passive = $state;

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
        $this->remotePath = $path;

        return $this;
    }

    /**
     * Gets remote path.
     *
     * @return string
     */
    public function getRemotePath() : string
    {
        return $this->remotePath;
    }

    /**
     * Check remote file name.
     *
     * @param string $remoteFileName
     */
    protected function checkRemoteFile(string&$remoteFileName) : void
    {
        if (!$remoteFileName) {
            $remoteFileName = $this->remoteFilename;
        }
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
        $remoteFileList = [];

        if ($changeToDirectory) {
            $this->setRemotePath($path);
        }

        foreach ($this->remoteFileSystem as $file) {
            if (isset($file['remotePath']) && isset($file['remoteFile']) && $file['remotePath'] == $this->remotePath) {
                $remoteFileList[] = $file['remoteFile'];
            }
        }

        return $remoteFileList;
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
        $this->remoteFileSystem[] = [
            'localPath' => $this->localPath,
            'localFile' => $sourceFileName,
            'remotePath' => $this->remotePath,
            'remoteFile' => $destinationFileName,
            'mode' => $fileMode
        ];

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

        $localFileName .= '';

        unset($remoteFileName, $fileMode);

        return $this;
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
        $oldFilePath = dirname($currentPath);
        $oldFileName = basename($currentPath);
        $newFilePath = dirname($newPath);
        $newFileName = basename($newPath);

        foreach ($this->remoteFileSystem as $index => $file) {
            if (!($file['remoteFile'] == $oldFileName && $file['remotePath'] == $oldFilePath)) {
                $this->remoteFileSystem[$index]['remoteFile'] = $newFileName;
                $this->remoteFileSystem[$index]['remotePath'] = $newFilePath;
            }
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
        $tmp = [];

        $filePath = dirname($path);
        $fileName = basename($path);

        foreach ($this->remoteFileSystem as $file) {
            if (!($file['remoteFile'] == $fileName && $file['remotePath'] == $filePath)) {
                $tmp[] = $file;
            }
        }

        $this->remoteFileSystem = $tmp;

        return $this;
    }

    /**
     * @param string $localFileName
     * @param bool $forceUnique
     */
    public function testLocalFile(string&$localFileName, bool $forceUnique = false) : void
    {
        $this->checkLocalFile($localFileName, $forceUnique);
    }

    /**
     * @param string $permissions
     * @return string
     */
    public function convertOctalChmod(string $permissions) : string
    {
        return $this->getOctalChmod($permissions);
    }
}
