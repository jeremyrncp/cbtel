<?php

namespace App\Service;

use App\Entity\Campaign;
use App\Entity\User;
use App\Entity\UserCampaign;

class UserService
{
    public function isAbilitedToCampaign(User $user, Campaign $campaign): bool
    {
        /** @var UserCampaign $userCampaign */
        foreach ($user->getUserCampaigns() as $userCampaign) {
            if ($campaign === $userCampaign->getCampaign()) {
                return true;
            }
        }

        return false;
    }
}
