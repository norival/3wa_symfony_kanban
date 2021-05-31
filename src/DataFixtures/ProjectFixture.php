<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\UserProject;
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
        $userProject = new UserProject();
        $userProject->setRole(UserProject::ROLE_ADMIN);
        $userProject->addUser($this->getReference(UserFixtures::USER_PERCEVAL));
        $manager->persist($userProject);
        $project->addUserProject($userProject);
        $userProject = new UserProject();
        $userProject->setRole(UserProject::ROLE_USER);
        $userProject->addUser($this->getReference(UserFixtures::USER_LANCELOT));
        $manager->persist($userProject);
        $project->addUserProject($userProject);
        $manager->persist($project);

        $project = new Project();
        $project->setTitle('Séduire Gueunièvre');
        $project->setDescription('Je dois voler Gueunièvre à Arthur');
        $userProject = new UserProject();
        $userProject->setRole(UserProject::ROLE_ADMIN);
        $userProject->addUser($this->getReference(UserFixtures::USER_LANCELOT));
        $manager->persist($userProject);
        $project->addUserProject($userProject);
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
