<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Yaml\Parser;
use AppBundle\Entity\Article;
use AppBundle\Entity\Translation\ArticleTranslation;
use Application\Sonata\MediaBundle\Entity\Media;

class LoadArticleData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $zipFilePath = realpath(__DIR__ . '/../Data') . '/media.zip';
        $galleryPath = realpath(__DIR__ . '/../../../../web/uploads/media');
        $zip = new \ZipArchive();
        $extracted = false;
        $fileExist = $zip->open($zipFilePath);
        if ($fileExist === true) {
            $extracted = $zip->extractTo($galleryPath);
            $zip->close();
        }
        if (!$extracted) {
            throw new \Exception('Images from ' . $zipFilePath . ' were not uploaded into the image gallery ' . $galleryPath . '.');
        }

        $yaml = new Parser();
        $items = $yaml->parse(file_get_contents(__DIR__ . '/../Data/articles.yml'));
        if ($items) {
            $mediaManager = $this->getMediaManager();
            foreach ($items as $item) {

                try {
                    $fileName = str_replace('\"', "", $item['img']['path']);

                    /** @var Media $media */
                    if ($this->hasReference($fileName)) {
                        $media = $this->getReference($fileName);
                    } else {
                        $imageFile = new File(__DIR__ . '/../../../../web/uploads/media/' . $fileName);
                        $media = $mediaManager->create();
                        $media->setBinaryContent($imageFile);
                        $media->setEnabled(true);
                        $media->setName($fileName);
                        $media->setContext('news');
                        $media->setProviderName('sonata.media.provider.image');
                        $mediaManager->save($media);
                        $this->setReference($fileName, $media);
                    }

                    $pageTree = $this->getReference($item['category']);
                    $a = new Article();
                    $a
                        ->setTitle($item['title'])
                        ->setUrl($item['url'])
                        ->setMedia($media)
                        ->setContent($item['content'])
                        ->setStatus('public')
                        ->setPageTree($pageTree)
                        ->setCreateTime(new \DateTime($item['created_at']))
                        ->setUpdateTime(new \DateTime($item['created_at']))
                        ->setTranslatableLocale('kg')
                    ;

                } catch (\Exception $ex) {
                    var_dump($item['item']);
                    throw $ex;
                }
                $manager->persist($a);
            }
            $manager->flush();
        }

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
        return 3;
    }
}
