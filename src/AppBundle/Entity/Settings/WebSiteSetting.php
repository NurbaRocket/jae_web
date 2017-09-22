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
    private $storeName;

    /**
     * @var string
     *
     * @ORM\Column(name="storeEmail", type="string", length=255, nullable=true)
     */
    private $storeEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="googleAnalyticsId", type="string", length=255, nullable=true)
     */
    private $googleAnalyticsId;

    /**
     * @param string $storeName
     * @return WebSiteSetting
     */
    public function setStoreName($storeName)
    {
        $this->storeName = $storeName;

        return $this;
    }

    /**
     * @return string
     */
    public function getStoreName()
    {
        return $this->storeName;
    }

    /**
     * @param string $storeEmail
     * @return WebSiteSetting
     */
    public function setStoreEmail($storeEmail)
    {
        $this->storeEmail = $storeEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getStoreEmail()
    {
        return $this->storeEmail;
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
