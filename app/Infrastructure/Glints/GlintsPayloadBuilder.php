<?php

namespace App\Infrastructure\Glints;

class GlintsPayloadBuilder
{
    public function __construct()
    {}

    public function build(array $details, array $profile, array $config)
    {
//        print_r($details);
//        print_r($profile);
//        print_r($config);

        return [
            'data' => [
                'resume' => $profile['resume'],
                'employerScreeningQuestionAnswers' => [],
                'note' => '',
                'attachments' => []
            ],
            'source' => 'For You',
            'traceInfo' => bin2hex(random_bytes(16))
        ];
    }
}
