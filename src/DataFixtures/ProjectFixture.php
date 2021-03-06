<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $project = new Project();
        $project->setTitle('Trouver le Graal');
        $project->setDescription('Il faut trouver le Graal pour Arthur !');
        $project->setSlug('trouver_le_graal');
        $project->setAdmin($this->getReference(UserFixtures::USER_PERCEVAL));
        $project->addUser($this->getReference(UserFixtures::USER_LANCELOT));
        $manager->persist($project);

        $project = new Project();
        $project->setTitle('Séduire Gueunièvre');
        $project->setDescription('Je dois voler Gueunièvre à Arthur');
        $project->setSlug('seduire_gueunievre');
        $project->setAdmin($this->getReference(UserFixtures::USER_LANCELOT));
        $project->addUser($this->getReference(UserFixtures::USER_PERCEVAL));
        $manager->persist($project);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
