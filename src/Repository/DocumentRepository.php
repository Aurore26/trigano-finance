<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\Langue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Document::class);
    }
	
	public function findOneLastDoc($array_codes, $langue)
	{
		$qb = $this->createQueryBuilder('doc');
		$qb	
			->join('doc.document_type', 't')
			->addSelect('t')
			->andWhere($qb->expr()->eq('doc.isPublished', ':published'))
			->andWhere($qb->expr()->eq('doc.langue', ':langue'))
			->andWhere($qb->expr()->in('t.code', ':codes'))
			->setParameter('published', true)
			->setParameter('langue', $langue)
			->setParameter('codes', $array_codes)
			->orderBy('doc.date', 'DESC')
			->setMaxResults(1);

		return $qb
			->getQuery()
			//->getOneOrNullResult();
			->getSingleResult();// déclenche une exceptionDoctrine\ORM\NoResultExceptionsi aucun résultat.
	}
	
	public function findLastDoc($array_codes, $langue, $limit = 3, $exercice = null)
	{
		$qb = $this->createQueryBuilder('doc');
		$qb	
			->join('doc.document_type', 't')
			->addSelect('t')
			->andWhere($qb->expr()->eq('doc.isPublished', ':published'))
			->andWhere($qb->expr()->eq('doc.langue', ':langue'))
			->andWhere($qb->expr()->in('t.code', ':codes'));
		if(isset($exercice)){
			$qb->andWhere($qb->expr()->eq('doc.exercice', ':exercice'));
		}		
			$qb->setParameter('published', true)
			->setParameter('langue', $langue)
			->setParameter('codes', $array_codes);
		
		if(isset($exercice)){
			$qb->setParameter('exercice', $exercice);
		}
			$qb->orderBy('doc.date', 'DESC')
			->setMaxResults($limit);

		return $qb
			->getQuery()
			->getResult();
	}
	
	// utilisé pour afficher le tableau	recap de tous les doc
	public function findAllDocByType($type_slug, $langue)
	{
		$qb = $this->createQueryBuilder('doc');
		$qb	
			->join('doc.document_type', 't')
			->addSelect('t')
			->andWhere($qb->expr()->eq('doc.isPublished', ':published'))
			->andWhere($qb->expr()->eq('doc.langue', ':langue'))
			->andWhere($qb->expr()->eq('t.slug', ':slug_type'))
			->setParameter('published', true)
			->setParameter('langue', $langue)
			->setParameter('slug_type', $type_slug)
			->orderBy('doc.date', 'DESC');

		return $qb
			->getQuery()
			->getResult();
	}
	
	// utilisé pour afficher le tableau	recap de tous les doc	
	public function findAllDocByExercice($exercice, $langue)
	{
		$qb = $this->createQueryBuilder('doc');
		$qb	
			->andWhere($qb->expr()->eq('doc.isPublished', ':published'))
			->andWhere($qb->expr()->eq('doc.langue', ':langue'))
			->andWhere($qb->expr()->eq('doc.exercice', ':exercice'))
			->setParameter('published', true)
			->setParameter('langue', $langue)
			->setParameter('exercice', $exercice)
			->addOrderBy('doc.date', 'DESC')
			->addOrderBy('doc.titre', 'DESC'); 

		return $qb
			->getQuery()
			->getResult();
	}
	
	
	// utilisé par bloc DOCUMENT	
	public function findDistinctYear($langue)
	{
		$qb = $this->createQueryBuilder('doc');
		$qb	->select('doc.exercice')
			->andWhere($qb->expr()->eq('doc.isPublished', ':published'))
			->andWhere($qb->expr()->eq('doc.langue', ':langue'))
			->setParameter('published', true)
			->setParameter('langue', $langue)
			->distinct('doc.exercice')
			->orderBy('doc.exercice', 'DESC');

		return $qb
			->getQuery()
			->getResult();
	}
	
	public function getPreviousDoc($document)
	{
		$qb = $this->createQueryBuilder('doc');
		$qb	->join('doc.langue', 'l')
			->addSelect('doc')
			->andWhere($qb->expr()->eq('doc.isPublished', ':published'))
			->andWhere($qb->expr()->eq('doc.inCalendar', ':inCalendar'))
			->andWhere($qb->expr()->eq('l.id', ':langue'))
			->andWhere('doc.date < :date OR (doc.date = :date AND doc.titre < :titre_prec) ') 
			->andWhere('doc.id <> :id_doc')
			->setParameter('published', true)
			->setParameter('inCalendar', true)
			->setParameter('langue', $document->getLangue()->getId())
        	->setParameter('date', $document->getDate())
			->setParameter('id_doc', $document->getId())
			->setParameter('titre_prec', $document->getTitre())
			->addOrderBy('doc.date', 'DESC')
			->addOrderBy('doc.titre', 'DESC')
			->setMaxResults(1);

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}
	
	public function getNextDoc($document)
	{
		$qb = $this->createQueryBuilder('doc');
		$qb	->join('doc.langue', 'l')
			->addSelect('doc')
			->andWhere($qb->expr()->eq('doc.isPublished', ':published'))
			->andWhere($qb->expr()->eq('doc.inCalendar', ':inCalendar'))
			->andWhere($qb->expr()->eq('l.id', ':langue'))
			->andWhere('doc.date > :date OR (doc.date = :date AND doc.titre > :titre_prec) ') 
			->andWhere('doc.id <> :id_doc')
			->setParameter('published', true)
			->setParameter('inCalendar', true)
			->setParameter('langue', $document->getLangue()->getId())
        	->setParameter('date', $document->getDate())
			->setParameter('id_doc', $document->getId())
			->setParameter('titre_prec', $document->getTitre())
			->addOrderBy('doc.date', 'ASC')
			->addOrderBy('doc.titre', 'ASC')
			->setMaxResults(1);

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}
}

