<?php

namespace AppBundle\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * DomainSetting
 *
 * @ORM\MappedSuperclass
 */
class DomainSetting
{
    /**
     * @var string
     *
     * @ORM\Column(name="systemDomain", type="string", length=255, nullable=true)
     */
    private $systemDomain;

    /**
     * @var string
     *
     * @ORM\Column(name="primaryDomain", type="string", length=255, nullable=true)
     */
    private $primaryDomain;

    /**
     * @var string
     *
     * @ORM\Column(name="checkoutDomain", type="string", length=255, nullable=true)
     */
    private $checkoutDomain;

    /**
     * @param string $domain
     * @return DomainSetting
     */
    public function setSystemDomain($domain)
    {
        $this->systemDomain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getSystemDomain()
    {
        return $this->systemDomain;
    }

    /**
     * @param string $domain
     * @return DomainSetting
     */
    public function setPrimaryDomain($domain)
    {
        $this->primaryDomain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryDomain()
    {
        return $this->primaryDomain;
    }

    /**
     * @param string $domain
     * @return DomainSetting
     */
    public function setCheckoutDomain($domain)
    {
        $this->checkoutDomain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckoutDomain()
    {
        return $this->checkoutDomain;
    }
}
