<?php

namespace App\Services;


class ProfileDetails
{

    public static function fromJobstreet($raw){
        return [
            'resumes' => $raw['data']['viewer']['resumes'] ?? [],
            'roles' => $raw['data']['viewer']['roles'] ?? [],
            'skills' => $raw['data']['viewer']['skills'] ?? [],
            'latest_resume' => end($raw['data']['viewer']['resumes']) ?? null,
            'latest_roles' => $raw['data']['viewer']['roles'][0] ?? null,
            'profile_visibility' => [
                '1' => $raw['data']['viewer']['profileVisibility']['level'] ?? null,
                '2' => $raw['data']['viewer']['profileVisibility2']['id'] ?? null
            ],
            'qualifications' => $raw['data']['viewer']['qualifications'] ?? [],
            'reference_checks' => $raw['data']['viewer']['referenceChecks'] ?? []
        ];
    }
    public static function fromGlints($raw){
        return [];
    }
}