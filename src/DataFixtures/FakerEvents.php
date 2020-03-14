<?php
// src/DataFixtures/FakerEvents.php
namespace App\DataFixtures;

use App\Entity\Events;
use App\DataFixtures\FakerUsers;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class FakerEvents extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        // On configure dans quelles langues nous voulons nos données
        $faker = Faker\Factory::create('fr_FR');

        // on créé 100 events
        for ($i = 0; $i < 100; $i++) {
            $event = new Events();
            $event->setCreator($this->getReference(FakerUsers::Users_Event_reference.'_'.rand(0,49)));
            $event->setName($faker->word);
            $event->setDate($faker->dateTimeThisYear($max = 'now', $timezone = null));
            $event->setCity($faker->city);
            $event->setNbParticipant($faker->numberBetween($min = 1, $max = 50));
            $event->setUrlImage($faker->imageUrl($width = 640, $height = 480));
            $event->setDescription($faker->text($maxNbChars = 200));
            $event->setUpdatedAt(new \DateTime());
            $manager->persist($event);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            FakerUsers::class,
        );
    }

}