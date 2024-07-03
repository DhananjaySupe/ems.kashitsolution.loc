<?php

namespace App\Entity;

use App\Repository\VenueSeatingPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=VenueSeatingPlanRepository::class)
 * @ORM\Table(name="eventic_seating_plan")
 * @Assert\Callback({"App\Validation\Validator", "validate"})
 * @Vich\Uploadable
 */
class VenueSeatingPlan {

    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Valid()
     */
    protected $translations;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $design;

    /**
     * @ORM\ManyToOne(targetEntity="Venue", inversedBy="seatingPlans")
     */
    private $venue;

    /**
     * @ORM\OneToMany(targetEntity="EventDate", mappedBy="seatingPlan", cascade={"persist", "remove"})
     */
    private $eventDates;

    /**
     * @var \DateTime $updatedAt
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct() {
        $this->eventDates = new ArrayCollection();
    }

    public function __call($method, $arguments) {
        return PropertyAccess::createPropertyAccessor()->getValue($this->translate(), $method);
    }

    public function getSectionsNamesArray() {
        $sectionsNamesArray = [];
        foreach ($this->design['sections'] as $section) {
            $sectionsNamesArray[$section['name']] = $section['name'];
        }
        ksort($sectionsNamesArray);
        return $sectionsNamesArray;
    }

    public function getSectionsSeatsQuantityArray() {
        $sectionsSeatsQuantityArray = [];
        foreach ($this->design['sections'] as $section) {
            $sectionsSeatsQuantityArray[$section['name']] = $this->getSectionSeatsCount($section);
        }
        ksort($sectionsSeatsQuantityArray);
        return $sectionsSeatsQuantityArray;
    }

    public function getSectionSeatsCount($section) {
        $count = 0;
        foreach ($section['rows'] as $row) {
            $count += $this->getRowSeatsCount($row);
        }
        return $count;
    }

    public function getRowSeatsCount($row) {
        return (intval($row['seatsEndNumber']) - intval($row['seatsStartNumber'])) + 1 - (count($row['disabledSeats'])) - (count($row['hiddenSeats']));
    }

    public function countTotalSeats() {
        $count = 0;
        foreach ($this->design['sections'] as $section) {
            foreach ($section['rows'] as $row) {
                $count += $this->getRowSeatsCount($row);
            }
        }
        return $count;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUpdatedAt(): ?\DateTimeInterface {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getVenue(): ?Venue {
        return $this->venue;
    }

    public function setVenue(?Venue $venue): self {
        $this->venue = $venue;

        return $this;
    }

    public function getDesign() {
        return $this->design;
    }

    public function setDesign($design): self {
        $this->design = $design;

        return $this;
    }

    /**
     * @return Collection|EventDate[]
     */
    public function getEventDates(): Collection {
        return $this->eventDates;
    }

    public function addEventDate(EventDate $eventDate): self {
        if (!$this->eventDates->contains($eventDate)) {
            $this->eventDates[] = $eventDate;
            $eventDate->setSeatingPlan($this);
        }

        return $this;
    }

    public function removeEventDate(EventDate $eventDate): self {
        if ($this->eventDates->contains($eventDate)) {
            $this->eventDates->removeElement($eventDate);
            // set the owning side to null (unless already changed)
            if ($eventDate->getSeatingPlan() === $this) {
                $eventDate->setSeatingPlan(null);
            }
        }

        return $this;
    }
}
