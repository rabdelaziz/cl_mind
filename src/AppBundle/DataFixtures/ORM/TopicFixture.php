<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Topic;

class TopicFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * load level
     * @param  ObjectManager $manager
     * @return Niveau                
     */
    public function load(ObjectManager $manager)
    {
        //php
        $topic1 = new Topic();
        $topic1->setName('PHP');
        $topic1->setDescription("Langage de programmation Web");
        $manager->persist($topic1);
        $manager->flush();
        $this->addReference('PHP',  $topic1);
        //java
        $topic2 = new Topic();
        $topic2->setName('JAVA');
        $topic2->setDescription("Langage de programmation Java");
        $manager->persist($topic2);
        $manager->flush();
        $this->addReference('JAVA',  $topic2);
        
        // Complétons les données
        $this->addData($manager);
    }

    public function getOrder()
    {
        return 3;
    }
    
    public function addData(ObjectManager $manager)
    {
    	$topicNameList = array(
    			'Mysql',
    			'Html',
    			'JavaScript',
    			'Perl',
    			'CSS',
    	);
    	foreach ($topicNameList as $name) {
    		$topic = new Topic();
    		$topic->setName($name)
    		->setDescription("Description $name");
    		 
    		$manager->persist($topic);
    	}
    	
    	$manager->flush();
    }
}