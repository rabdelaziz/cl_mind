<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Response;

class ResponseFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * load level
     * @param  ObjectManager $manager
     * @return Niveau                
     */
    public function load(ObjectManager $manager)
    {
        //Question 1 
        $reponse1 = new Response();
        $reponse1->setWording("in_array");
        $reponse1->setCorrect(true);
        $reponse1->setQuestion($this->getReference(0));
        $manager->persist($reponse1);

        $reponse2 = new Response();
        $reponse2->setWording("is_array");
        $reponse2->setCorrect(false);
        $reponse2->setQuestion($this->getReference(0));
        $manager->persist($reponse2);
        
        $reponse3 = new Response();
        $reponse3->setWording("array_key_exists");
        $reponse3->setCorrect(false);
        $reponse3->setQuestion($this->getReference(0));
        $manager->persist($reponse3);

        //Question 2
        $reponse4 = new Response();
        $reponse4->setWording("Sort");
        $reponse4->setCorrect(false);
        $reponse4->setQuestion($this->getReference(1));
        $manager->persist($reponse4);

        $response5 = new Response();
        $response5->setWording("Ksort");
        $response5->setCorrect(false);
        $response5->setQuestion($this->getReference(1));
        $manager->persist($response5);
        
        $response6 = new Response();
        $response6->setWording("Rsort");
        $response6->setCorrect(false);
        $response6->setQuestion($this->getReference(1));
        $manager->persist($response6);

        $response7 = new Response();
        $response7->setWording("Krsort");
        $response7->setCorrect(true);
        $response7->setQuestion($this->getReference(1));
        $manager->persist($response7);

        //Question 3
        $response8 = new Response();
        $response8->setWording("******123");
        $response8->setCorrect(false);
        $response8->setQuestion($this->getReference(2));
        $manager->persist($response8);

        $response9 = new Response();
        $response9->setWording("*****_1234");
        $response9->setCorrect(false);
        $response9->setQuestion($this->getReference(2));
        $manager->persist($response9);

        $response10 = new Response();
        $response10->setWording("******1234");
        $response10->setCorrect(true);
        $response10->setQuestion($this->getReference(2));
        $manager->persist($response10);

        $response11 = new Response();
        $response11->setWording("_*123*");
        $response11->setCorrect(false);
        $response11->setQuestion($this->getReference(2));
        $manager->persist($response11);

        $response12 = new Response();
        $response12->setWording(".**1****1236");
        $response12->setCorrect(true);
        $response12->setQuestion($this->getReference(2));
        $manager->persist($response12);

        //Question 4
        $response12 = new Response();
        $response12->setWording(".**1****1236");
        $response12->setCorrect(true);
        $response12->setQuestion($this->getReference(2));
        $manager->persist($response12);

        $response12 = new Response();
        $response12->setWording(".**1****1236");
        $response12->setCorrect(true);
        $response12->setQuestion($this->getReference(2));
        $manager->persist($response12);

        $response12 = new Response();
        $response12->setWording(".**1****1236");
        $response12->setCorrect(true);
        $response12->setQuestion($this->getReference(2));
        $manager->persist($response12);

        $response12 = new Response();
        $response12->setWording(".**1****1236");
        $response12->setCorrect(true);
        $response12->setQuestion($this->getReference(2));
        $manager->persist($response12);

        //Question 4
        $response13 = new Response();
        $response13->setWording("get, cookie, post");
        $response13->setCorrect(false);
        $response13->setQuestion($this->getReference(3));
        $manager->persist($response13); 

        $response14 = new Response();
        $response14->setWording("post, get, cookie");
        $response14->setCorrect(false);
        $response14->setQuestion($this->getReference(3));
        $manager->persist($response14);

        $response15 = new Response();
        $response15->setWording("get, post, cookie");
        $response15->setCorrect(true);
        $response15->setQuestion($this->getReference(3));
        $manager->persist($response15);

        $response16 = new Response();
        $response16->setWording("post,cookie, get");
        $response16->setCorrect(false);
        $response16->setQuestion($this->getReference(3));
        $manager->persist($response16);

        //Question 5
        $response17 = new Response();
        $response17->setWording("ob_flush()");
        $response17->setCorrect(false);
        $response17->setQuestion($this->getReference(4));
        $manager->persist($response17);

        $response18 = new Response();
        $response18->setWording("ob_get_flush()");
        $response18->setCorrect(false);
        $response18->setQuestion($this->getReference(4));
        $manager->persist($response18);
        
        $response19 = new Response();
        $response19->setWording("ob_end_flush()");
        $response19->setCorrect(true);
        $response19->setQuestion($this->getReference(4));
        $manager->persist($response19);

        $manager->flush();
        
        // On va générer les réponses pour chacune des 30 questions PHP puis JAVA
        $topicsLabelList = ['PHP', 'JAVA'];
        $reponseLabelList = ['A', 'B', 'C', 'D'];
        foreach ($topicsLabelList as $label) {
        	// Pour chaque question
        	for ($i = 1; $i <= 30 ; $i++) {
        		foreach ($reponseLabelList as $repLabel) {
        			$reponse = new Response();
        			$reponse->setWording("Question $label $i : Reponse $repLabel");
        			if ($repLabel === 'A') {
        				$reponse->setCorrect(true);
        			} else {
        				$reponse->setCorrect(false);
        			}
        			$key = $label . '_' . $i;
        			$reponse->setQuestion($this->getReference($key));
        					
        			$manager->persist($reponse);
        		}
        		
        	}
        }
        
        $manager->flush();

    }
    
    //Ordre de chargement
    public function getOrder()
    {
        return 5;
    }
}