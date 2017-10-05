<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Yaml\Parser;
use AppBundle\Entity\Article;
use AppBundle\Entity\Translation\ArticleTranslation;

use Application\Sonata\MediaBundle\Entity\Media;

class LoadArticleData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

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
                    $mediaName = $item['youtube'] ?: $item['img'];
                    
                    /** @var Media $media */
                    if ($this->hasReference($mediaName)) {
                        $media = $this->getReference($mediaName);
                    } else {
                        $media = $mediaManager->create();
                        $media->setEnabled(true);
                        $media->setContext('news');
                        $media->setName($mediaName);
                        if ($item['youtube'] != null) {
                            $media->setProviderName('sonata.media.provider.youtube');
                            $media->setBinaryContent('https://www.youtube.com/watch?v=' . $mediaName);
                        } else {
                            $imageFile = new File(__DIR__ . '/../../../../web/uploads/media/' . $mediaName);
                            $media->setProviderName('sonata.media.provider.image');
                            $media->setBinaryContent($imageFile);
                        }
                        $mediaManager->save($media);
                        $this->setReference($mediaName, $media);
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
                    if (array_key_exists('title_ru', $item)) {
                        $a->addTranslation(new ArticleTranslation('ru', 'title', $item['title_ru']));
                    }
                    if (array_key_exists('content_ru', $item)) {
                        $a->addTranslation(new ArticleTranslation('ru', 'content', $item['content_ru']));
                    }
                } catch (\Exception $ex) {
                    var_dump($item['url']);
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
