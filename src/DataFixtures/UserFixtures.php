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
        // Dev
        $user = new User();
        $user->setEmail('admin@demo-monsite.online');
        $user->setPassword($this->encoder->hashPassword($user, '@monsite2024!'));
        $user->setFirstname('Admin');
        $user->setLastname('monsite');
        $user->setRoles(array('ROLE_DEV'));
        $user->setIsVerified(true);
        $manager->persist($user);
        $manager->flush();


    }
}
