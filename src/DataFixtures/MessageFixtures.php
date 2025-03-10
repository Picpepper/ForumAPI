<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Message;
use App\Entity\Utilisateur;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 25; $i++) {
            $message = new Message();
            $message->setTitre($this->faker->realText(15)); // Génère un vrai texte court
            $message->setDatePoste($this->faker->dateTimeThisYear());
            $message->setContenu($this->faker->realText(200)); // Génère un vrai paragraphe
            $message->setUtilisateur($this->getReference('utilisateur' . mt_rand(0, 9), Utilisateur::class));
            $manager->persist($message);
            $this->addReference('message' . $i, $message);
        }

        for ($i = 0; $i < 40; $i++) {
            $message = new Message();
            $message->setTitre(NULL);
            $message->setDatePoste($this->faker->dateTimeThisYear());
            $message->setContenu($this->faker->realText(200)); // Génère un vrai paragraphe
            $message->setUtilisateur($this->getReference('utilisateur' . mt_rand(0, 9), Utilisateur::class));
            $message->setParent($this->getReference('message' . mt_rand(0, 9), Message::class));
            $manager->persist($message);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UtilisateurFixtures::class,
        ];
    }
}
