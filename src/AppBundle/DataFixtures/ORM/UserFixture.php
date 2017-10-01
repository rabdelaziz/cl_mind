<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;

class UserFixture extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{

    /**
     * @var ContainerInterface
    */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * load level
     * @param  ObjectManager $manager
     * @return Niveau                
     */
    public function load(ObjectManager $manager)
    {

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setUsername('testUser');
        $user->setFirstName('user_name');
        $user->setLastName('user_last_name');
        $user->setEmail('testUser@testUser.com');
        $user->setPlainPassword('password');
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_USER'));
        $userManager->updateUser($user, true);
        $this->addReference('testUser', $user);

        $adminUser = $userManager->createUser();
        $adminUser->setUsername('adminUser');
        $adminUser->setFirstName('user_name');
        $adminUser->setLastName('user_last_name');
        $adminUser->setEmail('adminUser@adminUser.com');
        $adminUser->setPlainPassword('adminUser');
        $adminUser->setEnabled(true);
        $adminUser->setRoles(array('ROLE_ADMIN'));
        $userManager->updateUser($adminUser, true);
        $this->addReference('adminUser', $adminUser);

    }

    public function getOrder()
    {
        return 1;
    }
}