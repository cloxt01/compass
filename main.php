<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\JobstreetAccount;
use App\Services\Adapters\JobstreetAdapter;
use App\Clients\JobstreetAPI;

$user = User::find(1);
$account = $user->jobstreetAccount;
if(!$account){
    echo("Account not found");
    exit();
}

$client = new JobstreetAPI($account->access_token);
// print_r($account->access_token);


$adapter = new JobstreetAdapter($client);
// print_r($adapter);

$search = $adapter->job()->search([
    'pageSize' => 3,
    'location' => 'Banten',
    'keyword' => 'IT'
]);
// $applied = $adapter->job()->applied(5);
// print_r($applied);

// $job = $adapter->loadJob('90201451');
// $canApply = $adapter->canApply($job);
// print_r($job);
// print_r($client->graphql('jobDetailsWithPersonalised', ['jobId' => '90201451']));

// $profile = $adapter->loadProfile();
// print_r($job);  
// $job = $adapter->job()->details('89707772');
// $payload = $adapter->buildPayload($job, $profile);
// // $document = $service->documents();
// // $review = $service->review();
// // var_dump($search);
// // var_dump($job);
foreach($search['data']['data'] as $jobData){
    $job = $adapter->loadJob($jobData['id']);
    $profile = $adapter->loadProfile();
    $canApply = $adapter->canApply($job);
    if($canApply['canApply']){
        $applied = $adapter->execute($adapter->buildPayload($job, $profile));
        print_r("Applied to Job ID: " . $jobData['id'] . " - " . ($applied ? "Success" : "Failed") . "\n");
    } else {
        print_r("Cannot apply to Job ID: " . $jobData['id'] . " - Reason: " .  $canApply['issues'][0]['message'] . "\n");
    }
}
// print_r($search);
// print_r($job);
// var_dump($adapter->canApply($job));
// print_r(json_encode($search));
// print_r($document->get_latest_resume());
// print_r($review->get_latest_roles());
