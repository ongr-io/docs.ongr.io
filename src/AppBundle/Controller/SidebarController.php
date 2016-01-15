<?php

namespace AppBundle\Controller;

use ONGR\ElasticsearchBundle\Result\Result;
use ONGR\ElasticsearchDSL\Aggregation\TermsAggregation;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SidebarController extends Controller
{
    public function bundleListAction()
    {
        return $this->render('AppBundle:Sidebar:bundleList.html.twig',
            [
                'bundle' => [],
            ]
        );
    }

    public function treeAction(Request $request)
    {
        $content = $this->get('es.manager.default.content');

        $termFilter = new TermQuery('path', 'README.md');
        $termAggregation = new TermsAggregation('bundle', 'bundle');

        $search = $content->createSearch();
        $search->addFilter($termFilter);
        $search->addAggregation($termAggregation);
        $search->setSize(0);

        $results = $content->execute($search);
        $bundles = $results->getAggregation('bundle');

        $search = $content->createSearch();
        $termFilter = new TermQuery('category', 'commons');
        $search->addFilter($termFilter);
        $search->setSize(100);
        $commons = $content->execute($search, Result::RESULTS_ARRAY);



        return $this->render('AppBundle:Sidebar:tree.html.twig',
            [
                'bundles' => $bundles,
                'commons' => $commons,
            ]
        );
    }

    private function getList($term, $add)
    {

    }
}
