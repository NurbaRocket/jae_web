<?php

namespace AppBundle\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * SEOSetting
 *
 * @ORM\MappedSuperclass
 */
class SEOSetting
{
    /**
     * @var string
     *
     * @ORM\Column(name="metaTitle", type="string", length=255, nullable=true)
     */
    private $metaTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="metaDescription", type="string", length=1000, nullable=true)
     */
    private $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="metaKeywords", type="string", length=1000, nullable=true)
     */
    private $metaKeywords;

    /**
     * Set metaTitle
     *
     * @param  string  $metaTitle
     * @return WebSiteSetting
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Get metaTitle
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Set metaDescription
     *
     * @param  string  $metaDescription
     * @return WebSiteSetting
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set metaKeywords
     *
     * @param  string  $metaKeywords
     * @return WebSiteSetting
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }
}
