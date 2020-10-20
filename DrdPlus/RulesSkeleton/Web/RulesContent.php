<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web;

use DrdPlus\RulesSkeleton\Cache;
use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\CurrentWebVersion;
use DrdPlus\RulesSkeleton\Redirect;
use DrdPlus\RulesSkeleton\Web\Menu\MenuBody;
use DrdPlus\RulesSkeleton\Web\Menu\MenuBodyInterface;
use Granam\Strict\Object\StrictObject;
use Granam\String\StringInterface;
use Granam\WebContentBuilder\HtmlDocument;
use Granam\WebContentBuilder\Web\Body;
use Granam\WebContentBuilder\Web\HtmlContentInterface;

class RulesContent extends StrictObject implements StringInterface
{
    public const TABLES = 'tables';
    public const FULL = ' full';
    public const PDF = 'pdf';
    public const GATEWAY = 'gateway';
    public const NOT_FOUND = 'not_found';

    /** @var HtmlContentInterface */
    private $htmlContent;
    /** @var CurrentWebVersion */
    private $currentWebVersion;
    /** @var Head */
    private $head;
    /** @var MenuBodyInterface */
    private $menuBody;
    /** @var Body */
    private $body;
    /** @var Cache */
    private $cache;
    /** @var string */
    private $contentType;
    /** @var Redirect|null */
    private $redirect;
    /** @var HtmlDocument */
    private $htmlDocument;

    public function __construct(
        HtmlContentInterface $htmlContent,
        MenuBodyInterface $menuBody,
        CurrentWebVersion $currentWebVersion,
        Cache $cache,
        string $contentType,
        ?Redirect $redirect
    )
    {
        $this->htmlContent = $htmlContent;
        $this->currentWebVersion = $currentWebVersion;
        $this->menuBody = $menuBody;
        $this->cache = $cache;
        $this->contentType = $contentType;
        $this->redirect = $redirect;
    }

    public function containsTables(): bool
    {
        return $this->contentType === self::TABLES;
    }

    public function containsFull(): bool
    {
        return $this->contentType === self::FULL;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        if ($this->containsPdf()) {
            return $this->htmlContent->getValue();
        }
        $cachedContent = $this->getCachedContent();
        if ($cachedContent !== null) {
            // redirection is not cached
            return $this->injectRedirectIfAny($cachedContent);
        }
        $previousMemoryLimit = \ini_set('memory_limit', '1G');
        $htmlDocument = $this->buildHtmlDocument();
        $updatedContent = $htmlDocument->saveHTML();
        $this->cache->cacheContent($updatedContent);
        if ($previousMemoryLimit !== false) {
            \ini_set('memory_limit', $previousMemoryLimit);
        }

        // has to be AFTER cache as we do not want to cache it
        return $this->injectRedirectIfAny($updatedContent);
    }

    protected function buildHtmlDocument(): HtmlDocument
    {
        if ($this->htmlDocument === null) {
            $htmlDocument = $this->htmlContent->getHtmlDocument();

            $this->injectCacheStamp($htmlDocument);
            $this->injectMenuWrapper($htmlDocument);
            $this->injectCacheId($htmlDocument);
            $this->injectBackgroundWallpaper($htmlDocument);

            $this->htmlDocument = $htmlDocument;
        }

        return $this->htmlDocument;
    }

    private function injectCacheStamp(HtmlDocument $htmlDocument): void
    {
        $patchVersion = $this->currentWebVersion->getCurrentPatchVersion();
        $htmlDocument->documentElement->setAttribute('data-content-version', $patchVersion);
        $htmlDocument->documentElement->setAttribute('data-cached-at', \date(\DATE_ATOM));
    }

    private function injectMenuWrapper(HtmlDocument $htmlDocument): void
    {
        $menuWrapper = $htmlDocument->createElement('div');
        $menuWrapper->setAttribute('id', HtmlHelper::ID_MENU_WRAPPER);
        $menuWrapper->prop_set_innerHTML($this->menuBody->getValue());
        $htmlDocument->body->insertBefore($menuWrapper, $htmlDocument->body->firstElementChild);
    }

    private function injectCacheId(HtmlDocument $htmlDocument): void
    {
        $htmlDocument->documentElement->setAttribute(HtmlHelper::DATA_CACHE_STAMP, $this->cache->getCacheId());
    }

    private function injectBackgroundWallpaper(HtmlDocument $htmlDocument): void
    {
        $this->injectBackgroundWallpaperPart($htmlDocument, HtmlHelper::CLASS_BACKGROUND_WALLPAPER_RIGHT_PART);
        $this->injectBackgroundWallpaperPart($htmlDocument, HtmlHelper::CLASS_BACKGROUND_WALLPAPER_LEFT_PART);
    }

    private function injectBackgroundWallpaperPart(HtmlDocument $htmlDocument, string $htmlClass): void
    {
        $backgroundWallpaper = $htmlDocument->createElement('div');
        $backgroundWallpaper->classList->add($htmlClass);
        $backgroundWallpaper->classList->add(HtmlHelper::CLASS_BACKGROUND_WALLPAPER);
        $backgroundWallpaper->classList->add(HtmlHelper::CLASS_BACKGROUND_RELATED);
        $htmlDocument->body->insertBefore($backgroundWallpaper, $htmlDocument->body->firstElementChild);
    }

    private function getCachedContent(): ?string
    {
        if ($this->cache->isCacheValid()) {
            return $this->cache->getCachedContent();
        }

        return null;
    }

    private function injectRedirectIfAny(string $content): string
    {
        if (!$this->getRedirect()) {
            return $content;
        }
        $cachedDocument = new HtmlDocument($content);
        $meta = $cachedDocument->createElement('meta');
        $meta->setAttribute('http-equiv', 'Refresh');
        $meta->setAttribute('content', $this->getRedirect()->getAfterSeconds() . '; url=' . $this->getRedirect()->getTarget());
        $meta->setAttribute('id', 'meta_redirect');
        $cachedDocument->head->appendChild($meta);

        return $cachedDocument->saveHTML();
    }

    private function getRedirect(): ?Redirect
    {
        return $this->redirect;
    }

    public function containsPdf(): bool
    {
        return $this->contentType === self::PDF;
    }

    public function containsPass(): bool
    {
        return $this->contentType === self::GATEWAY;
    }

    public function containsNotFound(): bool
    {
        return $this->contentType === self::NOT_FOUND;
    }

}