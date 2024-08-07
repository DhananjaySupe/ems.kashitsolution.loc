<?php

namespace App\Form;

use App\Entity\EventTicket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use App\Service\AppServices;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EventTicketType extends AbstractType {

    private $services;
    private $user;
    private $seatingPlansSections = [];

    public function __construct(AppServices $services, TokenStorageInterface $tokenStorage) {
        $this->services = $services;
        $this->user = $tokenStorage->getToken()->getUser();
        foreach ($this->user->getOrganizer()->getVenues() as $venue) {
            if (count($venue->getSeatingPlans()) > 0) {
                foreach ($venue->getSeatingPlans() as $seatingPlan) {
                    $this->seatingPlansSections[$seatingPlan->getVenue()->getName() . ' - ' . $seatingPlan->getName()] = $seatingPlan->getSectionsNamesArray();
                }
            }
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('active', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'label' => 'Enable sales for this ticket ?',
                    'choices' => ['Yes' => true, 'No' => false],
                    'attr' => ['class' => 'is-ticket-active'],
                    'label_attr' => ['class' => 'radio-custom radio-inline']
                ])
                ->add('name', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Ticket name',
                    'help' => 'Early bird, General admission, VIP...'
                ])
                ->add('description', TextareaType::class, [
                    'purify_html' => true,
                    'required' => false,
                    'label' => 'Ticket description',
                    'help' => 'Tell your attendees more about this ticket type'
                ])
                ->add('free', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'label' => 'Is this ticket free ?',
                    'choices' => ['No' => false, 'Yes' => true],
                    'attr' => ['class' => 'is-ticket-free-radio'],
                    'label_attr' => ['class' => 'radio-custom radio-inline']
                ])
                ->add('price', TextType::class, [
                    'purify_html' => true,
                    'required' => false,
                    'label' => 'Price',
                    'attr' => ['class' => 'touchspin-decimal event-date-ticket-price', 'data-min' => '0', "data-max" => '100000000']
                ])
                ->add('promotionalprice', TextType::class, [
                    'purify_html' => true,
                    'required' => false,
                    'label' => 'Promotional price',
                    'help' => 'Set a price lesser than than the original price to indicate a promotion (this price will be the SALE price)',
                    'attr' => ['class' => 'touchspin-decimal event-date-ticket-promotionalprice', 'data-min' => '0', "data-max" => '100000000']
                ])
                ->add('seatingPlanSections', ChoiceType::class, [
                    'required' => true,
                    'multiple' => true,
                    'expanded' => false,
                    'label' => 'Seating plan sections',
                    'choices' => $this->seatingPlansSections,
                    'choice_value' => function ($sectionName) {
                        return $sectionName;
                    },
                    'help' => '<ul class="list-unstyled"><li>Press CTRL to select multiple sections, press SHIFT to select all sections</li><li>A section can only be assigned to one ticket in an event date</li></ul>',
                    'help_html' => true,
                    'attr' => ['class' => 'event-date-ticket-seating-plan-sections '],
                ])
                ->add('quantity', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Quantity',
                    'attr' => ['class' => 'touchspin-integer event-date-ticket-quantity', 'data-min' => '1', "data-max" => '1000000']
                ])
                ->add('ticketsperattendee', TextType::class, [
                    'purify_html' => true,
                    'required' => false,
                    'label' => 'Tickets per attendee',
                    'help' => 'Set the number of tickets that an attendee can buy for this ticket type',
                    'attr' => ['class' => 'touchspin-integer', 'data-min' => '1', "data-max" => '1000000']
                ])
                ->add('salesstartdate', DateTimeType::class, [
                    'required' => false,
                    'label' => 'Sale starts On',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'datetimepicker']
                ])
                ->add('salesenddate', DateTimeType::class, [
                    'required' => false,
                    'label' => 'Sale ends On',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'datetimepicker']])
                ->add('position', HiddenType::class, [
                    'attr' => [
                        'class' => 'event-date-ticket-position']])
                // Set automatically on entity creation (generation function on entity class),
                // added here as a trick to identity the event ticket
                // fieldset to set the data-min attribute as the current ticket sales number
                ->add('reference', HiddenType::class, [
                    'attr' => [
                        'class' => 'event-date-ticket-reference']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => EventTicket::class,
            'validation_groups' => ['create', 'update']
        ]);
    }
}
