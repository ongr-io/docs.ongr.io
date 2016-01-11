<?php

namespace AppBundle\Twig;

use AppBundle\Document\Content;
use ONGR\ElasticsearchBundle\Result\Result;
use ONGR\ElasticsearchBundle\Service\Repository;
use ONGR\ElasticsearchDSL\Filter\NotFilter;
use ONGR\ElasticsearchDSL\Query\MatchQuery;
use ONGR\ElasticsearchDSL\Query\TermQuery;

class BundleContentListExtension extends \Twig_Extension
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * BundleContentListExtension constructor.
     *
     * @param Repository $repository
     */
    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bundle-content-list';
    }

    /**
     * Provide a list of helper functions to be used.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'bundle_content_tree',
                [
                    $this,
                    'getBundleContentTree',
                ],
                [
                    'needs_environment' => false,
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * @param string $bundle
     * @return string
     */
    public function getBundleContentTree($bundle)
    {
        #TODO Should be exchanged to path hierarchy aggregation or smth.
        $matchQuery = new MatchQuery('bundle', $bundle);
        $termFilter = new TermQuery('path', 'README.md');
        $notFilter = new NotFilter($termFilter);

        $search = $this->repository->createSearch();
        $search->addFilter($notFilter);
        $search->addQuery($matchQuery);
        $search->setSize(1000);

        $results = $this->repository->execute($search, Result::RESULTS_OBJECT);

        $tree = [];
        foreach ($results as $doc) {
            $path = explode('/', $doc->path);
            $tree = array_merge_recursive($tree, $this->pathToDoc($path, [], $doc));
        }

        $html = $this->renderToHtmlList($tree, "");

        return $html;
    }

    /**
     * Splits path to hierarchical array.
     *
     * @param array $path
     * @param $out
     * @param $doc
     * @return mixed
     */
    private function pathToDoc(array $path, $out, $doc)
    {
        if (count($path) == 0) {
            return $doc;
        }
        while ($key = array_shift($path)) {
            $out[$key] = $this->pathToDoc($path, $out, $doc);
            break;
        }

        return $out;
    }

    private function renderToHtmlList($array, $class = "")
    {
        $html = '<ul class="'.$class.'">';
        foreach ($array as $key => $node) {
            if (is_object($node)) {
                $html .= '<li><a href="'.$node->url.'">'.$this->prepareTitle($node->title).'</a></li>';
            } else {
                $html .= '<li>'.'<a class="sidebar-dropdown" href="javascript:void(1)">'.$this->prepareTitle($key).'</a>';
//                $html .= $this->renderToHtmlList($node, 'hidden');
                $html .= $this->renderToHtmlList($node, '');
                $html .= '</li>';
            }

        }
        $html .= '</ul>';
        return $html;
    }

    private function prepareTitle($str)
    {
        return ucfirst(str_replace(['_', '-'], ' ',$str));
    }
}