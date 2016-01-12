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
     * @ES\Id()
     */
    public $id;
    /**
     * @ES\Property(type="string")
     */
    public $title;

    /**
     * @ES\Property(type="string")
     */
    public $content;

    /**
     * @ES\Property(type="string", options={"index":"not_analyzed"})
     */
    public $version;

    /**
     * @ES\Property(type="string",
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
     * @ES\Property(type="string", options={"index":"not_analyzed"})
     */
    public $category;

    /**
     * @ES\Property(type="string", options={"index":"not_analyzed"})
     */
    public $bundle;

    /**
     * @ES\Property(type="string", options={"index":"not_analyzed"})
     */
    public $sha;
}
