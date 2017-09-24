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

class EnergoController extends Controller
{
    
    /**
     * @Route("/{_locale}")
     * @Route("/")
     * @Template("energo/index.html.twig")
     */
    
    public function showAction()
    {
        return array(
            'title' => 'Добро пожаловать!'
        );
    }



    /**
     * @Route("/{_locale}/category")
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
     * @Route("/{_locale}/page/{id}/")
     * @Method("GET")
     * @Template("energo/page.html.twig")
     */
    public function pageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var  $page PageTree */
        $page = $em->getRepository('AppBundle:PageTree')->findOneBy(array('id' => $id));
        $page->setTranslatableLocale($request->getLocale());
        if (!$page) {
            throw $this->createNotFoundException();
        }
        return array(
            'title'=> $page->getTitle(),
            'page' => $page,
        );
    }

    /**
     * @Route("/{_locale}/article/{id}/")
     * @Method("GET")
     * @Template("energo/page.html.twig")
     */
    public function articleAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var  $page PageTree */

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
     * @Route("/{url}", name="url_show")
     * @param $url
     * @Method("GET")
     * @Template()
     * @return Response
     * @throws NotFoundHttpException
     */
    public function fallbackAction($url, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $url = '/' . trim(urldecode($url), '/');
        $page = $em->getRepository('AppBundle:PageTree')->findOneBy(
            array(
                'url' => $url,
                //'active' => 1
            )
        );
        if (!empty($page)) {
            return $this->forward('AppBundle:Energo:page',
                array(
                    'id' => $page->getId(),
                ),
                $request->query->all()
            );
        }

        $page = $em->getRepository('AppBundle:PageTree')->findOneBy(
            array(
                'url' => $url,
                //'status' => true
            )
        );
        if (!empty($page)) {
            $this->container->get('request')->attributes->set('_controller', 'AppBundle:Energo:page');
            $this->container->get('request')->attributes->set('id', $page->getId());
            return $this->container->get('http_kernel')->handle($this->container->get('request'));
        }

        throw $this->createNotFoundException();
    }
}
