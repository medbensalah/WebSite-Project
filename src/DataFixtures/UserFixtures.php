<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(\Doctrine\Persistence\ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 50; $i++) {
            $gender = rand(0, 1) ? 'F' : 'M';
            $user = new User();
            $user->setName($faker->name);
            $user->setFirstName($faker->firstName);
            $user->setPhone($faker->numberBetween(10000000, 99999999));
            $user->setEmail($faker->email);
            $user->setPassword($faker->password(6, 20));
            $user->setBirth($faker->dateTimeBetween('-90 years', '-18 years', null));
            $user->setGender($gender);
            $user->setLocation($faker->city);
            $user->setPhoto($faker->imageUrl());

            $manager->persist($user);
        }
        $manager->flush();
    }
}
