<?php

namespace App\Support;

class DataHelper {
    public static function is_json(string $data): bool
    {
        json_decode($data);
        return json_last_error() === JSON_ERROR_NONE;
    }
    public static function validateJobSearchParams(array $params, array $list): bool
    {
        foreach ($params as $key => $value) {
            // Skip jika key tidak ada dalam list (anggap tidak perlu divalidasi)
            if (!isset($list[$key])) {
                continue;
            }

            $allowed = $list[$key];

            // Penanganan khusus untuk yearsOfExperienceFilter
            if ($key === 'yearsOfExperienceFilter') {
                // $allowed memiliki struktur [['range' => [...]]]
                if (isset($allowed[0]['range']) && is_array($allowed[0]['range'])) {
                    $allowedValues = $allowed[0]['range'];
                    // Nilai input bisa berupa array dengan key 'range' atau langsung nilai
                    if (is_array($value) && isset($value['range'])) {
                        $inputValue = $value['range'];
                        if (is_array($inputValue)) {
                            foreach ($inputValue as $item) {
                                if (!in_array($item, $allowedValues)) {
                                    return false;
                                }
                            }
                        } else {
                            if (!in_array($inputValue, $allowedValues)) {
                                return false;
                            }
                        }
                    } else {
                        // Jika input bukan format yang diharapkan, anggap tidak valid
                        return false;
                    }
                } else {
                    // Format allowed tidak sesuai
                    return false;
                }
                continue;
            }

            // Untuk key biasa
            if (is_array($value)) {
                foreach ($value as $item) {
                    if (!in_array($item, $allowed)) {
                        return false;
                    }
                }
            } else {
                if (!in_array($value, $allowed)) {
                    return false;
                }
            }
        }
        return true;
    }
}

