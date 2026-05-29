<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Clients\JobstreetAPI;
use Illuminate\Support\Facades\Log;

$user = User::first();
if (!$user) {
    echo "No users\n";
    exit(1);
}

$account = $user->jobstreetAccount;
if (!$account) {
    echo "User has no Jobstreet account\n";
    exit(1);
}

$client = new JobstreetAPI($account->access_token, $account->refresh_token ?? null);
$res = $client->graphql('GetAppliedJobs', ['first' => 1], ['debug' => true, 'cookies' => true]);

print_r($res);

?>