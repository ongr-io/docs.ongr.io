<?php

namespace AppBundle\Controller;

use ONGR\ElasticsearchBundle\Result\Result;
use ONGR\ElasticsearchBundle\Service\Repository;
use ONGR\ElasticsearchDSL\Query\FunctionScoreQuery;
use ONGR\ElasticsearchDSL\Query\QueryStringQuery;
use ONGR\ElasticsearchDSL\Search;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Docs pages Controller for the ONGR routing component
 */
class DocController extends Controller
{
    /**
     * Documentation page render action. $document is actually an instance of the Document.
     *
     * @param object $document
     *
     * @return Response
     */
    public function documentAction($document)
    {
        return $this->render(
            'AppBundle:Doc:document.html.twig',
            [
                'document' => $document,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {
        /** @var Repository $repository */
        $repository = $this->get('es.manager.default.content');
        $search = new Search();
        $stringQuery = new QueryStringQuery(
            $this->getQuery($request),
            ['fields' => ['title^3', 'headlines.value^2', 'paragraphs.value'], 'fuzziness' => 2]
        );

        $functionScore = new FunctionScoreQuery($stringQuery);
        $functionScore->addScriptScoreFunction("1.0 * _score / doc['headlines.level'].value");

        $search->addQuery($functionScore);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($repository->execute($search->setSize(5), Result::RESULTS_ARRAY));
        }

        return $this->render(
            'AppBundle:Doc:list.html.twig',
            [
                'results' => $repository->execute($search),
                'term' => $request->get('q'),
            ]
        );
    }

    /**
     * Returns search query
     *
     * @param Request $request
     *
     * @return string
     */
    private function getQuery(Request $request)
    {
        $terms = array_filter(preg_split('/[^\w]+/', $request->get('q')));

        foreach ($terms as &$term) {
            $term .= '~';
        }

        return implode(' ', $terms);
    }
}
