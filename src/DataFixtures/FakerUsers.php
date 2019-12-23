<?php
// src/DataFixtures/FakerUsers.php
namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class FakerUsers extends Fixture
{
    public const Users_Event_reference = 'creator';

    public function load(ObjectManager $manager)
    {
        // On configure dans quelles langues nous voulons nos données
        $faker = Faker\Factory::create('fr_FR');

        // on créé 10 personnes
        for ($i = 0; $i < 50; $i++) {
            $user = new Users();
            $user->setSurname($faker->firstName);
            $user->setName($faker->lastName);
            $user->setPhone($faker->phoneNumber);
            $user->setMail($faker->email);
            $user->setPassword(sha1(random_bytes(20)));
            $manager->persist($user);
            $this->addReference(self::Users_Event_reference.'_'.$i, $user);
        }

        $manager->flush();
    }
}