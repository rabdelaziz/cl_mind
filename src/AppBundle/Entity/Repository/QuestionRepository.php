<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\Mapping\OrderBy;

/**
 * QuestionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QuestionRepository extends \Doctrine\ORM\EntityRepository
{
	
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
	
}
