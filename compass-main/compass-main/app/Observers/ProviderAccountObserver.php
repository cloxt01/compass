<?php

namespace App\Observers;

use App\Models\GlintsAccount;
use App\Models\JobstreetAccount;

class ProviderAccountObserver
{
    public function deleted(GlintsAccount | JobstreetAccount $account)
    {
        $user = $account->user;

        if ($user) {
            $hasGlints = $user->glintsAccount()->exists();
            $hasJobstreet = $user->jobstreetAccount()->exists();


            if (!$hasGlints && !$hasJobstreet) {
                $user->update(['automation_paused' => true]);
            }
        }
    }
}
