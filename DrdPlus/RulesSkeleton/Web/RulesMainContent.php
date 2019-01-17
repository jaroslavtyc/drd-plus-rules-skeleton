<?php
declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web;

use DrdPlus\RulesSkeleton\HtmlHelper;
use Granam\WebContentBuilder\HtmlDocument;
use Granam\WebContentBuilder\Web\BodyInterface;
use Granam\WebContentBuilder\Web\HeadInterface;
use Gt\Dom\Element;

class RulesMainContent extends MainContent
{
    /** @var DebugContactsBody */
    private $debugContactsBody;

    public function __construct(
        HtmlHelper $htmlHelper,
        HeadInterface $head,
        BodyInterface $body,
        DebugContactsBody $debugContactsBody
    )
    {
        parent::__construct($htmlHelper, $head, $body);
        $this->debugContactsBody = $debugContactsBody;
    }

    protected function buildHtmlDocument(string $content): HtmlDocument
    {
        $htmlDocument = parent::buildHtmlDocument($content);
        /** @var Element $debugContactsElement */
        $debugContactsElement = $htmlDocument->createElement('div');
        $debugContactsElement->setAttribute('id', HtmlHelper::ID_DEBUG_CONTACTS);
        $debugContactsElement->prop_set_innerHTML($this->debugContactsBody->getValue());
        $htmlDocument->body->appendChild($debugContactsElement);

        return $htmlDocument;
    }
}