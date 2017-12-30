<?php

namespace Jae\EnergoBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use ReCaptcha\ReCaptcha;

class EnergoController extends Controller
{
    /**
     * @Route("/subscriber/{code}/balance", name="get_my_balance")
     * @Method("POST")
     */
    public function myBalance($code, Request $request)
    {
        $recaptcha = new ReCaptcha('6LcPuDMUAAAAAJCUeUKGq08RhI_dx7iQ_wq7C-rE');
        $response = $recaptcha->verify($request->get('g-recaptcha-response'), $request->getClientIp());
        if ($response->isSuccess()) {
            $browser = new \Buzz\Browser();
            $browser->getClient()->setTimeout(5000);
            try {
                // 213.145.145.94
                $data = $browser->get('http://213.145.145.94:3030/subscriber/' . $code);
                return JsonResponse::fromJsonString($data->getContent());
            } catch (\Exception $ex) {
                return JsonResponse::create(array(
                    'message' => 'Не удалось получить данные. Попробуйте еще раз'
                ));
            }/**/
        }
        return JsonResponse::create(array(
            'message' => "The reCAPTCHA wasn't entered correctly. Go back and try it again."
        ));
    }

    /**
     * @Route("/provider/{code}/balance", name="get_provider_balance")
     * @Method("POST")
     */
    public function providerBalance($code, Request $request)
    {
        $recaptcha = new ReCaptcha('6LcPuDMUAAAAAJCUeUKGq08RhI_dx7iQ_wq7C-rE');
        $response = $recaptcha->verify($request->get('g-recaptcha-response'), $request->getClientIp());
        if ($response->isSuccess()) {
            $browser = new \Buzz\Browser();
            $browser->getClient()->setTimeout(5000);
            try {
                // 213.145.145.94
                $data = $browser->get('http://213.145.145.94:3030/provider/' . $code);
                return JsonResponse::fromJsonString($data->getContent());
            } catch (\Exception $ex) {
                return JsonResponse::create(array(
                    'message' => 'Не удалось получить данные. Попробуйте еще раз'
                ));
            }/**/
        }
        return JsonResponse::create(array(
            'message' => "The reCAPTCHA wasn't entered correctly. Go back and try it again."
        ));
    }
}
