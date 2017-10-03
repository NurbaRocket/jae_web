<?php

namespace AppBundle\Entity;

use Gedmo\Translatable\Translatable;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @Gedmo\TranslationEntity(class="AppBundle\Entity\Translation\ArticleTranslation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
 */

class Article implements Translatable, TranslatableInterface
{
    use PersonalTranslatableTrait;

   /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
   private $id;

   /**
    * @var String title
    *
    * @ORM\Column(name="title", type="string", length=225)
    * @Gedmo\Translatable
    */
   private $title;

    /**
     * @var String
     * @ORM\Column(name="url", type="string", length=255, unique=true, nullable=true)
     */
    private $url;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     * <many-to-one field="image"  target-entity="Application\Sonata\MediaBundle\Entity\Media">
     *   <cascade><cascade-all/></cascade>
     * </many-to-one>
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $media;

   /**
    * @var String text
    *
    * @ORM\Column(name="content", type="text")
    * @Gedmo\Translatable
    */
   private $content;


   /**
    * @var String tags
    *
    * @ORM\Column(name="tags", type="string", length=100, nullable=true)
    */
   private $tags;


   /**
    * @var String status
    *
    * @ORM\Column(name="status", type="string", length=100)
    */
   private $status;


   /**
    * @var \DateTime createTime
    *
    * @ORM\Column(name="createTime", type="datetime", length=100)
    */
   private $createTime;

   /**
    * @var \DateTime updateTime
    *
    * @ORM\Column(name="updateTime", type="datetime", length=100)
    */
   private $updateTime;

    /**
     * @ORM\ManyToOne(targetEntity="PageTree", inversedBy="articles")
     */
    private $pageTree;

    /**
     * @ORM\OneToMany(
     *   targetEntity="AppBundle\Entity\Translation\ArticleTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
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
     *
     * @return $this
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Set tags
     *
     * @param string $tags
     *
     * @return Article
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Article
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return Article
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     *
     * @return Article
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;

        return $this;
    }

    /**
     * Get updateTime
     *
     * @return \DateTime
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    public function setPageTree(PageTree $pageTree)
    {
        $this->pageTree = $pageTree;

        return $this;
    }

    public function getPageTree()
    {
        return $this->pageTree;
    }
}
