<?php
declare(strict_types=1);
/** be strict for parameter types, https://www.quora.com/Are-strict_types-in-PHP-7-not-a-bad-idea */

namespace DrdPlus\Tests\RulesSkeleton;

use DeviceDetector\Parser\Bot;
use DrdPlus\RulesSkeleton\Request;
use DrdPlus\RulesSkeleton\UsagePolicy;
use PHPUnit\Framework\TestCase;

class UsagePolicyTest extends TestCase
{
    /**
     * @test
     * @expectedException \DrdPlus\RulesSkeleton\Exceptions\ArticleNameCanNotBeEmptyForUsagePolicy
     */
    public function I_can_not_create_it_without_article_name(): void
    {
        new UsagePolicy('', new Request(new Bot()));
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_confirm_ownership_of_visitor(): void
    {
        $_COOKIE = [];
        $usagePolicy = new UsagePolicy('foo', new Request(new Bot()));
        self::assertNotEmpty($_COOKIE);
        self::assertSame('confirmedOwnershipOfFoo', $_COOKIE['ownershipCookieName']);
        self::assertSame('trialOfFoo', $_COOKIE['trialCookieName']);
        self::assertSame('trialExpiredAt', $_COOKIE['trialExpiredAtName']);
        self::assertArrayNotHasKey('confirmedOwnershipOfFoo', $_COOKIE);
        $usagePolicy->confirmOwnershipOfVisitor($expiresAt = new \DateTime());
        self::assertSame((string)$expiresAt->getTimestamp(), $_COOKIE['confirmedOwnershipOfFoo']);
    }
}