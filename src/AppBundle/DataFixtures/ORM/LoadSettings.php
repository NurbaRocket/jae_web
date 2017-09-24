<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use AppBundle\Entity\Setting;
use AppBundle\Entity\Settings\SEOSetting;
use AppBundle\Entity\Settings\DomainSetting;
use AppBundle\Entity\Settings\WebSiteSetting;


class LoadSettings implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $webSiteSettings = new WebSiteSetting();
        $webSiteSettings
            ->setSiteName('ОАО "Жалалабатэлектро"')
            ->setSiteEmail('test@test.com')
        ;
        $setting = new Setting();
        $setting->setName('webSite');
        $setting->setValue($webSiteSettings);
        $manager->persist($setting);

        $seoSettings = new SEOSetting();
        $seoSettings->setMetaTitle('Открытое Акционерное Общество «Жалалабатэлектро»');
        $setting = new Setting();
        $setting->setName('seo');
        $setting->setValue($seoSettings);
        $manager->persist($setting);

        $domainSettings = new DomainSetting();
        $domainSettings->setSystemDomain('localhost');
        $setting = new Setting();
        $setting->setName('domain');
        $setting->setValue($domainSettings);
        $manager->persist($setting);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}
