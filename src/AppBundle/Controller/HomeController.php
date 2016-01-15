<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        $content = $this->get('es.manager.default.content');
        $homepage = $content->findOneBy(['bundle' => 'global-docs', 'path' => 'WhatIsONGR.md']);

        return $this->render(
            'AppBundle:Doc:document.html.twig',
            [
                'document' => $homepage,
            ]
        );
    }
}
