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
        $messages = []; // Stocker les messages créés

        // Étape 1 : Créer les messages sans parent
        for ($i = 0; $i < 40; $i++) {
            $message = new Message();
            $message->setTitre($this->faker->sentence(mt_rand(3, 6)), true);  // Génère un vrai texte court
            $message->setDatePoste($this->faker->dateTimeThisYear());
            $message->setContenu($this->faker->realText(200)); // Génère un vrai paragraphe
            $message->setUtilisateur($this->getReference('utilisateur' . mt_rand(0, 9), Utilisateur::class));

            $manager->persist($message);
            $this->addReference('message' . $i, $message);
            $messages[] = $message; // Ajouter dans le tableau
        }

        $manager->flush(); // Enregistrer en base pour que les ID existent

        // Étape 2 : Assigner les parents aux messages
        foreach ($messages as $message) {
            if (count($messages) > 1) {
                $randomParent = $messages[array_rand($messages)];
                if ($randomParent !== $message) { // Éviter qu'un message soit son propre parent
                    $message->setParent($randomParent);
                }
            }
        }

        $manager->flush(); // Enregistrer les changements
    }

    public function getDependencies(): array
    {
        return [
            UtilisateurFixtures::class,
        ];
    }
}
