<?php

namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoadUserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordEncoder;
    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder=$passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $USER = new User();
        $USER->setUsername("random")
            ->setPassword($this->passwordEncoder->hashPassword($USER, "1234"));
        $manager->persist($USER);
        $USER2 = new User();
        $USER2->setUsername("admin")
            ->setPassword($this->passwordEncoder->hashPassword($USER, "1234"))
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($USER2);
        $manager->flush();
    }

}