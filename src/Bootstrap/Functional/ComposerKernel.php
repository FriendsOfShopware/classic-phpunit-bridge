<?php

namespace Frosh\ClassicPhpunitBridge\Bootstrap\Functional;

use Shopware\Models\Shop\Shop;

class ComposerKernel extends \AppKernel
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