<?php
namespace DrdPlus\RulesSkeleton;

use Granam\Strict\Object\StrictObject;
use Granam\String\StringTools;
use Gt\Dom\Element;
use Gt\Dom\HTMLCollection;
use Gt\Dom\HTMLDocument;
use Gt\Dom\Node;

class HtmlHelper extends StrictObject
{
    /**
     * @var bool
     */
    private $devMode;
    /**
     * @var bool
     */
    private $shouldHideCovered;

    public function __construct(bool $devMode, bool $shouldHideCovered)
    {
        $this->devMode = $devMode;
        $this->shouldHideCovered = $shouldHideCovered;
    }

    /**
     * @param HTMLDocument $html
     */
    public function prepareCodeLinks(HTMLDocument $html)
    {
        if (!$this->devMode) {
            foreach ($html->getElementsByClassName('source-code-title') as $withSourceCode) {
                $withSourceCode->className = str_replace('source-code-title', 'hidden', $withSourceCode->className);
                $withSourceCode->classList->remove('covered-by-code');
                $withSourceCode->classList->remove('generic');
                $withSourceCode->removeAttribute('data-source-code');
            }
        } else {
            foreach ($html->getElementsByClassName('source-code-title') as $withSourceCode) {
                $withSourceCode->appendChild($sourceCodeLink = new Element('a', 'source code'));
                $sourceCodeLink->setAttribute('class', 'source-code');
                $sourceCodeLink->setAttribute('href', $withSourceCode->getAttribute('data-source-code'));
            }
        }
    }

    /**
     * @param HTMLDocument $html
     */
    public function addIdsToTablesAndHeadings(HTMLDocument $html)
    {
        $elementNames = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'th'];
        foreach ($elementNames as $elementName) {
            /** @var Element $headerCell */
            foreach ($html->getElementsByTagName($elementName) as $headerCell) {

                if ($headerCell->getAttribute('id')) {
                    continue;
                }
                if ($elementName === 'th' && strpos(trim($headerCell->textContent), 'Tabulka') === false) {
                    continue;
                }
                $id = false;
                /** @var \DOMNode $childNode */
                foreach ($headerCell->childNodes as $childNode) {
                    if ($childNode->nodeType === XML_TEXT_NODE) {
                        $id = trim($childNode->nodeValue);
                        break;
                    }
                }
                if (!$id) {
                    continue;
                }
                $headerCell->setAttribute('id', $id);
                if ($headerCell->getAttribute('data-original-id')) {
                    continue;
                }
                $headerCell->setAttribute('data-original-id', $id);
            }
        }
    }

    /**
     * @param HTMLDocument $html
     */
    public function addAnchorsToIds(HTMLDocument $html)
    {
        $this->addAnchorsToChildrenWithIds($html->body->children);
        $withoutDiacritics = preg_replace_callback(
            '~\s+(?:id\s*="|href\s*="#)(?<name>[^"]+)"~',
            function ($matches) {
                return str_replace($matches['name'], StringTools::toConstant($matches['name']), $matches[0]);
            },
            $html->body->innerHTML
        );
        $withAnchorsToOriginalIds = preg_replace(
            '~<(([[:alnum:]]+)(?:(?!data-original-id=")[^>])*)data-original-id\s*=\s*"([^"]+)"\s*([^>]*)>((?:(?!</\2>).)+)</\2>~is',
            '<$1 $4><span id="$3" class="invisible-id">#$3</span>$5</$2>',
            $withoutDiacritics
        );

        $html->body->innerHTML = $withAnchorsToOriginalIds;
    }

    public function addAnchorsToChildrenWithIds(HTMLCollection $children)
    {
        foreach ($children as $child) {
            if ($child->id) {
                $anchorToChildItself = false;
                /** @var \DOMNode $childNode */
                foreach ($child->childNodes as $childNode) {
                    if ($childNode->nodeType === XML_TEXT_NODE) {
                        $anchorToChildItself = new Element('a');
                        $child->replaceChild($anchorToChildItself, $childNode);
                        $anchorToChildItself->nodeValue = $childNode->nodeValue;
                        break;
                    }
                }
                if (!$anchorToChildItself) {
                    continue;
                }
                $anchorToChildItself->setAttribute('href', '#' . $child->id);
                foreach ($child->childNodes as $childNode) {
                    if ($childNode === $anchorToChildItself) {
                        continue;
                    }
                    $child->removeChild($childNode);
                    $anchorToChildItself->appendChild($childNode);
                }
            }
            // recursion
            $this->addAnchorsToChildrenWithIds($child->children);
        }
    }

    /**
     * @param HTMLDocument $html
     */
    public function hideCovered(HTMLDocument $html)
    {
        if (!$this->devMode || !$this->shouldHideCovered) {
            return;
        }
        /** @var Node $image */
        foreach ($html->getElementsByTagName('img') as $image) {
            $html->removeChild($image);
        }
        $classesToHide = ['covered-by-code', 'introduction', 'quote', 'generic', 'note', 'excluded'];
        foreach ($classesToHide as $classToHide) {
            foreach ($html->getElementsByClassName($classToHide) as $nodeToHide) {
                $nodeToHide->className = str_replace($classToHide, 'hidden', $nodeToHide->className);
            }
        }
    }
}