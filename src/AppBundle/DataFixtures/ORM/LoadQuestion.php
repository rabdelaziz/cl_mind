<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Niveau;
use AppBundle\Entity\Topic;
use AppBundle\Entity\Question;
use AppBundle\Entity\Reponse;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadQuestion implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $question = new Question();

        $question->setEnonce("C'est quoi la fonction PHP pour vérifier si un élement appartient à un tableau?");
        $question->setDuree(3);

        // Définir le niveau de difficulté de la question
        $niveau = new Niveau();
        $niveau->setNom('facile');

        $question->setNiveau($niveau);

        // Définir le Thème
        $topic = new Topic();
        $topic->setName('PHP');
        $topic->setDescription("Langage de programmation Web");
        $question->setTopic($topic);

        // Ajouter les réponses
        $reponse1 = new Reponse();
        $reponse1->setEnonce("in_array");
        $reponse1->setCorrect(true);

        $reponse2 = new Reponse();
        $reponse2->setEnonce("is_array");
        $reponse2->setCorrect(false);

        $question->addReponse($reponse1);
        $question->addReponse($reponse2);

        // On la persiste
        $manager->persist($topic);
        $manager->persist($niveau);
        $manager->persist($question);

        $manager->flush();
    }
}