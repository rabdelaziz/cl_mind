<?php

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use FOS\UserBundle\Util\TokenGeneratorInterface;

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
    
    /** @var RoleHierarchyInterface RoleHierarchy */
	private $tokenGenerator;

    /**
     * 
     * @param EntityManager $entityManager
     * @param TokenGeneratorInterface $tokenGenerator
     */
	public function __construct(EntityManager $entityManager, TokenGeneratorInterface $tokenGenerator)
	{
		$this->em = $entityManager;
        $this->tokenGenerator = $tokenGenerator;
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
	
//	/**
//	 * 
//	 * @param User $user
//	 * @return \AppBundle\Entity\User
//	 */
//	public function generatePassword(User $user)
//	{
//		$user->setPlainPassword($user->getFirstName());
//		
//		return $user;
//	}
    
    /**
     * Permet de générer un mot de passe aléatoire
     *
     * @param int $caractersCount Le nb de caractères du mot de passe
     * @return string
     */
    public function generatePassword($caractersCount = 8)
    {
        return substr($this->tokenGenerator->generateToken(), 0, $caractersCount);
    }

}