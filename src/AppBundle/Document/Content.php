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
     * @var string
     *
     * @ES\Id()
     */
    private $id;

    /**
     * @var string
     *
     * @ES\Property(type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ES\Property(type="string")
     */
    private $content;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"index":"not_analyzed"})
     */
    private $version;

    /**
     * @var string
     *
     * @ES\Property(type="string",
     *     options={
     *     "index":"not_analyzed",
     *     "fields"={
     *         "tokens"={"type"="string", "analyzer"="pathAnalyzer"}
     *      }
     *     }
     * )
     */
    private $path;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"index":"not_analyzed"})
     */
    private $bundle;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"index":"not_analyzed"})
     */
    private $org;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"index":"not_analyzed"})
     */
    private $repo;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"index":"not_analyzed"})
     */
    private $sha;

    /**
     * @var string
     *
     * @ES\Property(type="string")
     */
    private $description = '';

    /**
     * @var string
     *
     * @ES\Property(type="string")
     */
    private $menuTitle;

    /**
     * @var Collection
     *
     * @ES\Embedded(class="AppBundle:Paragraph", multiple=true)
     */
    private $headlines;

    /**
     * @var Collection
     *
     * @ES\Embedded(class="AppBundle:Paragraph", multiple=true)
     */
    private $paragraphs;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->headlines = new Collection();
        $this->paragraphs = new Collection();
    }

    /**
     * Sets id
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets content
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Returns content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets version
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Returns version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Returns path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets bundle
     *
     * @param string $bundle
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * Returns bundle
     *
     * @return string
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * Sets organization
     *
     * @param string $org
     */
    public function setOrg($org)
    {
        $this->org = $org;
    }

    /**
     * Returns organization
     *
     * @return string
     */
    public function getOrg()
    {
        return $this->org;
    }

    /**
     * Sets repository
     *
     * @param string $repo
     */
    public function setRepo($repo)
    {
        $this->repo = $repo;
    }

    /**
     * Returns repository
     *
     * @return string
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * Sets secure hash
     *
     * @param string $sha
     */
    public function setSha($sha)
    {
        $this->sha = $sha;
    }

    /**
     * Returns secure hash
     *
     * @return string
     */
    public function getSha()
    {
        return $this->sha;
    }

    /**
     * Sets description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets title displayed in menu only
     *
     * @param string $menuTitle
     */
    public function setMenuTitle($menuTitle)
    {
        $this->menuTitle = $menuTitle;
    }

    /**
     * Returns title displayed in menu only
     *
     * @return string
     */
    public function getMenuTitle()
    {
        return $this->menuTitle;
    }

    /**
     * Adds headline
     *
     * @param Paragraph $headline
     */
    public function addHeadline(Paragraph $headline)
    {
        $this->headlines[] = $headline;
    }

    /**
     * Sets headlines
     *
     * @param Collection $headlines
     */
    public function setHeadlines(Collection $headlines)
    {
        $this->headlines = $headlines;
    }

    /**
     * Returns headlines
     *
     * @return Collection
     */
    public function getHeadlines()
    {
        return $this->headlines;
    }

    /**
     * Adds paragraph
     *
     * @param Paragraph $paragraph
     */
    public function addParagraph(Paragraph $paragraph)
    {
        $this->paragraphs[] = $paragraph;
    }

    /**
     * Sets paragraphs
     *
     * @param Collection $paragraphs
     */
    public function setParagraphs(Collection $paragraphs)
    {
        $this->paragraphs = $paragraphs;
    }

    /**
     * Returns paragraphs
     *
     * @return Collection
     */
    public function getParagraphs()
    {
        return $this->paragraphs;
    }
}
