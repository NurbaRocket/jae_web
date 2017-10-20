<?php

namespace Jae\EnergoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Subscriber
 *
 * @ORM\Table(name="subscriber")
 *
 */
class Subscriber
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var String name
     *
     * @ORM\Column(name="name", type="string", length=225)
     */
    private $name;

    /**
     * @var Integer
     * @ORM\Column(name="balance", type="integer")
     */
    private $balance;

    /**
     * @var
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;


}
