<?php
namespace Ilch;

/**
 * Class for modifying the file manager config file and set session variables
 *
 * @author Mairu
 */
class FileManagerConfigurator
{
    /** @var string */
    private $pathToConfig;

    /** @var string */
    private $configFileContents;

    /** @var array */
    private $configArray;

    /**
     * @param string $pathToConfig
     * @throws \InvalidArgumentException
     */
    public function __construct($pathToConfig = '')
    {
        if (empty($pathToConfig)) {
            $pathToConfig = __DIR__ . '/../filemanager/scripts/filemanager.config.js';
        }
        if (!file_exists($pathToConfig)) {
            throw new \InvalidArgumentException('Invalid path to filemanager config file');
        }
        $this->pathToConfig = $pathToConfig;
        $this->configFileContents = file_get_contents($this->pathToConfig);
        $this->configArray = json_decode($this->configFileContents, true);
    }

    /**
     * Modifies the file manager config file for correct paths for file uploads
     * @param bool $allowUpload
     * @return bool if the file manager is configured correctly
     */
    public function configureFileManager($allowUpload)
    {
        if (!isset($_SESSION['ic_CKEditor'])) {
            $_SESSION['ic_CKEditor'] = array();
        }

        if ($allowUpload) {
            $_SESSION['ic_CKEditor']['allowUpload'] = $allowUpload;
        }

        //only check once per session
        if (isset($_SESSION['ic_CKEditor']['configValid'])) {
            return $_SESSION['ic_CKEditor']['configValid'];
        }

        $configValid = false;
        $required = $this->createRequiredBaseUrlOption();
        if ($required == $this->getBaseUrlOption()) {
            $configValid = true;
        } elseif ($this->isConfigWritable()) {
            $configValid = $this->writeBaseUrlOption($required);
        }

        $_SESSION['ic_CKEditor']['configValid'] = $configValid;

        return $configValid;
    }

    /**
     * Create the required baseUrl option value for the current "configuration"
     * @return bool|string
     */
    private function createRequiredBaseUrlOption()
    {
        $dir = dirname($_SERVER['SCRIPT_NAME']);
        //if not run in a directory no baseUrl options needed
        if (strlen($dir) == 1) {
            return false;
        }

        if (!isset($this->configArray['options']['relPath'])) {
            throw new \RuntimeException('relPath option missing in filemanger config');
        }
        $relPath = $this->configArray['options']['relPath'];

        return $dir . $relPath;
    }

    /**
     * Returns the current baseUrl option from the config file
     * @return bool|string
     */
    private function getBaseUrlOption()
    {
        if (isset($this->configArray['options']['baseUrl'])) {
            return $this->configArray['options']['baseUrl'];
        }
        return false;
    }

    /**
     * @return bool
     */
    private function isConfigWritable()
    {
        return is_writeable($this->pathToConfig);
    }

    /**
     * @param string|false $baseUrl
     * @return bool
     */
    private function writeBaseUrlOption($baseUrl)
    {
        if (!is_string($baseUrl) && $baseUrl !== false) {
            throw new \InvalidArgumentException('only a string or false accepted as parameter');
        }
        $fileContents = file_get_contents($this->pathToConfig);
        $modified = false;
        $modifiedFileContents = preg_replace_callback(
            '~"baseUrl"\s*:\s*("[a-z0-9_\-/\\.]+"|false)~i',
            function(array $matches) use ($baseUrl, &$modified) {
                if (is_string($baseUrl)) {
                    $baseUrl = '"' . $baseUrl . '"';
                }
                if ($baseUrl != $matches[1]) {
                    $modified = true;
                    return str_replace($matches[1], $baseUrl, $matches[0]);
                }
                return $matches[0];
            },
            $fileContents
        );
        if ($modified) {
            file_put_contents($this->pathToConfig, $modifiedFileContents);
            return true;
        }
        return false;
    }
}