<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Article;
use Symfony\Component\Yaml\Parser;

class LoadArticleData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $yaml = new Parser();
        $items = $yaml->parse(file_get_contents(__DIR__ . '/../Data/articles.yml'));
        if ($items) {
            foreach ($items as $item) {
                $a = new Article();
                try {
                    $imagePath = str_replace('\"', "", $item['img']['path']);
                    $p = $this->getReference($item['category']);
                    $a
                        ->setTitle($item['title'])
                        ->setUrl($item['url'])
                        ->setImage($imagePath)
                        ->setContent($item['content'])
                        ->setStatus('public')
                        ->setPageTree($p)
                        ->setCreateTime(new \DateTime($item['created_at']))
                        ->setUpdateTime(new \DateTime($item['created_at']));
                    ;
                } catch (\Exception $ex) {
                    var_dump($item['item']);
                    throw $ex;
                }
                $manager->persist($a);
            }
        }
        $manager->flush();

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
