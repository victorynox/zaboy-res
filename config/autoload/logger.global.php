<?php
/**
 * Created by PhpStorm.
 * User: victorsecuring
 * Date: 19.12.16
 * Time: 11:45 AM
 */

use \zaboy\res\Logger\FileLogWriter;
use \zaboy\res\Logger\FileLogWriterFactory;
use \zaboy\installer\Command;
use \zaboy\res\Logger\Installer as LoggerInstaller;

return [
    'logWriter' => [
        FileLogWriter::class => [
            FileLogWriterFactory::FILE_NAME_KEY =>
                realpath(Command::getPublicDir() . DIRECTORY_SEPARATOR .
                    LoggerInstaller::LOGS_DIR . DIRECTORY_SEPARATOR . LoggerInstaller::LOGS_FILE)
        ]
    ],
    'services' => [
        'factories' => [
            FileLogWriter::class => FileLogWriterFactory::class
        ],
        'aliases' =>[
            'logWriter' => FileLogWriter::class
        ]
    ]
];
