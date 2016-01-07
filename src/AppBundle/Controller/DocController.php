<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Docs pages Controller for the ONGR routing component
 */
class DocController extends Controller
{
    /**
     * Documentation page render action. $document is actually an instance of the Document.
     *
     * @param object $document
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function documentAction($document)
    {
        return $this->render('AppBundle:Doc:document.html.twig',
            [
                'document' => $document,
            ]
        );
    }

}
