<?php

$ch = curl_init('https://glints.com/api/oauth2/token');

// === HTTP version (Chrome pakai HTTP/2) ===
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);

// === TLS version ===
curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2); // Chrome 149 masih TLS 1.2/1.3

// === Cipher suites (urutan Chrome 149) ===
curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST,
    'TLS_AES_128_GCM_SHA256:TLS_AES_256_GCM_SHA384:TLS_CHACHA20_POLY1305_SHA256:' .
    'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:' .
    'ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:' .
    'ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:' .
    'ECDHE-RSA-AES128-SHA:ECDHE-RSA-AES256-SHA:AES128-GCM-SHA256:AES256-GCM-SHA384');

// === EC Curves (urutan Chrome) ===
curl_setopt($ch, CURLOPT_SSL_EC_CURVES, 'X25519:P-256:P-384');

// === ALPN aktif ===
curl_setopt($ch, CURLOPT_SSL_ENABLE_ALPN, true);

// === EXTRA: header browser wajib ===
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json, text/plain, */*',
    'accept-language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
    'content-type: application/json;charset=UTF-8',
    'dnt: 1',
    'origin: https://glints.com',
    'referer: https://glints.com/id/en/login',
    'sec-ch-ua: "Google Chrome";v="149", "Chromium";v="149", "Not)A;Brand";v="24"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-origin',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
    'x-glints-country-code: ID'
]);


// === POST data ===
$payload = json_encode([
    'grant_type' => 'password',
    'client_id' => '2f58c66702c29b821efec58b84e1aa84ee2d2a03a3bd2df8aea61fcd5e5ca50d',
    'username' => 'ferdi.cloxt00@gmail.com',
    'password' => 'Gamerz00'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo $response;
