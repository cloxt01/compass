<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Clients\Application\UseCase\ApplyUseCase;
use App\Clients\GlintsAPI;
use App\Models\User;
use App\Models\JobstreetAccount;
use App\Services\Adapters\GlintsAdapter;
use App\Services\Adapters\JobstreetAdapter;
use App\Clients\JobstreetAPI;
use App\Services\JobDetails;
use App\Support\DataHelper;
use App\Services\Glints\API as GlintsService;
use App\Http\Controllers\Api\Glints\api as GlintsApiService;

$user = User::find(1);
$jobstreetaccount = $user->jobstreetAccount;
$glintsaccount = $user->glintsAccount;

if(!$jobstreetaccount){
    echo("jobstreetAccount not found");
}
if(!$glintsaccount){
    echo("glintsAccount not found");
}

//$cookie = config('compass.platforms.jobstreet.cookie');
//$client = new JobstreetAPI($account->access_token, $cookie);
//// print_r($account->access_token);
//print_r($client);

$cookie = $jobstreetaccount->cookie;
$token = $jobstreetaccount->access_token;
if(!$cookie){
    echo("cookie not found\n");
}
if(!$token){
    echo("token not found\n");
}

$jobstreet_client = new JobstreetAPI($token, $cookie);
$jobstreet_adapter = new JobstreetAdapter($jobstreet_client);
$glints_client = new GlintsAPI($glintsaccount->access_token, $glintsaccount->cookie);
$glints_service = new GlintsService($glints_client);
//$glintsApi = new GlintsApiService($glints_client);
//
//$profile = $jobstreet_adapter->loadProfile();
$jobs = $jobstreet_adapter->job()->search([
    [
        'keyword'  => "Teknisi",
        'pageSize' => 1,
    ]
]);
print_r(json_encode($jobs));

//var_dump($profile);
//$data= $glints_service->search_location('Lebak');
//var_dump($data);
//$job = $adapter->job();
//var_dump(JobDetails::fromJobstreet($job->details('92727497')));

//print_r($job->hiring_question(['jobId' => '403c0949-fc7e-44ba-acfb-c78dac3f5a9b']));
//$useCase = new ApplyUseCase($adapter, $glintsaccount);
//$jobId = '01e2f7a1-8d54-4aea-95c9-2904a1409dce';
//var_dump($useCase->apply($jobId));
//print_r($adapter->job()->search([
//    'CountryCode' => 'ID',
//    'searchTerm' => 'IT',
//    'LocationIds' => ["ea61f4ac-5864-4b2b-a2c8-aa744a2aafea","3d6ac5e8-f12d-40cd-823d-44597402ea3b","078b37b2-e791-4739-958e-c29192e5df3e","e2a615bb-0997-42c8-a018-c4a1768ae01f","af0ed74f-1b51-43cf-a14c-459996e39105","ddd28ed9-d36c-48e1-aa74-de3006122569","262b7d3c-2c51-4854-a7e8-6635e0657338","fbc373c5-cadc-4dd8-acb3-1e57850e2a08","f5817918-7ce1-436d-9478-c438b3466adb","87c39cfb-0e3c-4edb-a7a0-754fb70cc587","826664b3-1f31-497f-bc8a-a23699b1a531","337f8da8-70d8-4916-9733-1cee8e4902e9","ae3c458e-5947-4833-8f1b-e001ce2fad1d","d305a80e-4891-45a4-8c5b-29c9242f431e"],
//    'includeExternalJobs' => false,
//    'pageSize' => 30,
//    'page' => 1
//
//]));

//print_r($job->loadJob('403c0949-fc7e-44ba-acfb-c78dac3f5a9b'));
//
//$list = ['CountryCode' => ['ID'],
//    'lastUpdatedAtRange' => ['ANY_TIME', 'PAST_MONTH', 'PAST_24_HOURS', 'PAST_WEEK'],
//    'type' => [
//        'FULL_TIME',
//        'CONTRACT',
//        'INTERNSHIP',
//        'PROJECT_BASED',
//        'PART_TIME'
//    ],
//    'workArrangementOptions' => [
//        'ONSITE',
//        'HYBRID',
//        'REMOTE'
//    ],
//    'educationLevels' => [
//        "PRIMARY_SCHOOL",
//        "SECONDARY_SCHOOL",
//        "HIGH_SCHOOL",
//        "DIPLOMA",
//        "BACHELOR_DEGREE",
//        "PROFESSIONAL_EDUCATION",
//        "MASTER_DEGREE",
//        "DOCTORATE"
//    ],
//    'yearsOfExperienceFilter' => [
//        ['range' => [
//            "NO_EXPERIENCE",
//            "FRESH_GRAD",
//            "LESS_THAN_A_YEAR",
//            "ONE_TO_THREE_YEARS",
//            "THREE_TO_FIVE_YEARS",
//            "FIVE_TO_TEN_YEARS",
//            "MORE_THAN_TEN_YEARS"
//        ]]
//    ]
//];
//$params = [
//    'CountryCode' => 'I',
//    'type' => ['FULL_TIME', 'INTERNSHIP'],
//    'yearsOfExperienceFilter' => ['range' => 'sa']
//];

//$adapter = new JobstreetAdapter($client);


// print_r($adapter);

// $search = $adapter->job()->search([
//     'pageSize' => 3,
//     'location' => 'Banten',
//     'keyword' => 'IT'
// ]);
// $applied = $adapter->job()->applied(5);
// print_r($applied);

// $job = $adapter->loadJob('90201451');
// $canApply = $adapter->canApply($job);
// print_r($job);
// print_r($client->graphql('jobDetailsWithPersonalised', ['jobId' => '90201451']));

//$profile = $adapter->loadProfile();
//print_r($profile);
// print_r($job);
// $job = $adapter->job()->details('89707772');
// $payload = $adapter->buildPayload($job, $profile);
// // $document = $service->documents();
// // $review = $service->review();
// // var_dump($search);
// // var_dump($job);
//
//print_r($adapter->job()->applied(10));
// foreach($search['data']['data'] as $jobData){
//     $job = $adapter->loadJob($jobData['id']);
//     $profile = $adapter->loadProfile();
//     $canApply = $adapter->canApply($job);
//     if($canApply['canApply']){
//         $applied = $adapter->execute($adapter->buildPayload($job, $profile));
//         print_r("Applied to Job ID: " . $jobData['id'] . " - " . ($applied ? "Success" : "Failed") . "\n");
//     } else {
//         print_r("Cannot apply to Job ID: " . $jobData['id'] . " - Reason: " .  $canApply['issues'][0]['message'] . "\n");
//     }
// }


// print_r($search);
// print_r($job);
// var_dump($adapter->canApply($job));
// print_r(json_encode($search));
// print_r($document->get_latest_resume());
// print_r($review->get_latest_roles());
