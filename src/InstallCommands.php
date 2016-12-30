<?php
/**
 * Created by PhpStorm.
 * User: victorsecuring
 * Date: 30.12.16
 * Time: 1:46 PM
 */

namespace zaboy\res;


use Composer\Script\Event;
use zaboy\installer\Install\AbstractCommand;
use zaboy\installer\Install\InstallerInterface;
class InstallCommands extends AbstractCommand
{
    /**
     * @param null $dir
     * @return InstallerInterface[]
     */
    public static function getInstallers($dir = null)
    {
        if (!isset($dir)) {
            $dir = __DIR__;
        }
        return parent::getInstallers($dir);
    }
}