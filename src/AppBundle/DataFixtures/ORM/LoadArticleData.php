<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Article;
use Symfony\Component\Yaml\Parser;

class LoadArticleData implements FixtureInterface, OrderedFixtureInterface
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
                    $a
                        ->setTitle($item['title'])
                        ->setUrl($item['url'])
                        ->setImage($imagePath)
                        ->setContent($item['content'])
                        ->setStatus('public')
                        ->setCreateTime(new \DateTime($item['created_at']))
                        ->setUpdateTime(new \DateTime($item['created_at']));
                    ;
                } catch (\Exception $ex) {
                    throw $ex;
                }
                $manager->persist($a);
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
        return 3;
    }
}
