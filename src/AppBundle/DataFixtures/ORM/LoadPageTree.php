<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\PageTree;
use AppBundle\Entity\PageTreeTranslation;
use Symfony\Component\Yaml\Parser;

class LoadPageTree extends  AbstractFixture implements OrderedFixtureInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param Array $item
     * @param ObjectManager $manager
     * @param null|PageTree $parent
     */
    private function persistPageTree($item, $manager, $parent = null)
    {
        $p = new PageTree();
        $p
            ->setTitle($item['title'])
            ->setUrl($item['url'])
            //->setPosition($item['position'])
            ->setContent($item['content'])
            ->setMetaDescription($item['metaDescription'])
            ->setTranslatableLocale('ru_RU')
            ->addTranslation(new PageTreeTranslation('kg_KG', 'title', $item['title_kg']))
            //->addTranslation(new PageTreeTranslation('kg_KG', 'content', $item['content']))
        ;
        if ($parent) {
            $p->setParent($parent);
        }

        $manager->persist($p);
        $this->addReference($p->getUrl(), $p);
        if (isset($item['children'])) {
            foreach ($item['children'] as $item) {
                $this->persistPageTree($item, $manager, $p);
            }
        }
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $yaml = new Parser();
        $items = $yaml->parse(file_get_contents(__DIR__ . '/../Data/pageTree.yml'));
        if ($items) {
            foreach ($items as $item) {
                $this->persistPageTree($item, $manager);
            }
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }
}