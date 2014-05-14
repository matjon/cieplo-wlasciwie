<?php

namespace Kraken\WarmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function landingAction()
    {
        return $this->render('KrakenWarmBundle:Default:landing.html.twig');
    }

    public function howItWorksAction()
    {
        return $this->render('KrakenWarmBundle:Default:howItWorks.html.twig');
    }

    public function whyNotWorksAction()
    {
        return $this->render('KrakenWarmBundle:Default:whyNotWorks.html.twig');
    }

    public function rulesAction()
    {
        return $this->render('KrakenWarmBundle:Default:rules.html.twig');
    }

    public function whatAction()
    {
        return $this->render('KrakenWarmBundle:Default:what.html.twig');
    }
}
