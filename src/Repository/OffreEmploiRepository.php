<?php

namespace App\Repository;

use App\Entity\OffreEmploi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class OffreEmploiRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OffreEmploi::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('o')
            ->where('o.something = :value')->setParameter('value', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
	
	// utilisé par bloc DOCUMENT	
	public function findTopLocalisation($langue)
	{
		$qb = $this->createQueryBuilder('offre');
		$qb	//->select($qb->expr()->count('offre.localisation'))
			->select('offre.localisation, count(offre.localisation) AS nb_loc')
			->andWhere($qb->expr()->eq('offre.langue', ':langue'))
			->setParameter('langue', $langue)
			->groupBy('offre.localisation')
			//->setMaxResults(1)
			->orderBy('nb_loc', 'DESC')
			;

		return $qb
			->getQuery()
			->getResult();
	}
}
