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
        try{
            $publicDir = $this->projectDir . DIRECTORY_SEPARATOR . 'public';
            unlink($publicDir . DIRECTORY_SEPARATOR . 'csv-storage' . DIRECTORY_SEPARATOR . 'logs.csv');
        } catch (\Exception $e){
            echo $e->getMessage() . "\n";
        }
        try{
            $publicDir = $this->projectDir . DIRECTORY_SEPARATOR . 'public';
            rmdir($publicDir . DIRECTORY_SEPARATOR . 'csv-storage');
        } catch (\Exception $e){
            echo $e->getMessage() . "\n";
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