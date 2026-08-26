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
        return [
            'id' => $raw['id'] ?? null,
            'email' => $raw['email'] ?? null,
            'first_name' => $raw['firstName'] ?? null,
            'last_name' => $raw['lastName'] ?? null,
            'full_name' => ($raw['firstName'] ?? '') . " " . ($raw['lastName'] ?? '') ?? null,
            'resume' => $raw['resume'] ?? null,
            'gender' => $raw['gender'] ?? null,
            'phone' => $raw['phone'] ?? null,
            'whatsappNumber' => $raw['whatsappNumber'] ?? null,
            'isPhoneNumberVerified' => $raw['isPhoneNumberVerified'] ?? null,
            'isWhatsappVerified' => $raw['isWhatsappVerified'] ?? null,
            'isVerified' => $raw['isVerified'] ?? false,
            'careerStartDate' => $raw['careerStartDate'] ?? null,
            'highestEducation' => $raw['highestEducationLevel'] ?? null,
            'preferredLocations' => $raw['preferredLocations'] ?? [],
            'applicationsCount' => $raw['applicationsCount'] ?? null,
        ];
    }
}
