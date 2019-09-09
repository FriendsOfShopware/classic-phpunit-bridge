<?php

namespace Frosh\ClassicPhpunitBridge\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ControllerTest extends TestCase
{
    /**
     * @var \Enlight_Controller_Front
     */
    protected $front;

    /**
     * @var \Enlight_Template_Manager
     */
    protected $template;

    /**
     * @var \Enlight_View_Default
     */
    protected $view;

    /**
     * @var \Enlight_Controller_Request_RequestHttp
     */
    protected $request;

    /**
     * @var \Enlight_Controller_Response_ResponseHttp
     */
    protected $response;

    public function setUp():void
    {
        parent::setUp();

        Shopware()->Container()->reset('session');
        Shopware()->Container()->reset('auth');

        $this->reset();
    }

    public function dispatch(string $url, bool $followRedirects = false): \Enlight_Controller_Response_ResponseHttp
    {
        $request = $this->Request();
        if ($url !== null) {
            $request->setRequestUri($url);
        }
        $request->setPathInfo(null);

        $response = $this->Response();

        $front = $this->Front()
            ->setRequest($request)
            ->setResponse($response);

        $front->dispatch();

        if ($followRedirects && $this->Response()->getStatusCode() === Response::HTTP_FOUND) {
            $link = parse_url($this->Response()->getHeader('Location'), PHP_URL_PATH);
            $this->resetResponse();
            $cookies = $this->Response()->getCookies();
            $this->resetRequest();
            $this->Request()->setCookies($cookies);

            return $this->dispatch($link);
        }

        /** @var \Enlight_Controller_Plugins_ViewRenderer_Bootstrap $viewRenderer */
        $viewRenderer = $front->Plugins()->get('ViewRenderer');
        $this->view = $viewRenderer->Action()->View();

        return $response;
    }

    /**
     * Reset all instances, resources and init the internal view, template and front properties
     */
    public function reset()
    {
        $app = Shopware();

        $this->resetRequest();
        $this->resetResponse();

        // Force the assignments to be cleared. Needed for some test cases
        if ($this->view && $this->view->hasTemplate()) {
            $this->view->clearAssign();
        }

        $this->view = null;
        $this->template = null;
        $this->front = null;

        $app->Plugins()->reset();
        $app->Events()->reset();

        $container = Shopware()->Container();

        $container->get('models')->clear();

        $container
            ->reset('plugins')
            ->reset('front')
            ->reset('router')
            ->reset('system')
            ->reset('modules');

        $container->load('front');
        $container->load('plugins');

        foreach ($container->get('kernel')->getPlugins() as $plugin) {
            if (!$plugin->isActive()) {
                continue;
            }
            $container->get('events')->addSubscriber($plugin);
        }
    }

    public function resetRequest(): self
    {
        if ($this->request instanceof \Enlight_Controller_Request_RequestTestCase) {
            $this->request->clearQuery()
                ->clearPost()
                ->clearCookies();
        }
        $this->request = null;

        return $this;
    }

    public function resetResponse(): self
    {
        $this->response = null;

        return $this;
    }

    public function Front(): \Enlight_Controller_Front
    {
        if ($this->front === null) {
            $this->front = Shopware()->Container()->get('front');
        }

        return $this->front;
    }

    public function Template(): \Enlight_Template_Manager
    {
        if ($this->template === null) {
            $this->template = Shopware()->Container()->get('template');
        }

        return $this->template;
    }

    public function View(): \Enlight_View_Default
    {
        return $this->view;
    }

    public function Request(): \Enlight_Controller_Request_RequestTestCase
    {
        if ($this->request === null) {
            $this->request = \Enlight_Controller_Request_RequestTestCase::createFromGlobals();
        }

        return $this->request;
    }

    public function Response(): \Enlight_Controller_Response_ResponseHttp
    {
        if ($this->response === null) {
            $this->response = new \Enlight_Controller_Response_ResponseTestCase();
        }

        return $this->response;
    }

    /**
     * @param mixed $value
     */
    protected function setConfig(string $name, $value): void
    {
        Shopware()->Container()->get('config_writer')->save($name, $value);
        Shopware()->Container()->get('cache')->clean();
        Shopware()->Container()->get('config')->setShop(Shopware()->Shop());
    }
}
