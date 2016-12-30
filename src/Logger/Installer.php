<?php
/**
 * Created by PhpStorm.
 * User: victorsecuring
 * Date: 30.12.16
 * Time: 2:16 PM
 */

namespace zaboy\res\Logger;


use Interop\Container\ContainerInterface;
use zaboy\installer\Install\InstallerInterface;

class Installer implements InstallerInterface
{

    protected $projectDir;
    /**
     * Installer constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->projectDir = realpath(__DIR__ . DIRECTORY_SEPARATOR .'..' . DIRECTORY_SEPARATOR . '..');


    }

    /**
     * install
     * @return void
     */
    public function install()
    {
        if (getenv('APP_ENV') !== 'dev') {
            echo 'getenv("APP_ENV") !== "dev" It has did nothing';
            exit;
        }
        $logCsv = $this->projectDir . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'csv-storage' . DIRECTORY_SEPARATOR .
            'logs.csv';
        $publicDir = $this->projectDir . DIRECTORY_SEPARATOR . 'public';
        mkdir($publicDir . DIRECTORY_SEPARATOR . 'csv-storage');
        copy($logCsv, $publicDir . DIRECTORY_SEPARATOR . 'csv-storage' . DIRECTORY_SEPARATOR . 'logs.csv');
    }

    /**
     * Clean all installation
     * @return void
     */
    public function uninstall()
    {
        if (getenv('APP_ENV') !== 'dev') {
            echo 'getenv("APP_ENV") !== "dev" It has did nothing';
            exit;
        }
        $publicDir = $this->projectDir . DIRECTORY_SEPARATOR . 'public';
        if(file_exists($publicDir . DIRECTORY_SEPARATOR . 'csv-storage' . DIRECTORY_SEPARATOR . 'logs.csv')){
            unlink($publicDir . DIRECTORY_SEPARATOR . 'csv-storage' . DIRECTORY_SEPARATOR . 'logs.csv');
        }
        if(is_dir($publicDir . DIRECTORY_SEPARATOR . 'csv-storage')){
            rmdir($publicDir . DIRECTORY_SEPARATOR . 'csv-storage');
        }
    }

    /**
     * Make clean and install.
     * @return void
     */
    public function reinstall()
    {
        $this->uninstall();
        $this->install();
    }
}