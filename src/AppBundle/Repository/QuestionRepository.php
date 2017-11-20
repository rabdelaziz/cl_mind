<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Mapping\OrderBy;
use AppBundle\Entity\Question;

/**
 * QuestionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QuestionRepository extends \Doctrine\ORM\EntityRepository
{
	
	public function findAllOrderByTopic()
	{
		return $this->createQueryBuilder('q')
			->leftJoin('q.topic', 't')
			->orderBy('t.name', 'DESC')
			->getQuery()
			->getResult();

	}
	public function getQuestionsByTopicIdAndLevel($topicId, $questionLevel, $limit = null, $offset = null)
	{
		$qb = $this->createQueryBuilder('q')
			->leftJoin('q.topic', 't')
			->leftJoin('q.level', 'l')
			->where('topic_id = :topicId')
			->setParameter('topicId', $topicId)
			->andWhere('l.name = :name')
			->setParameter('name', $questionLevel);
		
		if (isset($limit)) {
			$qb->setMaxResults($limit);
		}
		if (isset($offset)) {
			$qb->setFirstResult($offset);
		}
		
		return $qb->getQuery()->getResult();
	}
	
	public function getQuestionsWithTopic($topicName, $questionLevel, $limit = null, $offset = null, $orderBy = 'DESC')
	{
		$qb = $this->createQueryBuilder('q')
			->leftJoin('q.topic', 't')
			->leftJoin('q.level', 'l')
			->where('t.name = :topicName')
			->setParameter('topicName', $topicName)
			->andWhere('l.name = :levelName')
			->setParameter('levelName', $questionLevel)
			->orderBy('q.id', $orderBy);
		
		if (isset($limit)) {
			$qb->setMaxResults($limit);
		}
		if (isset($offset)) {
			$qb->setFirstResult($offset);
		}
	
		return $qb->getQuery()->getResult();
	}
	
	
	/**
	 * Permet de récupérer une question avec toutes les entités liées
	 * 
	 * @param int $id
	 * @return Question|NULL
	 */
	public function getQuestionById($id)
	{
	    $qb = $this->createQueryBuilder('q')
	       ->leftJoin('q.topic', 't')
	       ->leftJoin('q.level', 'l')
	       ->leftJoin('q.responses', 'r')
	       ->addSelect('t')
	       ->addSelect('l')
	       ->addSelect('r')
	       ->where('q.id = :id')
	       ->setParameter(':id', $id, \PDO::PARAM_INT);
	    
       return $qb->getQuery()->getSingleResult();
	}
	
	/**
	 * Permet de récupérer la liste de toutes les quetions
	 * 
	 * @return array
	 */
	public function getQuestionsOrderByTopic($order = 'ASC')
	{
	    $order = in_array($order, ['ASC', 'DESC']) ? $order : 'ASC';
	    
	    $qb = $this->createQueryBuilder('q')
	       ->leftJoin('q.topic', 't')
	       ->leftJoin('q.level', 'l')
	       ->addSelect('t')
	       ->addSelect('l')
	       ->orderBy('t.name', $order);
	     
	    return $qb->getQuery()->getResult();
	
	}
	
}
