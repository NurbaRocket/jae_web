<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EnergoController extends Controller
{
    
    /**
     * @Route("/")
     */
    
    public function showAction()
    {
        // replace this example code with whatever you need
        return $this->render('energo/index.html.twig');
    }

}
