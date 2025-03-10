<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class UtilisateurFixtures extends Fixture
{
    private $faker;
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->faker = Factory::create("fr_FR");
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $slugger = new AsciiSlugger();

        for ($i = 0; $i < 15; $i++) {
            $utilisateur = new Utilisateur();
            $utilisateur->setPseudonyme($this->faker->lastName())
                ->setEmail(strtolower($utilisateur->getPseudonyme()) . '@' . $this->faker->freeEmailDomain())
                ->setPassword($this->passwordHasher->hashPassword($utilisateur, $slugger->slug(strtolower($utilisateur->getPseudonyme()))))
                ->setDateInscription($this->faker->dateTimeThisYear());
            $this->addReference('utilisateur' . $i, $utilisateur);
            $manager->persist($utilisateur);
        }
        $manager->flush();
    }
}
