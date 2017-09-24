<?php

namespace AppBundle\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebSiteSetting
 *
 * @ORM\MappedSuperclass
 */
class WebSiteSetting
{
    /**
     * @var string
     *
     * @ORM\Column(name="storeName", type="string", length=255, nullable=true)
     */
    private $siteName;

    /**
     * @var string
     *
     * @ORM\Column(name="storeEmail", type="string", length=255, nullable=true)
     */
    private $siteEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="googleAnalyticsId", type="string", length=255, nullable=true)
     */
    private $googleAnalyticsId;

    /**
     * @param string $siteName
     * @return WebSiteSetting
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * @param string $siteEmail
     * @return WebSiteSetting
     */
    public function setSiteEmail($siteEmail)
    {
        $this->siteEmail = $siteEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getSiteEmail()
    {
        return $this->siteEmail;
    }

    /**
     * @param string $googleAnalyticsId
     * @return WebSiteSetting
     */
    public function setGoogleAnalyticsId($googleAnalyticsId)
    {
        if (preg_match('/UA-[0-9]{4,10}-[0-9]{1,4}/', $googleAnalyticsId, $match)) {
            $this->googleAnalyticsId = $match[0];
        } else {
            $this->googleAnalyticsId = '';
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleAnalyticsId()
    {
        return $this->googleAnalyticsId;
    }
}
