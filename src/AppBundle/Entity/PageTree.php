<?php

namespace AppBundle\Entity;

use AppBundle\Entity\PageTreeTranslation;
#use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;

/**
 * PageTree
 *
 * @Gedmo\Tree(type="closure")
 * @Gedmo\TreeClosure(class="AppBundle\Entity\PageTreeClosure")
 * @Gedmo\TranslationEntity(class="AppBundle\Entity\PageTreeTranslation")
 * @ORM\Table(name="pagetree")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\ClosureTreeRepository")
 */
class PageTree implements Translatable, \Serializable
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=64)
     * @Gedmo\Translatable
     */
    private $title;

    /**
     * @ORM\Column(name="pageType", type="string", length=64)
     */
    private $pageType;

    /**
     * @var
     * @Gedmo\Locale
     */
    private $locale;

    /**
     * This parameter is optional for the closure strategy
     *
     * @ORM\Column(name="level", type="integer", nullable=true)
     * @Gedmo\TreeLevel
     */
    private $level;

    /**
     * @Gedmo\TreeParent
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="PageTree", inversedBy="children")
     */
    private $parent;

    /**
     * @var String
     * @ORM\Column(name="url", type="string", length=255, unique=true, nullable=true)
     */
    private $url;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;

    /**
     * @var String
     * @ORM\Column(name="content", type="text", nullable=true)
     * @Gedmo\Translatable
     */
    private $content;

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
     * @ORM\OneToMany(
     *   targetEntity="AppBundle\Entity\PageTreeTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * method used when values is set throught a type collection (add new throught the data-prototype)
     *
     * @param $at
     * @return $this
     */
    public function setTranslations($at)
    {
        foreach ($at as $t) {
            $this->addTranslation($t);
        }
        return $this;
    }

    public function addTranslation(PageTreeTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
        return $this;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function findTranslation($locale, $field)
    {
        $id = $this->id;
        $existingTranslation = $this->translations ? $this->translations->filter(
            function($object) use ($locale, $field, $id) {
                return ($object && ($object->getLocale() === $locale) && ($object->getField() === $field));
            })->first() : null;
        return $existingTranslation;
    }

    /**
     * Remove translation
     *
     * @param PageTreeTranslation $translation
     */
    public function removeTranslation(PageTreeTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    public function getArticles()
    {
        return $this->articles;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setParent(PageTree $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function addClosure(PageTreeClosure $closure)
    {
        $this->closures[] = $closure;
    }

    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return String
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * @param $pageType
     * @return $this
     */
    public function setPageType($pageType)
    {
        $this->pageType = $pageType;

        return $this;
    }

    /**
     * @return String
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param String $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return String
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param String $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaTitle
     * @return $this
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     * @return $this
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $metaKeywords
     * @return $this
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle() ?: '';
    }

    public function serialize()
    {
        return serialize(array(
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url,
            'status' => $this->status,
            'content' => $this->content,
            'metaTitle' => $this->metaTitle,
            'metaDescription' => $this->metaDescription,
            'metaKeywords' => $this->metaKeywords,
            'level' => $this->level,
            'parent' => $this->parent
        ));
    }

    public function unserialize($serialized)
    {
        $values = unserialize($serialized);
        $this->id = $values['id'];
        $this->title = $values['title'];
        $this->url = $values['url'];
        $this->status = $values['status'];
        $this->content = $values['content'];
        $this->metaTitle = $values['metaTitle'];
        $this->metaDescription = $values['metaDescription'];
        $this->metaKeywords = $values['metaKeywords'];
        $this->level = $values['level'];
        $this->parent = $values['parent'];
    }
}
