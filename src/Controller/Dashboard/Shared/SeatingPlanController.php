<?php

namespace App\Controller\Dashboard\Shared;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\AppServices;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\VenueSeatingPlan;
use App\Form\VenueSeatingPlanType;

class SeatingPlanController extends Controller {

    /**
     * @Route("/organizer/my-venues/{venueSlug}/seating-plans", name="dashboard_organizer_venue_seating_plans", methods="GET")
     */
    public function index($venueSlug, Request $request, AppServices $services, TranslatorInterface $translator) {

        $venue = $services->getVenues(array('organizer' => $this->getUser()->getOrganizer()->getSlug(), 'hidden' => 'all', 'slug' => $venueSlug))->getQuery()->getOneOrNullResult();
        if (!$venue) {
            $this->addFlash('error', $translator->trans('The venue can not be found'));
            return $services->redirectToReferer('venue');
        }

        $seatingPlans = $services->getVenuesSeatingPlans(array('venue' => $venue->getSlug()))->getQuery()->getResult();

        return $this->render('Dashboard/Shared/Venue/SeatingPlans/index.html.twig', [
                    'venue' => $venue,
                    'seatingPlans' => $seatingPlans,
        ]);
    }

    /**
     * @Route("/organizer/my-venues/{venueSlug}/seating-plans/add", name="dashboard_organizer_venue_seating_plans_add", methods="GET|POST")
     * @Route("/organizer/my-venues/{venueSlug}/seating-plans/{seatingPlanSlug}/edit", name="dashboard_organizer_venue_seating_plans_edit", methods="GET|POST")
     */
    public function addedit(Request $request, AppServices $services, TranslatorInterface $translator, $venueSlug, $seatingPlanSlug = null) {
        $em = $this->getDoctrine()->getManager();

        $venue = $services->getVenues(array('organizer' => $this->getUser()->getOrganizer()->getSlug(), 'hidden' => 'all', 'slug' => $venueSlug))->getQuery()->getOneOrNullResult();
        if (!$venue) {
            $this->addFlash('error', $translator->trans('The venue can not be found'));
            return $services->redirectToReferer('venue');
        }

        if (!$seatingPlanSlug) {
            $seatingPlan = new VenueSeatingPlan();
            $seatingPlan->setVenue($venue);
        } else {
            $seatingPlan = $services->getVenuesSeatingPlans(array('slug' => $seatingPlanSlug))->getQuery()->getOneOrNullResult();
            if (!$seatingPlan) {
                $this->addFlash('error', $translator->trans('The seating plan can not be found'));
                return $services->redirectToReferer('venue');
            }
            if (count($seatingPlan->getEventDates()) > 0) {
                $this->addFlash('error', $translator->trans('The seating plan can not be edited after it is assigned to one or more event dates'));
                return $services->redirectToReferer('venue');
            }
        }

        $form = $this->createForm(VenueSeatingPlanType::class, $seatingPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            foreach ($venue->getSeatingPlans() as $existentVenueSeatingPlan) {
                if ($existentVenueSeatingPlan != $seatingPlan && $seatingPlan->getName() == $existentVenueSeatingPlan->getName()) {
                    $form->get('translations')->addError(new \Symfony\Component\Form\FormError($translator->trans('The seating plan name has to be unique per venue')));
                }
            }

            if ($form->isValid()) {

                $seatingPlan->setDesign(json_decode($request->request->get('venue_seating_plan')['design']));
                $seatingPlan->setUpdatedAt(new \DateTime());
                $em->persist($seatingPlan);
                $em->flush();
                if (!$seatingPlanSlug) {
                    $this->addFlash('success', $translator->trans('The seating plan has been successfully created'));
                } else {
                    $this->addFlash('success', $translator->trans('The seating plan has been successfully updated'));
                }
                return $this->redirectToRoute("dashboard_organizer_venue_seating_plans", ["venueSlug" => $venueSlug]);
            } else {
                $this->addFlash('error', $translator->trans('The form contains invalid data'));
            }
        }
        return $this->render('Dashboard/Shared/Venue/SeatingPlans/add-edit.html.twig', [
                    "seatingPlan" => $seatingPlan,
                    "venue" => $venue,
                    "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/organizer/my-venues/{venueSlug}/seating-plans/{seatingPlanSlug}/duplicate", name="dashboard_organizer_venue_seating_plans_duplicate", methods="GET")
     */
    public function duplicate(Request $request, AppServices $services, TranslatorInterface $translator, $venueSlug, $seatingPlanSlug = null) {
        $em = $this->getDoctrine()->getManager();

        $venue = $services->getVenues(array('organizer' => $this->getUser()->getOrganizer()->getSlug(), 'hidden' => 'all', 'slug' => $venueSlug))->getQuery()->getOneOrNullResult();
        if (!$venue) {
            $this->addFlash('error', $translator->trans('The venue can not be found'));
            return $services->redirectToReferer('venue');
        }

        $seatingPlan = $services->getVenuesSeatingPlans(array('slug' => $seatingPlanSlug))->getQuery()->getOneOrNullResult();
        if (!$seatingPlan) {
            $this->addFlash('error', $translator->trans('The seating plan can not be found'));
            return $services->redirectToReferer('venue');
        }

        $seatingPlanDuplicated = new VenueSeatingPlan();
        $seatingPlanDuplicated->translate($seatingPlan->getDefaultLocale())->setName($seatingPlan->getName() . ' - ' . $translator->trans('Duplicated'));
        $seatingPlanDuplicated->setDesign($seatingPlan->getDesign());
        $seatingPlanDuplicated->setVenue($seatingPlan->getVenue());
        $seatingPlanDuplicated->setUpdatedAt(new \DateTime());
        $em->persist($seatingPlanDuplicated);
        $seatingPlanDuplicated->mergeNewTranslations();
        $em->flush();
        $this->addFlash('success', $translator->trans('The seating plan has been successfully duplicated'));
        return $this->redirectToRoute("dashboard_organizer_venue_seating_plans", ["venueSlug" => $venueSlug]);
    }
}
