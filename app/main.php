<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Clients\JobstreetAPI;
use App\Clients\GlintsAPI;
use App\Services\SearchJobs;

$cookie_gl = '_gcl_au=1.1.2088390296.1766588940; _ga=GA1.1.1051728370.1766588941; ab180ClientId=379d376e-d31e-49c0-84dd-63412a07981c; g_state={"i_l":0,"i_ll":1766589007485,"i_b":"fJGKPzaLqvASi2rfw8PTZG8C/P8f7C3C4adKY93IBbE","i_e":{"enable_itp_optimization":0}}; cf_clearance=CTxa9TResqEcfzCLHAtzkIMxr2cefBFm4Iki7TTa_lU-1769927121-1.2.1.1-hZhew7Oy.PXPeWSMsKSUrvzL0JuaHUrIWA50szcM4OXtnWJ1wbFwbEHryxXoWSbPujdo4eMFT3VHq11Cm8qgHlXEi8aK.K_b9saOuNIZnzWgwbjw8cAZCzPMlGcIzLB5Z9WPDPEa8NhXF742_19_BMdbohcFueV.rwD7jX7M.TU2x39Scdngc.D5xhSRgnhrd9oAEktd9m5RW3Q5g_I4X3BR_ghL1tIMmvNGJJiMg.I; device_id=10ab8ddd-145b-48e6-97d3-2f6411dff6b9; builderSessionId=0eac103d2c5248eb9eb4dbc45be4dfed; glints_tracking_id=0642957d-0f05-4419-bc5f-215fda8954bf; sessionFirstTouchPath=/id; sessionLastTouchPath=/id; airbridge_migration_metadata__taplokerbyglints=%7B%22version%22%3A%221.11.1%22%7D; airbridge_touchpoint__taplokerbyglints=%7B%22channel%22%3A%22glints.com%22%2C%22parameter%22%3A%7B%7D%2C%22generationType%22%3A1224%2C%22url%22%3A%22https%3A//glints.com/id%22%2C%22timestamp%22%3A1769927169369%7D; session=Fe26.2**b8b106a2e3e82b580a73b634f8b2113f08580466e43a0b09392c44f3d16ca569*JLvBuAryE3SipVShzk0MVA*icyaR_4he6WyMogi6wS8qBO5P4UL6_WdRglBLL5ZKu93OkoRhz65O20OqsFavPa6**f7aa42995d5c0b7acbbdf7df00d153ce32554ca42dc105baf18f1ef7e601931e*i6x2p-ZckZoV00HL66bIgqXXR0q4TxntGkuk9KO8Dew; airbridge_user__taplokerbyglints=%7B%22attributes%22%3A%7B%22country_code%22%3A%22ID%22%2C%22role%22%3A%22CANDIDATE%22%2C%22has_whatsapp_number%22%3Atrue%2C%22user_email%22%3A%22ferdi.cloxt00@gmail.com%22%2C%22days_from_signup%22%3A603%2C%22has_resume%22%3Atrue%2C%22has_mobile_number%22%3Atrue%2C%22age_in_years%22%3A20%2C%22gender%22%3A%22male%22%2C%22number_of_skills_listed%22%3A10%2C%22utm_campaign%22%3A%22%22%2C%22utm_referrer%22%3A%22explore%22%2C%22utm_medium%22%3A%22%22%2C%22utm_source%22%3A%22%22%7D%2C%22externalUserID%22%3A%2257cdb1bf-7495-4c03-82c4-74fa1baefa6f%22%2C%22externalUserEmail%22%3A%22ferdi.cloxt00@gmail.com%22%7D; airbridge_session__taplokerbyglints=%7B%22id%22%3A%224d8a58ba-bf6c-45c1-9bb6-14d241238daf%22%2C%22timeout%22%3A1800000%2C%22start%22%3A1769927131415%2C%22end%22%3A1769927610802%7D; sessionIsLastTouch=false; _ga_FQ75P4PXDH=GS2.1.s1769927129$o2$g1$t1769927623$j47$l0$h0; traceInfo=%7B%22expInfo%22%3A%22mgtExperimentWebFYPMultRetrievals%3Abase%2CmgtDstExperimentRecmdApplication%3Abase_0210_decay_3%2CmgtDstExperimentRecmdPaidJob%3Aexp_2_1206%2CmgtDstExperimentRecmdConversionRate%3Agroup_10_0812%2CmgtDstExpSalaryWeight%3Aexp_0619_w3%2CmgtDstExpUserTrafficControlByScore%3Aexperiment%2CmgtDstExpFYPJobType%3Abase_0828%2CmgtDstExpFYPAge%3Aexp_0830_w2%2CmgtMobExperimentJobCardUI%3Aexp_new_1227%2CmgtOneTapApplyVIPJobs%3AB%22%2C%22requestId%22%3A%22edd69fa8cd8378556f74aaa9c51294e8%22%7D';
$jb = new JobstreetAPI(token: User::find(1)->jobstreetAccount->access_token);
// $gl = new GlintsAPI(cookie: $cookie_gl);

// $search = (new SearchJobs($jb))->search();

// print_r($search);
print_r($jb->graphql('DocumentQuery', [
    'jobId' => '89903493'
]));
// print_r($gl->graphql('searchHierarchicalLocations'));
// print_r($search);
// print_r($gl);k

