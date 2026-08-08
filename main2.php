<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Clients\GlintsAPI;
use App\Clients\JobstreetAPI;
use App\Models\User;
use App\Services\Adapters\Provider\GlintsAdapter;
use App\Services\Adapters\Provider\JobstreetAdapter;
use App\Services\Platform\Glints\GlintsHelper as GlintsService;

$user = User::find(1);
//$jobstreetaccount = $user->jobstreetAccount;
$glintsaccount = $user->glintsAccount;

//if(!$jobstreetaccount){
//    echo("jobstreetAccount not found");
//}
if(!$glintsaccount){
    echo("glintsAccount not found");
}

//$cookie = $jobstreetaccount->cookie ?? '';
//$token = $jobstreetaccount->access_token ?? '';
//if(!$cookie){
//    echo("cookie not found\n");
//}
//if(!$token){
//    echo("token not found\n");
//}

//$jobstreet_client = new JobstreetAPI($token, $cookie);
//$jobstreet_adapter = new JobstreetAdapter($jobstreet_client);
$glints_client = new GlintsAPI($glintsaccount->access_token ?? '', $glintsaccount->cookie ?? '');
$glints_adapter = new GlintsAdapter($glints_client);

$glints_service = new GlintsService($glints_client);
$profile_glints = $glints_adapter->loadProfile();
$job = $glints_adapter->loadJob('22a8ef91-f588-4c5c-8dd9-2e711b880927');
$questionnaire = $job['products']['questionnaire'];
print_r("Questionnaire : ".json_encode($questionnaire));
$answer = $glints_adapter->answerQuestion($profile_glints, $questionnaire);
print_r("Answer : ".json_encode($answer));

