<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Question;

class QuestionFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $questions = [
            ['enonce' => 'C\'est quoi la fonction PHP pour vérifier si un élement appartient à un tableau?',
             'duree' => '2',
            ],
            ['enonce' => 'Quelles chaînes seront matchées par l\'expression régulière ci-dessous',
             'duree' => '2',
            ],
            ['enonce' => 'Laquelle de ces fonctions permet de classer un tableau par les clefs en ordre décroissant ?',
             'duree' => '2',
            ],
            ['enonce' => 'Par défaut, dans quel ordre de priorité sont affectées les variables envoyées par HTTP ?',
             'duree' => '2',
            ],
            ['enonce' => 'Quelle fonction de bufferisation (temporisation) envoie au navigateur les données contenues par le tampon, et stoppe la tamporisation de sortie ?',
             'duree' => '2',
            ],
        ];


        foreach ($questions as $key => $questionAttr) {
            $question = new Question();
            $question->setEnonce($questionAttr['enonce']);
            $question->setDuree($questionAttr['duree']);
            if($key % 2 == 0) {
                $question->setNiveau($this->getReference('facile'));
            } else {
                $question->setNiveau($this->getReference('moyen'));
            }
            $question->setTopic($this->getReference('PHP'));
            $manager->persist($question);
            $manager->flush();
            $this->addReference($key, $question);

        }
        
    }


    public function getOrder()
    {
        return 4;
    }
}