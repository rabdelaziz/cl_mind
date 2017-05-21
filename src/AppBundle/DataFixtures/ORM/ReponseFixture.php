<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Response;

class ReponseFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * load level
     * @param  ObjectManager $manager
     * @return Niveau                
     */
    public function load(ObjectManager $manager)
    {
        //Question 1 
        $reponse1 = new Reponse();
        $reponse1->setEnonce("in_array");
        $reponse1->setCorrect(true);
        $reponse1->setQuestion($this->getReference(0));
        $manager->persist($reponse1);

        $reponse2 = new Reponse();
        $reponse2->setEnonce("is_array");
        $reponse2->setCorrect(false);
        $reponse2->setQuestion($this->getReference(0));
        $manager->persist($reponse2);
        
        $reponse3 = new Reponse();
        $reponse3->setEnonce("array_key_exists");
        $reponse3->setCorrect(false);
        $reponse3->setQuestion($this->getReference(0));
        $manager->persist($reponse3);

        //Question 2
        $reponse4 = new Reponse();
        $reponse4->setEnonce("Sort");
        $reponse4->setCorrect(false);
        $reponse4->setQuestion($this->getReference(1));
        $manager->persist($reponse4);

        $reponse5 = new Reponse();
        $reponse5->setEnonce("Ksort");
        $reponse5->setCorrect(false);
        $reponse5->setQuestion($this->getReference(1));
        $manager->persist($reponse5);
        
        $reponse6 = new Reponse();
        $reponse6->setEnonce("Rsort");
        $reponse6->setCorrect(false);
        $reponse6->setQuestion($this->getReference(1));
        $manager->persist($reponse6);

        $reponse7 = new Reponse();
        $reponse7->setEnonce("Krsort");
        $reponse7->setCorrect(true);
        $reponse7->setQuestion($this->getReference(1));
        $manager->persist($reponse7);

        //Question 3
        $reponse8 = new Reponse();
        $reponse8->setEnonce("******123");
        $reponse8->setCorrect(false);
        $reponse8->setQuestion($this->getReference(2));
        $manager->persist($reponse8);

        $reponse9 = new Reponse();
        $reponse9->setEnonce("*****_1234");
        $reponse9->setCorrect(false);
        $reponse9->setQuestion($this->getReference(2));
        $manager->persist($reponse9);

        $reponse10 = new Reponse();
        $reponse10->setEnonce("******1234");
        $reponse10->setCorrect(true);
        $reponse10->setQuestion($this->getReference(2));
        $manager->persist($reponse10);

        $reponse11 = new Reponse();
        $reponse11->setEnonce("_*123*");
        $reponse11->setCorrect(false);
        $reponse11->setQuestion($this->getReference(2));
        $manager->persist($reponse11);

        $reponse12 = new Reponse();
        $reponse12->setEnonce(".**1****1236");
        $reponse12->setCorrect(true);
        $reponse12->setQuestion($this->getReference(2));
        $manager->persist($reponse12);

        //Question 4
        $reponse12 = new Reponse();
        $reponse12->setEnonce(".**1****1236");
        $reponse12->setCorrect(true);
        $reponse12->setQuestion($this->getReference(2));
        $manager->persist($reponse12);

        $reponse12 = new Reponse();
        $reponse12->setEnonce(".**1****1236");
        $reponse12->setCorrect(true);
        $reponse12->setQuestion($this->getReference(2));
        $manager->persist($reponse12);

        $reponse12 = new Reponse();
        $reponse12->setEnonce(".**1****1236");
        $reponse12->setCorrect(true);
        $reponse12->setQuestion($this->getReference(2));
        $manager->persist($reponse12);

        $reponse12 = new Reponse();
        $reponse12->setEnonce(".**1****1236");
        $reponse12->setCorrect(true);
        $reponse12->setQuestion($this->getReference(2));
        $manager->persist($reponse12);

        //Question 4
        $reponse13 = new Reponse();
        $reponse13->setEnonce("get, cookie, post");
        $reponse13->setCorrect(false);
        $reponse13->setQuestion($this->getReference(3));
        $manager->persist($reponse13); 

        $reponse14 = new Reponse();
        $reponse14->setEnonce("post, get, cookie");
        $reponse14->setCorrect(false);
        $reponse14->setQuestion($this->getReference(3));
        $manager->persist($reponse14);

        $reponse15 = new Reponse();
        $reponse15->setEnonce("get, post, cookie");
        $reponse15->setCorrect(true);
        $reponse15->setQuestion($this->getReference(3));
        $manager->persist($reponse15);

        $reponse16 = new Reponse();
        $reponse16->setEnonce("post,cookie, get");
        $reponse16->setCorrect(false);
        $reponse16->setQuestion($this->getReference(3));
        $manager->persist($reponse16);

        //Question 5
        $reponse17 = new Reponse();
        $reponse17->setEnonce("ob_flush()");
        $reponse17->setCorrect(false);
        $reponse17->setQuestion($this->getReference(4));
        $manager->persist($reponse17);

        $reponse18 = new Reponse();
        $reponse18->setEnonce("ob_get_flush()");
        $reponse18->setCorrect(false);
        $reponse18->setQuestion($this->getReference(4));
        $manager->persist($reponse18);
        
        $reponse19 = new Reponse();
        $reponse19->setEnonce("ob_end_flush()");
        $reponse19->setCorrect(true);
        $reponse19->setQuestion($this->getReference(4));
        $manager->persist($reponse19);

        $manager->flush();

    }
    
    //Ordre de chargement
    public function getOrder()
    {
        return 5;
    }
}