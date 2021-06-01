<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tag = new Tag();
        $tag->setTitle('kaamelott');
        $tag->setDescription('Ttrucs à faire à Kaamelott');
        $tag->setColor('#5ae216');
        $manager->persist($tag);

        $tag = new Tag();
        $tag->setTitle('taverne');
        $tag->setDescription('Ttrucs à faire à la taverne');
        $tag->setColor('#f0fc05');
        $manager->persist($tag);

        $manager->flush();
    }
}
