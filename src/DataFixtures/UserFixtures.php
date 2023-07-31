<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{


    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $encoder;


    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('bienvenue@e-systemes.com');
        $user->setPassword($this->encoder->hashPassword($user, 'demo'));
        $user->setFirstname('esys');
        $user->setLastname('DEV');
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setIsVerified(true);
        $manager->persist($user);
        $manager->flush();
    }
}
