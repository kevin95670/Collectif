<?php
// src/DataFixtures/FakerCategories.php
namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class FakerCategories extends Fixture
{

    public function load(ObjectManager $manager)
    {

        $categories=['Sport','Gastronomie','Jeux vidéo','Cinéma','Enseignement','Théâtre','Autre'];

        // on créé 10 personnes
        for ($i = 0; $i < count($categories); $i++) {
            $categorie = new Categories();
            $categorie->setName($categories[$i]);
            $manager->persist($categorie);
        }

        $manager->flush();
    }
}