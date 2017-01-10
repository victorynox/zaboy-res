<?php
/**
 * Created by PhpStorm.
 * User: victorsecuring
 * Date: 19.12.16
 * Time: 11:45 AM
 */

use \zaboy\res\Logger\FileLogWriter;
use \zaboy\res\Logger\FileLogWriterFactory;

return [
    'logWriter' => [
        FileLogWriter::class => [
            FileLogWriterFactory::FILE_NAME_KEY => realpath('logs.txt')
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
