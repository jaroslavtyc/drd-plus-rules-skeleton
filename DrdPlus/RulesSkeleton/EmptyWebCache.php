<?php
declare(strict_types=1);

namespace DrdPlus\RulesSkeleton;

use DrdPlus\FrontendSkeleton\Cache;

class EmptyWebCache extends Cache
{
    public function isCacheValid(): bool
    {
        return false;
    }

    public function getCachedContent(): string
    {
        return '';
    }

    public function saveContentForDebug(string $content): void
    {
    }

    public function cacheContent(string $content): void
    {
    }

}