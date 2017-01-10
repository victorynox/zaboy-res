<?php

chdir(dirname(__DIR__));

require 'vendor/autoload.php';
require_once 'config/env_configurator.php';

use Zend\Diactoros\Server;

// Define application environment - 'dev' or 'prop'
if (getenv('APP_ENV') === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $env = 'develop';
}

$container = include 'config/container.php';


$server = Server::createServer(function () {
    return "Hello world";
}, $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$server->listen();
