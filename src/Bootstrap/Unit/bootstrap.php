<?php

use Composer\Autoload\ClassLoader;

define('TESTS_RUNNING', true);
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['SERVER_REQUEST_METHOD'] = 'GET';

debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
$searchDirectory = dirname(__DIR__, 7);
$pluginFolder = dirname(__DIR__, 6);
$pluginName = pathinfo($pluginFolder, PATHINFO_BASENAME);

$loader = new ClassLoader();
$loader->addPsr4($pluginName . '\\', $pluginFolder);
$loader->register();

while (true) {
    $newSearchDirectory = realpath($searchDirectory . DIRECTORY_SEPARATOR . '..');
    if ($searchDirectory === false || strlen($searchDirectory) < 3 || $newSearchDirectory === $searchDirectory) {
        throw new RuntimeException('No autoloader found');
    }
    $searchDirectory = $newSearchDirectory;

    if (file_exists($autoloadFile = implode(DIRECTORY_SEPARATOR, [$searchDirectory, 'vendor', 'autoload.php']))) {
        require $autoloadFile;
        break;
    }
}
