<?php

namespace App\Service\Notification;

use App\Entity\Prospect;
use App\Entity\Template;

class TemplateNotiifcationService
{
    public function render(Template $template, Prospect $prospect)
    {
       return str_replace([
               '%company%',
               "%activity%",
               '%address%',
               '%postalCode%',
               '%city%',
               '%phone%',
               '%mobile%',
               '%email%',
               '%rappel%',
               '%rendezvous%'
           ],
           [
               $prospect->getCompany(),
               $prospect->getActivity(),
               $prospect->getAddress(),
               $prospect->getPostalCode(),
               $prospect->getCity(),
               $prospect->getPhone(),
               $prospect->getMobile(),
               $prospect->getEmail(),
               $prospect->getRappel()?->format("d-m-Y H:i"),
               $prospect->getRendezvous()?->format('d-m-Y H:i')
           ],
           $template->getContent()
        );
    }
}
