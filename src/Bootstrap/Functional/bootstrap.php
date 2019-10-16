<?php

define('TESTS_RUNNING', true);
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['SERVER_REQUEST_METHOD'] = 'GET';
$loader = null;

$searchDirectory = dirname(__DIR__, 7);
while (true) {
    $newSearchDirectory = realpath($searchDirectory . DIRECTORY_SEPARATOR . '..');
    if ($searchDirectory === false || strlen($searchDirectory) < 3 || $newSearchDirectory === $searchDirectory) {
        throw new RuntimeException('No autoloader found');
    }
    $searchDirectory = $newSearchDirectory;

    if (file_exists($autoloadFile = implode(DIRECTORY_SEPARATOR, [$searchDirectory, 'vendor', 'autoload.php']))) {
        $loader = require $autoloadFile;
        break;
    }
}

use Doctrine\Common\Annotations\AnnotationRegistry;
use Dotenv\Dotenv;

$kernel = \Frosh\ClassicPhpunitBridge\Bootstrap\Functional\NormalKernel::class;
if (file_exists($searchDirectory . '/.env')) {
    $dotenv = Dotenv::create($searchDirectory);
    $dotenv->load();
    $kernel = \Frosh\ClassicPhpunitBridge\Bootstrap\Functional\ComposerKernel::class;
}
define('SEARCH_DIRECTORY', $searchDirectory);


AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$kernel::start();

