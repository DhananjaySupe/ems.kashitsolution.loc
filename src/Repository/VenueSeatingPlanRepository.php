<?php

namespace App\Repository;

use App\Entity\VenueSeatingPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VenueSeatingPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method VenueSeatingPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method VenueSeatingPlan[]    findAll()
 * @method VenueSeatingPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VenueSeatingPlanRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, VenueSeatingPlan::class);
    }

    public function getVenuesSeatingPlans($id, $venue, $organizer, $slug, $limit, $count) {
        $qb = $this->createQueryBuilder("s");
        if ($count) {
            $qb->select("COUNT(s)");
        } else {
            $qb->select("s");
        }
        if ($id !== "all") {
            $qb->andWhere("s.id = :id")->setParameter("id", $id);
        }
        if ($slug !== "all") {
            $qb->join("s.translations", "seatingPlanTranslations");
            $qb->andWhere("seatingPlanTranslations.slug = :slug")->setParameter("slug", $slug);
        }
        if ($venue !== "all" || $organizer !== "all") {
            $qb->join("s.venue", "venue");
        }
        if ($venue !== "all") {
            $qb->join("venue.translations", "venuetranslations");
            $qb->andWhere("venuetranslations.slug = :venue")->setParameter("venue", $venue);
        }
        if ($organizer !== "all") {
            $qb->join("venue.organizer", "organizer");
            $qb->andWhere("organizer.slug = :organizer")->setParameter("organizer", $organizer);
        }
        if ($limit !== "all") {
            $qb->setMaxResults($limit);
        }
        $qb->orderBy("s.id", "DESC");
        return $qb;
    }
}
