<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class FakerUsers extends Fixture
{

	private $passwordEncoder;

	public const Users_Event_reference = 'creator';
     
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
  	}
    public function load(ObjectManager $manager)
    {
    	// On configure dans quelles langues nous voulons nos données
        $faker = Faker\Factory::create('fr_FR');

        // on créé 50 personnes
        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user->setSurname($faker->firstName);
            $user->setName($faker->lastName);
            $user->setPhone($faker->phoneNumber);
            $user->setEmail($faker->email);
            $user->setRoles($user->getRoles());
            $user->setPassword($this->passwordEncoder->encodePassword(
            $user,$faker->word));
            $manager->persist($user);
            $this->addReference(self::Users_Event_reference.'_'.$i, $user);
        }
        $manager->flush();
    }
}
