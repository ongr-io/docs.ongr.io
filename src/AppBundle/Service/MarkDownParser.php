<?php

namespace AppBundle\Service;

use cebe\markdown\GithubMarkdown;

class MarkDownParser extends GithubMarkdown
{
    private $bundle = '';

    public function __construct()
    {
        $this->html5 = true;
    }

    /**
     * @return string
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * @param string $bundle
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;
    }

    protected function renderLink($block)
    {
        if (isset($block['refkey'])) {
            if (($ref = $this->lookupReference($block['refkey'])) !== false) {
                $block = array_merge($block, $ref);
            } else {
                return $block['orig'];
            }
        }

        $attributeHtml = '';

        $urlParts = explode('/', $block['url']);
        if (is_array($urlParts)) {
            switch ($urlParts[0]) {
                case 'http:':
                case 'https:':
                    $attributeHtml = 'target="_blank"';
                    break;
                case 'Resources':
                    unset($urlParts[0], $urlParts[1]);
                    $block['url'] = '/'.$this->getBundle().'/'.implode('/', $urlParts);
                    if (substr($block['url'], -3) === '.md') {
                        $block['url'] = substr($block['url'], 0, -3);
                    }
                    break;
                case 'doc':
                case 'docs':
                    unset($urlParts[0]);
                    $block['url'] = '/'.$this->getBundle().'/'.implode('/', $urlParts);
                    if (substr($block['url'], -3) === '.md') {
                        $block['url'] = substr($block['url'], 0, -3);
                    }
                    break;
                default:
                    if (substr($block['url'], -3) === '.md') {
                        $block['url'] = '/'.$this->getBundle().'/'.substr($block['url'], 0, -3);
                    }
                    break;
            }
        }

        return '<a href="' . htmlspecialchars($block['url']) . '"'
        . (
            empty($block['title']) ?
            '' :
            ' title="' . htmlspecialchars($block['title'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, 'UTF-8') . '"'
          )
        . $attributeHtml
        . '>' . $this->renderAbsy($block['text']) . '</a>';
    }

    /**
     * {@inheritdoc}
     */
    public function parseBlocks($lines)
    {
        if (!is_array($lines)) {
            $lines = explode("\n", $lines);
        }

        return parent::parseBlocks($lines);
    }

    /**
     * Renders content as a text
     *
     * @param array $blocks
     *
     * @return string
     */
    public function renderAbsyText($blocks)
    {
        return rtrim(strip_tags(parent::renderAbsy($blocks)));
    }
// Line numbers
// TODO Do we need code line numbers?
//    protected function renderCode($block)
//    {
//        $class = isset($block['language']) ? ' class="line-numbers language-' . $block['language'] . '"' : '';
//        return "<pre><code$class>" .
//            htmlspecialchars(
//                is_array($block['content']) ? implode("\n", $block['content']) : $block['content'] . "\n", ENT_NOQUOTES, 'UTF-8') .
//            '</code></pre>';
//    }
}