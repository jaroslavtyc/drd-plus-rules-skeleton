<?php
declare(strict_types=1);
/** be strict for parameter types, https://www.quora.com/Are-strict_types-in-PHP-7-not-a-bad-idea */

namespace DrdPlus\Tests\RulesSkeleton;

class TestsConfiguration extends \DrdPlus\Tests\FrontendSkeleton\TestsConfiguration
{
    public const HAS_PROTECTED_ACCESS = 'has_protected_access';
    public const CAN_BE_BOUGHT_ON_ESHOP = 'can_be_bought_on_eshop';
    public const HAS_CHARACTER_SHEET = 'has_character_sheet';
    public const HAS_LINKS_TO_JOURNALS = 'has_links_to_journals';
    public const HAS_LINK_TO_SINGLE_JOURNAL = 'has_link_to_single_journal';
    public const HAS_AUTHORS = 'has_authors';

    // every setting SHOULD be strict (expecting instead of ignoring)

    /** @var bool */
    private $hasProtectedAccess = true;
    /** @var bool */
    private $canBeBoughtOnEshop = true;
    /** @var bool */
    private $hasCharacterSheet = true;
    /** @var bool */
    private $hasLinksToJournals = true;
    /** @var bool */
    private $hasLinkToSingleJournal = true;
    /** @var bool */
    private $hasDebugContacts = true;
    /** @var bool */
    private $hasIntroduction = true;
    /** @var bool */
    private $hasAuthors = true;
    /** @var array|string[] */
    private $blockNamesToExpectedContent = ['just-some-block' => <<<HTML
<div class="block-just-some-block">
    First part of some block
</div>

<div class="block-just-some-block">
    Second part of some block
</div>

<div class="block-just-some-block">
    Last part of some block
</div>
HTML
    ];

    /**
     * @return bool
     */
    public function hasProtectedAccess(): bool
    {
        return $this->hasProtectedAccess;
    }

    /**
     * @return TestsConfiguration
     */
    public function disableHasProtectedAccess(): TestsConfiguration
    {
        $this->hasProtectedAccess = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function canBeBoughtOnEshop(): bool
    {
        return $this->canBeBoughtOnEshop;
    }

    /**
     * @return TestsConfiguration
     */
    public function disableCanBeBoughtOnEshop(): TestsConfiguration
    {
        $this->canBeBoughtOnEshop = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasCharacterSheet(): bool
    {
        return $this->hasCharacterSheet;
    }

    /**
     * @return TestsConfiguration
     */
    public function disableHasCharacterSheet(): TestsConfiguration
    {
        $this->hasCharacterSheet = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasLinksToJournals(): bool
    {
        return $this->hasLinksToJournals;
    }

    /**
     * @return TestsConfiguration
     */
    public function disableHasLinksToJournals(): TestsConfiguration
    {
        $this->hasLinksToJournals = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasLinkToSingleJournal(): bool
    {
        return $this->hasLinkToSingleJournal;
    }

    /**
     * @return TestsConfiguration
     */
    public function disableHasLinkToSingleJournal(): TestsConfiguration
    {
        $this->hasLinkToSingleJournal = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasDebugContacts(): bool
    {
        return $this->hasDebugContacts;
    }

    /**
     * @return TestsConfiguration
     */
    public function disableHasDebugContacts(): TestsConfiguration
    {
        $this->hasDebugContacts = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasIntroduction(): bool
    {
        return $this->hasIntroduction;
    }

    /**
     * @return TestsConfiguration
     */
    public function disableHasIntroduction(): TestsConfiguration
    {
        $this->hasIntroduction = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasAuthors(): bool
    {
        return $this->hasAuthors;
    }

    /**
     * @return TestsConfiguration
     */
    public function disableHasAuthors(): TestsConfiguration
    {
        $this->hasAuthors = false;

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getBlockNamesToExpectedContent(): array
    {
        return $this->blockNamesToExpectedContent;
    }

    /**
     * @param array $blockNamesToExpectedContent
     * @return TestsConfiguration
     */
    public function setBlockNamesToExpectedContent(array $blockNamesToExpectedContent): TestsConfiguration
    {
        $this->blockNamesToExpectedContent = $blockNamesToExpectedContent;

        return $this;
    }
}