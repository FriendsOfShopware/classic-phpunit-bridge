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
use Shopware\Models\Shop\Shop;

class TestKernel extends \Shopware\Kernel
{
    /**
     * Static method to start boot kernel without leaving local scope in test helper
     */
    public static function start()
    {
        $kernel = new self('testing', true);
        $kernel->boot();

        $container = $kernel->getContainer();
        $container->get('plugins')->Core()->ErrorHandler()->registerErrorHandler(E_ALL | E_STRICT);

        /** @var \Shopware\Models\Shop\Repository $repository */
        $repository = $container->get('models')->getRepository(Shop::class);

        $shop = $repository->getActiveDefault();
        if (Shopware()->Container()->has('shopware.components.shop_registration_service')) {
            Shopware()->Container()->get('shopware.components.shop_registration_service')->registerShop($shop);
        } else {
            $shop->registerResources();
        }

        $_SERVER['HTTP_HOST'] = $shop->getHost();
    }

    protected function getConfigPath()
    {
        return __DIR__ . '/config.php';
    }
}

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

TestKernel::start();

