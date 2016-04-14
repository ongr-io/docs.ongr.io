<?php

namespace AppBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\ElasticsearchBundle\Collection\Collection;
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
    public $bundle;

    /**
     * @ES\Property(type="string", options={"index":"not_analyzed"})
     */
    public $org;

    /**
     * @ES\Property(type="string", options={"index":"not_analyzed"})
     */
    public $repo;

    /**
     * @ES\Property(type="string", options={"index":"not_analyzed"})
     */
    public $sha;

    /**
     * @var string
     *
     * @ES\Property(type="string")
     */
    public $description = '';

    /**
     * @var string
     *
     * @ES\Property(type="string")
     */
    public $menuTitle;

    /**
     * @var Collection
     *
     * @ES\Embedded(class="AppBundle:Paragraph", multiple=true)
     */
    public $headlines;

    /**
     * @var Collection
     *
     * @ES\Embedded(class="AppBundle:Paragraph", multiple=true)
     */
    public $paragraphs;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->headlines = new Collection();
        $this->paragraphs = new Collection();
    }
}
