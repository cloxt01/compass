<?php

namespace App\Services;


class ProfileDetails
{

    public static function fromJobstreet($raw){
        return [
            'resumes' => $raw['document']['resumes'] ?? [],
            'roles' => $raw['review']['roles'] ?? [],
            'skills' => $raw['review']['skills'] ?? [],
            'latest_resume' => ($raw['document']['resumes'] ?? []) ? end($raw['document']['resumes']) : null,
            'latest_roles' => $raw['review']['roles'][0] ?? null,
            'profile_visibility' => [
                '1' => $raw['review']['profileVisibility']['level'] ?? null,
                '2' => $raw['review']['profileVisibility2']['id'] ?? null
            ],
            'qualifications' => $raw['review']['qualifications'] ?? [],
            'reference_checks' => $raw['review']['referenceChecks'] ?? []
        ];
    }
    public static function fromGlints($raw){
        return [];
    }
}