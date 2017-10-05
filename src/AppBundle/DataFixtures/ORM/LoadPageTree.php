<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\PageTree;
use AppBundle\Entity\Translation\PageTreeTranslation;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\HttpFoundation\File\File;
use Application\Sonata\MediaBundle\Entity\Media;

class LoadPageTree extends  AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

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
            ->setPageType($item['pageType'])
            ->setUrl($item['url'])
            ->setContent($item['content'])
            ->setTranslatableLocale('ru')
            ->addTranslation(new PageTreeTranslation('kg', 'title', $item['title_kg']))
            ->addTranslation(new PageTreeTranslation('kg', 'content', !array_key_exists('content_kg', $item) ? $item['content'] : $item['content_kg']))
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

    private function registerPageFiles()
    {
        $mediaManager = $this->getMediaManager();
        $fileName = 'kg_new_age_steps.doc';
        $imageFile = new File(__DIR__ . '/../../../../web/uploads/media/' . $fileName);
        $media = new Media();
        $media->setBinaryContent($imageFile);
        $media->setEnabled(true);
        $media->setName($fileName);
        $media->setContext('files');
        $media->setProviderName('sonata.media.provider.file');
        $mediaManager->save($media);
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $yaml = new Parser();
        $items = $yaml->parse(file_get_contents(__DIR__ . '/../Data/category.yml'));
        if ($items) {
            foreach ($items as $item) {
                $this->persistPageTree($item, $manager);
            }
        }

        $manager->flush();
    }

    /**
     * @return \Sonata\MediaBundle\Model\MediaManagerInterface
     */
    public function getMediaManager()
    {
        return $this->container->get('sonata.media.manager.media');
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
