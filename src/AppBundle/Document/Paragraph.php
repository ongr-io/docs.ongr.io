<?php

namespace AppBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;

/**
 * @ES\Object
 */
class Paragraph
{
    /**
     * @var string
     *
     * @ES\Property(type="string", options={"analyzer": "whitespace"})
     */
    private $value;

    /**
     * @var int
     *
     * @ES\Property(type="integer")
     */
    private $level;
    
    /**
     * Constructor
     *
     * @param string $value
     * @param int    $level
     */
    public function __construct($value, $level = 1)
    {
        $this->value = $value;
        $this->level = $level;
    }

    /**
     * Sets value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * Returns value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets level
     *
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Returns level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }
}
