<?php

namespace AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Intl\Intl;
use AppBundle\Entity\Article;
use AppBundle\Entity\PageTree;
use AppBundle\Entity\PageInterface;

class HelperExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @param ContainerInterface $container
     */
    public function __construct($container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('breadcrumbs', array($this, 'breadcrumbs')),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('breadcrumbs', array($this, 'breadcrumbs')),
            new \Twig_SimpleFilter('pageUrl', array($this, 'pageUrl')),
            new \Twig_SimpleFilter('countryFilter', array($this, 'countryFilter'))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }


    /**
     * @param string $countryCode
     * @param null|String $locale
     * @return null|String
     */
    public function countryFilter($countryCode, $locale = null)
    {
        try {
            return Intl::getRegionBundle()->getCountryName($countryCode, $locale);
        } catch (\Exception $ex) {
            return '';
        }
    }

    /**
     * @param PageInterface $page
     * @param Array $params
     * @return String
     */
    public function pageUrl($page, $params = array())
    {
        $url = '';
        if ($page instanceof PageInterface) {
            $router = $this->container->get('router');
            if ($page->getUrl() != null) {
                $params['url' ]= $page->getUrl();
                $url = $router ->generate('url_show', $params);
            } elseif ($page instanceof Article)  {
                $params['id' ]= $page->getId();
                $url = $router ->generate('article_show', $params);
            } elseif ($page instanceof PageTree)  {
                $params['id' ]= $page->getId();
                $url = $router ->generate('page_tree_show', $params);
            }
        }
        return $url;
    }

    /**
     *
     * @param PageInterface|String $page
     * @return String
     */
    public function breadcrumbs($page) {
        $param = array();
        if ($page instanceof PageInterface) {
            $param = array(
                'type' => $page instanceof Article ? 'article' : 'page',
                'page' => $page
            );
        }
        if (is_string($page)) {
            $param = array(
                'type' => 'string',
                'page' => $page
            );
        }
        return $this->environment->render('helper/breadcrumbs.html.twig', $param);
    }

    public function getName()
    {
        return 'app_helper';
    }
}
