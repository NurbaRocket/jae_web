<?php
/**
 * Created by PhpStorm.
 * User: sarbalaev
 * Date: 04.10.2017
 * Time: 12:58
 */

namespace AppBundle\Entity;


interface PageInterface {

    /**
     *
     * @return String
     */
    public function getUrl();

    /**
     *
     * @return Integer
     */
    public function getId();
}