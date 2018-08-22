<?php
declare(strict_types=1);
/** be strict for parameter types, https://www.quora.com/Are-strict_types-in-PHP-7-not-a-bad-idea */

namespace DrdPlus\Tests\RulesSkeleton\Partials;

use DrdPlus\RulesSkeleton\Dirs;

trait DirsForTestsTrait
{
    use \DrdPlus\Tests\FrontendSkeleton\Partials\DirsForTestsTrait;

    public function getWebRoot(): string
    {
        return $this->getDocumentRoot() . '/web/passed';
    }

    protected function getGenericPartsRoot(): string
    {
        return __DIR__ . '/../../../../parts/rules-skeleton';
    }

    /**
     * @param string|null $documentRoot
     * @return \DrdPlus\FrontendSkeleton\Dirs|Dirs
     */
    protected function createDirs(string $documentRoot = null): \DrdPlus\FrontendSkeleton\Dirs
    {
        return new Dirs($documentRoot ?? $this->getDocumentRoot());
    }
}