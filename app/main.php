<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\JobstreetAccount;
use App\Services\JobstreetService;
use App\Clients\JobstreetAPI;

$user = User::find(2);
$account = $user->jobstreetAccount;
if(!$account){
    echo("Account not found");
    exit();
}

$client = new JobstreetAPI($account->access_token);
print_r($account->access_token);


$service = new JobstreetService($client);

$search = $service->search_jobs()->search();

print_r($search);