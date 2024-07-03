<?php

namespace App\Repository;

use App\Entity\VenueSeatingPlanTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VenueSeatingPlanTranslationRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, VenueSeatingPlanTranslation::class);
    }
}
