<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\JobstreetAccount;
use App\Services\JobstreetService;
use App\Clients\JobstreetAPI;

$user = User::find(1);
$account = $user->jobstreetAccount;
if(!$account){
    echo("Account not found");
    exit();
}

$client = new JobstreetAPI($account->access_token);
// print_r($account->access_token);


$service = new JobstreetService($client);

$search = $service->job()->search([
    'pageSize' => 1,
    'location' => 'Banten',
    'keyword' => 'Developer'
]);
$job = $service->job();
// $document = $service->documents();
// $review = $service->review();
// var_dump($search);
// print_r(json_encode($search));
print_r($job->applied(3000));
// print_r($document->get_latest_resume());
// print_r($review->get_latest_roles());
