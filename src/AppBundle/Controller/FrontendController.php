<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use AppBundle\Entity\PageTree;
use AppBundle\Entity\Article;
use ReCaptcha\ReCaptcha;

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
            'articles' => $articles
        );
    }


    /**
     * Вызывается только в шаблоне, отрендерить top menu
     *
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
     * Вызывается только в шаблоне, отрендерить sidebar
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
     * Вызывается только в шаблоне, отрендерить секцию "плановые отключения"
     *
     * @Template("common/posts.html.twig")
     */
    public function postSectionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('AppBundle:Post')->findBy(
            array(),
            array('createTime' => 'DESC'),
            6
        );
        return array(
            'posts' => $posts
        );
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
     * Article content
     *
     * @Route("/{_locale}/outage/{id}/", name="post_show")
     * @Method("GET")
     * @Template("page/post.html.twig")
     */
    public function postAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var  $page Article */

        $page = $em->getRepository('AppBundle:Post')->findOneBy(array('id' => $id));

        if (!$page) {
            throw $this->createNotFoundException();
        }
        return array(
            'title'=> $page->getTitle(),
            'page' => $page,
        );
    }

    /**
     * Post index
     *
     * @Route("/{_locale}/outages/", name="outage_index_show")
     * @Method("GET")
     * @Template("page/post_index.html.twig")
     */
    public function outagesAction(Request $request, $_locale)
    {
        $paginatorOptions = array(
            'defaultSortFieldName' => 'p.createTime',
            'defaultSortDirection' => 'desc'
        );
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Post')->createQueryBuilder('p');
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->get('page', 1),
            16,
            $paginatorOptions
        );

        return array(
            'title' => 'Плановые отключения',
            'pagination' => $pagination
        );
    }

    /**
     * @Route("/{_locale}/feedback", name="feedback_show", defaults={"_locale" = "ru"})
     * @Method("GET|POST")
     * @Template("page/feedback.html.twig")
     */
    public function feedbackAction(Request $request, \Swift_Mailer $mailer)
    {
        $recaptcha = new ReCaptcha('6LcPuDMUAAAAAJCUeUKGq08RhI_dx7iQ_wq7C-rE');
        $form = $this->getFeedbackForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $response = $recaptcha->verify($request->request->get('g-recaptcha-response'), $request->getClientIp());
            if ($response->isSuccess()) {
                $feedback = $form->getData();
                $message = (new \Swift_Message($feedback['stuff']. ' от ' . $feedback['firstName'] . ' ' . $feedback['familyName']))
                    ->setFrom($feedback['email'])
                    ->setTo('support@jae.kg')
                    ->setBody($feedback['content'], 'text/html')
                    ->addPart($feedback['content'], 'text/plain')
                ;
                $mailer->send($message);
                $this->get('session')->getFlashBag()->add(
                    'message',
                    'Ваше письмо отправлено!'
                );
                return $this->redirectToRoute('feedback_show');
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    "The reCAPTCHA wasn't entered correctly. Go back and try it again."
                );
            }
        }
        return array(
            'form' => $form->createView(),
            'title'=> 'Электронная приемная'
        );
    }

    /**
     * @Route("/{_locale}/calculate-taxes", name="calculate_tax_show", requirements={"_locale": "[a-zA-Z]{2}"})
     * @Method("GET|POST")
     * @Template("page/calculate_tax.html.twig")
     */
    public function calculateTaxAction(Request $request)
    {

        return array(
            'title' => 'Калькулятор тарифов'
        );
    }

    /**
     * @Route("/{_locale}/my-balance", name="my_balance_show")
     * @Method("GET")
     * @Template("page/my_balance.html.twig")
     */
    public function myBalanceAction(Request $request)
    {
        return array(
            'title' => 'Баланс абонента'
        );
    }

    /**
     * @Route("/provider-balance", name="provider_balance_show")
     * @Method("GET")
     * @Template("page/provider_balance.html.twig")
     */
    public function providerBalanceAction(Request $request)
    {
        return array(
            'title' => 'Баланс провайдера'
        );
    }

    /**
     * Process user friendly url
     *
     * @Route("/{_locale}/{url}", name="url_show",
     *    defaults={"_locale" = "ru"},
     *    requirements={"_locale": "[a-zA-Z]{2}"}
     * )
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
        // Если урл принадлежит PageTree, то передаст обработку методу pageAction(), указав Id объекта
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
        // Если урл принадлежит Article, то передаст обработку методу articleAction(), указав Id объекта
        if (!empty($article)) {
            $request->attributes->set('_controller', 'AppBundle:Frontend:article');
            $request->attributes->set('id', $article->getId());
            $request->attributes->set('_locale', $_locale);
            return $this->get('http_kernel')->handle($request);
        }

        throw $this->createNotFoundException();
    }

    /**
     * Форма электронного приема
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getFeedbackForm()
    {
        $form = $this->createFormBuilder()
            ->add('firstName', FormType\TextType::class, array(
                'label' => 'feedback_first_name_label'
            ))
            ->add('familyName', FormType\TextType::class, array(
                'label' => 'feedback_family_name_label'
            ))
            ->add('email', FormType\EmailType::class, array(
                'label' => 'feedback_email_label'
            ))
            ->add('city', FormType\TextType::class, array(
                'label' => 'feedback_city_label'
            ))
            ->add('phone', FormType\TextType::class, array(
                'label' => 'feedback_phone_label'
            ))
            ->add('stuff', FormType\ChoiceType::class, array(
                'label' => 'feedback_type_label',
                'choices' => array(
                    'Обращение' => 'Обращение',
                    'Заявление' => 'Заявление',
                    'Предложение' => 'Предложение',
                    'Жалоба' => 'Жалоба'
                )
            ))
            ->add('content', CKEditorType::class, array(
                'label' => 'feedback_content_label',
                'config' => array(
                    'toolbar' => array(
                        array(
                        )
                    )
                ),
            ))
            ->add('send', FormType\SubmitType::class,array(
                'label' => 'feedback_button_label',
                'attr' => array(
                    'class' => 'g-recaptcha',
                    'data-callback' => 'onSubmit',
                    'data-sitekey' => '6LcPuDMUAAAAAFX1CN1vCLEcHfTCMZaLkQ_kU-c6',
                )
            ))
            ->setAttributes(array('id', 'feedback_form'))
            ->setAction($this->generateUrl('feedback_show'))
            ->getForm()
        ;
        return $form;
    }
}
