<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Create 4 random User
        for ($i = 0; $i < 4; ++$i) {
            $user = new User();
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $email = $firstName.'.'.$lastName.'@'.$faker->freeEmailDomain();

            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setEmail($email);
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT)); // Assure-toi de gérer le hachage du mot de passe
            $user->setApiActivated(false);
            $manager->persist($user);
        }

        // Exécuter l'enregistrement en base de données
        $manager->flush();
    }
}
