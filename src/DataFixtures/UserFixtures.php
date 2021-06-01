<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const USER_PERCEVAL = 'user-perceval';
    public const USER_LANCELOT = 'user-lancelot';
    public const USER_ROOT = 'user-root';

    public function __construct(private UserPasswordEncoderInterface $passwordEncoder)
    {
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('Le Gallois');
        $user->setFirstname('Perceval');
        $user->setEmail('perceval@galle.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
        $user->setIsActif(true);
        $user->setUsername('perceval');
        $this->addReference(self::USER_PERCEVAL, $user);

        $manager->persist($user);

        $user = new User();
        $user->setName('Du Lac');
        $user->setFirstname('Lancelot');
        $user->setEmail('lancelot@dulac.ka');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
        $user->setIsActif(true);
        $user->setUsername('lancelot');
        $this->addReference(self::USER_LANCELOT, $user);

        $manager->persist($user);

        $user = new User();
        $user->setName('Root');
        $user->setFirstname('Root');
        $user->setEmail('root@root.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
        $user->setIsActif(true);
        $user->setUsername('root');
        $user->setRoles(['ROLE_ADMIN']);
        $this->addReference(self::USER_ROOT, $user);

        $manager->persist($user);

        $manager->flush();
    }
}
