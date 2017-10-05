<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\PageTree;
use AppBundle\Entity\Article;

class FrontendController extends Controller
{
    /**
     * Main Page
     *
     * @Route("/{_locale}",
     *    defaults={"_locale" = "ru"},
     *    requirements={"_locale": "[a-zA-Z]{2}"},
     *    name="main_page"
     * )
     * @Template("energo/index.html.twig")
     */
    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('AppBundle:Article')->findBy(
            array(),
            array('createTime' => 'DESC'),
            3
        );
        return array(
            'articles' => $articles,
            'title' => 'Добро пожаловать!'
        );
    }


    /**
     * Render menu
     *
     * @Route("/category")
     * @Template("common/topmenu.html.twig")
     */
    public function menuAction()
    {
        $em = $this->getDoctrine()->getManager();
        $pageTree = $em->getRepository('AppBundle:PageTree')->findBy(
            array(
                'parent' => null,
                //'status' => true
            ),
            array('id' => 'asc')
        );
        $repository = $em->getRepository('AppBundle:PageTree');

        return array(
            'pageTree' => $pageTree,
            'repository' => $repository
        );
    }

    /**
     * Render article sidebar
     *
     * @Template("common/article_sidebar.html.twig")
     */
    public function articleSidebarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $params = array();
        $params['categories'] = $em->getRepository('AppBundle:PageTree')->findBy(array('pageType' => 'news_page'));

        return $params;
    }

    /**
     * PageTree Content
     *
     * @Route("/{_locale}/page/{id}/", name="page_tree_show")
     * @Method("GET")
     * @Template("page/page.html.twig")
     */
    public function pageAction(Request $request, $_locale, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var  $page PageTree */
        $page = $em->getRepository('AppBundle:PageTree')->findOneBy(array('id' => $id));
        if (!$page) {
            throw $this->createNotFoundException();
        }
        $params =  array(
            'repository' => $em->getRepository('AppBundle:PageTree'),
            'page' => $page,
            'title' => $page->getTitle(),
        );
        if ($page->getPageType() == 'news_page') {
            $paginatorOptions = array(
                'defaultSortFieldName' => 'a.createTime',
                'defaultSortDirection' => 'desc'
            );
            $queryBuilder = $em->getRepository('AppBundle:Article')->createQueryBuilder('a')
                ->where('a.pageTree = :pid')
                ->setParameter('pid', $page)
            ;
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $queryBuilder,
                $request->query->get('page', 1),
                16,
                $paginatorOptions
            );
            $params['pagination'] = $pagination;

        }
        /** @var \AppBundle\Entity\User $u */
        /*$u = $em->getRepository('AppBundle:User')->findOneBy(array('id' => 1));
        $u->addRole(\AppBundle\Entity\User::ROLE_SUPER_ADMIN);
        $u->addRole('ROLE_ADMIN');
        $em->persist($u);
        $em->flush();/**/

        return $params;
    }

    /**
     * Article content
     *
     * @Route("/{_locale}/article/{id}/", name="article_show")
     * @Method("GET")
     * @Template("page/article.html.twig")
     */
    public function articleAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var  $page Article */

        $page = $em->getRepository('AppBundle:Article')->findOneBy(array('id' => $id));

        if (!$page) {
            throw $this->createNotFoundException();
        }
        return array(
            'title'=> $page->getTitle(),
            'page' => $page,
        );
    }


    /**
     * Process user friendly url
     *
     * @Route("/{_locale}/{url}", name="url_show",
     *    defaults={"_locale" = "ru"},
     *    requirements={"_locale": "[a-zA-Z]{2}"}
     * )
     * @param $url
     * @Method("GET")
     * @Template()
     * @return Response
     * @throws NotFoundHttpException
     */
    public function fallbackAction($_locale, $url, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $url =  trim(urldecode($url), '/');
        /** @var $page PageTree */
        $page = $em->getRepository('AppBundle:PageTree')->findOneBy(
            array(
                'url' => $url,
                'status' => 1
            )
        );
        if (!empty($page)) {
            $request->attributes->set('_controller', 'AppBundle:Frontend:page');
            $request->attributes->set('id', $page->getId());
            $request->attributes->set('_locale', $_locale);
            return $this->get('http_kernel')->handle($request);
        }

        /** @var $article Article */
        $article = $em->getRepository('AppBundle:Article')->findOneBy(
            array(
                'url' => $url,
                'status' => 'public'
            )
        );
        if (!empty($article)) {
            $request->attributes->set('_controller', 'AppBundle:Frontend:article');
            $request->attributes->set('id', $article->getId());
            $request->attributes->set('_locale', $_locale);
            return $this->get('http_kernel')->handle($request);
        }

        throw $this->createNotFoundException();
    }
}
