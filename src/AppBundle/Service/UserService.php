<<<<<<< HEAD
<?php

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;

/*
 *
 */
class UserService
{
	/**
	 * 
	 * @var EntityManager
	 */
	protected $em;

	public function __construct(EntityManager $entityManager)
	{
		$this->em = $entityManager;
	}
	
	/**
	 * 
	 * @param \AppBundle\Entity\User $candidate
	 * @return boolean|null|\AppBundle\Entity\User
	 */
	public function findUser(\AppBundle\Entity\User $candidate)
	{
		$user = $this->em->getRepository('AppBundle:User')->findUser($candidate);	
		if (null !== $user) {
			if ($candidate->getEmail() != $user->getEmail()
					|| strcasecmp($candidate->getFirstName(), $user->getFirstName()) != 0
					|| strcasecmp($candidate->getLastName(), $user->getLastName()) != 0
			) {						
				return false;				
			}
		}
		
		return $user;
	}
	
	/**
	 * Permet de générer les informations manquantes d'un utilisateur
	 * 
	 * @param User $user
	 * @return \AppBundle\Entity\User
	 */
	public function generateUserData(User $user)
	{
		$user->setUsername($user->getEmail());
		$user->setPlainPassword($user->getFirstName());
		//$user->setFirstName($user->getFirstName());
		$user->setEnabled(true);
		$user->setRoles(array('ROLE_USER'));
		
		return $user;
	}
	
	/**
	 * 
	 * @param User $user
	 * @return \AppBundle\Entity\User
	 */
	public function generatePassword(User $user)
	{
		$user->setPlainPassword($user->getFirstName());
		
		return $user;
	}

=======
<?php

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;

/*
 *
 */
class UserService
{
	/**
	 * 
	 * @var EntityManager
	 */
	protected $em;

	public function __construct(EntityManager $entityManager)
	{
		$this->em = $entityManager;
	}
	
	/**
	 * 
	 * @param \AppBundle\Entity\User $candidate
	 * @return boolean|null|\AppBundle\Entity\User
	 */
	public function findUser(\AppBundle\Entity\User $candidate)
	{
		$user = $this->em->getRepository('AppBundle:User')->findUser($candidate);	
		if (null !== $user) {
			if ($candidate->getEmail() != $user->getEmail()
					|| strcasecmp($candidate->getFirstName(), $user->getFirstName()) != 0
					|| strcasecmp($candidate->getLastName(), $user->getLastName()) != 0
			) {						
				return false;				
			}
		}
		
		return $user;
	}
	
	/**
	 * Permet de générer les informations manquantes d'un utilisateur
	 * 
	 * @param User $user
	 * @param array $roles
	 * @return \AppBundle\Entity\User
	 */
	public function generateUserData(User $user, array $roles)
	{
		$user->setUsername($user->getEmail());
		$user->setPlainPassword($user->getFirstName());

		$user->setEnabled(true);
		$user->setRoles($roles);
		
		return $user;
	}
	
	/**
	 * 
	 * @param User $user
	 * @return \AppBundle\Entity\User
	 */
	public function generatePassword(User $user)
	{
		$user->setPlainPassword($user->getFirstName());
		
		return $user;
	}

>>>>>>> develop
}