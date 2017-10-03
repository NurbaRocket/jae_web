<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Class PageFile
 *
 * @ORM\Table(name="page_files")
 * @ORM\Entity
 */
class PageFile
{
    /**
     * @var Integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $media;

    /**
     * @var PageTree
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PageTree", inversedBy="files")
     * @ORM\JoinColumn(name="page_tree_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $pageTree;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Media $media
     * @return $this
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPageTree()
    {
        return $this->pageTree;
    }

    /**
     * @param mixed $pageTree
     * @return $this
     */
    public function setPageTree($pageTree)
    {
        $this->pageTree = $pageTree;

        return $this;
    }
}
