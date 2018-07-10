<?php
global $testsConfiguration;
$testsConfiguration = new \DrdPlus\Tests\RulesSkeleton\TestsConfiguration('http://rules.drdplus.loc', 'https://rules.drdplus.info');
$testsConfiguration->setSomeExpectedTableIds(['IAmSoAlone', 'JustSomeTable']);
$testsConfiguration->setExpectedWebName('HTML kostra pro DrDPlus, jakoby pravidla čaroděje');
$testsConfiguration->setExpectedPageTitle('☠️ HTML kostra pro DrDPlus, jakoby pravidla čaroděje');
$testsConfiguration->setExpectedLicence(\DrdPlus\Tests\RulesSkeleton\TestsConfiguration::LICENCE_MIT);
$testsConfiguration->addTooShortFailureName('nevšiml si')
    ->addTooShortSuccessName('všiml si')
    ->addTooShortResultName('Bonus')
    ->addTooShortResultName('Postih');