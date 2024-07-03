<?php

namespace App\Validation;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Validator {

    public static function validate($object, ExecutionContextInterface $context, $payload) {

        global $kernel;
        $defaultlocalecheck = false;

        foreach ($object->getTranslations() as $translation) {
            if ($translation->getLocale() === $kernel->getContainer()->getParameter('locale')) {
                $defaultlocalecheck = true;
                break;
            }
        }

        if (!$defaultlocalecheck) {
            $context->buildViolation('You must set the default locale at least for the translation fields')
                    ->atPath('translations')
                    ->addViolation();
        }
    }

    public static function validateEvent($object, ExecutionContextInterface $context, $payload) {

        global $kernel;
        $defaultlocalecheck = false;

        foreach ($object->getTranslations() as $translation) {
            if ($translation->getLocale() === $kernel->getContainer()->getParameter('locale')) {
                $defaultlocalecheck = true;
                break;
            }
        }

        if (!$defaultlocalecheck) {
            $context->buildViolation('You must set the default locale at least for the translation fields')
                    ->atPath('translations')
                    ->addViolation();
        }

        foreach ($object->getEventdates() as $indexEventDate => $eventDate) {
            foreach ($eventDate->getTickets() as $indexDateTicket => $eventDateTicket) {
                if (!$eventDateTicket->getFree() && !$eventDateTicket->getPrice()) {
                    $context->buildViolation('This value should not be blank.')
                            ->atPath('eventdates[' . $indexEventDate . '].tickets[' . $indexDateTicket . '].price')
                            ->addViolation();
                }
                if ($eventDate->getHasSeatingPlan() === true && $eventDate->getSeatingPlan() !== null && count($eventDateTicket->getSeatingPlanSections()) == 0) {
                    $context->buildViolation('This value should not be blank.')
                            ->atPath('eventdates[' . $indexEventDate . '].tickets[' . $indexDateTicket . '].seatingPlanSections')
                            ->addViolation();
                }
            }
        }

        foreach ($object->getEventdates() as $indexEventDate => $eventDate) {
            foreach ($eventDate->getTickets() as $indexDateTicket => $eventDateTicket) {
                foreach ($eventDate->getTickets() as $indexDateTicketToCheck => $eventDateTicketToCheck) {
                    if ($eventDateTicket != $eventDateTicketToCheck) {
                        foreach ($eventDateTicket->getSeatingPlanSections() as $sectionName) {
                            if (in_array($sectionName, $eventDateTicketToCheck->getSeatingPlanSections())) {
                                $context->buildViolation('Section "' . $sectionName . '" has been already assigned')
                                        ->atPath('eventdates[' . $indexEventDate . '].tickets[' . $indexDateTicket . '].seatingPlanSections')
                                        ->addViolation();
                            }
                        }
                    }
                }
            }
        }
    }
}
