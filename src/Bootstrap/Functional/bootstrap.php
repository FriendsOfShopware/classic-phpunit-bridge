<?php

define('TESTS_RUNNING', true);
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['SERVER_REQUEST_METHOD'] = 'GET';

$searchDirectory = dirname(dirname(__DIR__));
while (true) {
    $newSearchDirectory = realpath($searchDirectory . DIRECTORY_SEPARATOR . '..');
    if ($searchDirectory === false || strlen($searchDirectory) < 3 || $newSearchDirectory === $searchDirectory) {
        throw new RuntimeException('No autoloader found');
    }
    $searchDirectory = $newSearchDirectory;

    if (file_exists($autoloadFile = implode(DIRECTORY_SEPARATOR, [$searchDirectory, 'tests', 'Functional', 'bootstrap.php']))) {
        require $autoloadFile;
        break;
    }
}
