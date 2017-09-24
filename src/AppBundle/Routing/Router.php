<?php

namespace AppBundle\Routing;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RequestContext;

class Router extends \Symfony\Bundle\FrameworkBundle\Routing\Router
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container, $resource, array $options = array(), RequestContext $context = null)
    {
        parent::__construct($container, $resource, $options, $context);
        $this->container = $container;
    }

    public function match($pathinfo)
    {
        try {
            $param = parent::match($pathinfo);
            return $param;
        } catch (\Exception $ex) {
        }

        return array(
            '_controller' => 'KeenSteps\EcommerceBundle\Controller\FrontendController::fallbackAction',
            '_route' => 'url_show',
            'url' => str_replace('/', '%2F', $pathinfo)
        );
    }

    public function generate($name, $parameters = array(), $referenceType = \Symfony\Component\Routing\Router::ABSOLUTE_PATH)
    {
        $forceHttp = false;
        if (isset($parameters['forceHttp'])) {
            $forceHttp = $parameters['forceHttp'];
            unset($parameters['forceHttp']);
        }
        if ($name == 'url_show') {
            $parameters['url'] = ltrim($parameters['url'], '/');
            $parameters['url'] = str_replace('/', '%2F', $parameters['url']);
        }
        $url = parent::generate($name, $parameters, $referenceType);
        if ($name == 'url_show') {
            $url = str_replace('%252F', '/', $url);
        }

        if ($this->container->get('request_stack')->getMasterRequest()->isTestEnvironment()) { // test environment without ssl or primary domain
            return $url;
        }

        if ($referenceType != \Symfony\Component\Routing\Router::ABSOLUTE_URL) {
            return $url;
        }

        $domainSettings = $settings = $this->container->get('ecommerce.settings')->getDomain();
        if (stripos($url, '/admin/') === false) {
            if (stripos($url, '/checkout/') !== false) { // checkout pages has it's own domain and must be secure
                $url = str_replace('://' . $this->getContext()->getHost(), '://' . $settings->getCheckoutDomain(), $url);
                $url = str_replace('http://', 'https://', $url);
            } else {
                $url = str_replace('https://', 'http://', $url);
                if ($domainSettings->getPrimaryDomain()) {  // frontend has it's own domain
                    $url = str_replace('://' . $this->getContext()->getHost(), '://' . $settings->getPrimaryDomain(), $url);
                } else {
                    $url = str_replace('://' . $this->getContext()->getHost(), '://' . $settings->getSystemDomain(), $url);
                }
            }
        } else {
            $url = str_replace('://' . $this->getContext()->getHost(), '://' . $settings->getSystemDomain(), $url);
            $url = str_replace('http://', 'https://', $url); // admin panel must be secure
        }

        // головная боль. если мы на фронтенде продакшна в режиме редактирования темы и
        // то на чекаут и по фронтенду по системному домену по ссл-у
        if (!$forceHttp && $this->getContext()->getScheme() == 'https' && $this->getContext()->getHost() == $settings->getSystemDomain() && $this->container->get('session')->get('_security_backend')) {
            $url = str_replace('http://', 'https://', $url); // admin panel must be secure
            $url = str_replace('://' . $settings->getCheckoutDomain(), '://' . $settings->getSystemDomain(), $url);
            if ($settings->getPrimaryDomain()) {
                $url = str_replace('://' . $settings->getPrimaryDomain(), '://' . $settings->getSystemDomain(), $url);
            }
        }

        if ($forceHttp) {
            $url = str_replace('https://', 'http://', $url);
        }
        return $url;
    }
}
