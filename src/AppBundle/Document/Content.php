<?php

namespace AppBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\RouterBundle\Document\SeoAwareTrait;

/**
 * @ES\Document(type="content")
 */
class Content
{
    use SeoAwareTrait;

    /**
     * @ES\Property(name="title", type="string")
     */
    public $title;

    /**
     * @ES\Property(name="content", type="string")
     */
    public $content;

    /**
     * @ES\Property(name="version", type="string", options={"index":"not_analyzed"})
     */
    public $version;

    /**
     * @ES\Property(name="path", type="string",
     *     options={
     *     "index":"not_analyzed",
     *     "fields"={
     *         "tokens"={"type"="string", "analyzer"="pathAnalyzer"}
     *      }
     *     }
     * )
     */
    public $path;

    /**
     * @ES\Property(name="category", type="string", options={"index":"not_analyzed"})
     */
    public $category;

    /**
     * @ES\Property(name="bundle", type="string", options={"index":"not_analyzed"})
     */
    public $bundle;
}
