<?php
/**
 * Glints Curl Executor v2
 * Support: argumen, file, STDIN
 */

// ============================================================
// 1. BACA INPUT
// ============================================================
$rawCurl = '';

if ($argc > 1) {
    // Cek apakah ada argumen --file
    if (strpos($argv[1], '--file=') === 0) {
        $file = substr($argv[1], 7);
        if (file_exists($file)) {
            $rawCurl = file_get_contents($file);
        } else {
            die("❌ File tidak ditemukan: $file\n");
        }
    } else {
        // Gabungkan semua argumen sebagai satu string
        $rawCurl = implode(' ', array_slice($argv, 1));
    }
}

// Jika masih kosong, baca dari STDIN
if (empty(trim($rawCurl))) {
    echo "📥 Paste curl command, lalu tekan ENTER.\n";
    echo "   (Ketik '__END__' pada baris baru untuk mengakhiri, atau Ctrl+Z+Enter di Windows)\n\n";

    $stream = fopen('php://stdin', 'r');
    $lines = [];
    while (($line = fgets($stream)) !== false) {
        $trimmed = trim($line);
        if ($trimmed === '__END__') break;
        $lines[] = $line;
    }
    fclose($stream);
    $rawCurl = implode('', $lines);
}

$rawCurl = trim($rawCurl);
if (empty($rawCurl)) {
    echo "❌ Tidak ada input curl.\n";
    echo "Cara pakai:\n";
    echo "  php glints_curl_exec.php \"curl ...\"\n";
    echo "  php glints_curl_exec.php --file=request.txt\n";
    echo "  atau paste curl command, lalu '__END__' pada baris baru.\n";
    exit(1);
}

// ============================================================
// 2. PARSE CURL
// ============================================================
function parseCurl($raw) {
    // Hapus backslash newline
    $raw = preg_replace("/\\\\\s*\n/", ' ', $raw);
    $raw = trim($raw);

    // URL
    preg_match("/curl\s+(?:-X\s+\w+\s+)?'([^']+)'/", $raw, $matches);
    $url = $matches[1] ?? '';

    // Method
    $method = 'GET';
    if (preg_match("/-X\s+(\w+)/", $raw, $m)) $method = strtoupper($m[1]);
    if (strpos($raw, '--data-raw') !== false || strpos($raw, '-d') !== false) $method = 'POST';

    // Headers
    $headers = [];
    preg_match_all("/-H\s+'([^']+)'/", $raw, $matches);
    foreach ($matches[1] as $h) $headers[] = $h;

    // Body
    $body = null;
    if (preg_match("/--data-raw\s+'([^']*)'/", $raw, $m)) $body = $m[1];
    elseif (preg_match("/-d\s+'([^']*)'/", $raw, $m)) $body = $m[1];

    // Cookies
    $cookies = '';
    if (preg_match("/-b\s+'([^']+)'/", $raw, $m)) $cookies = $m[1];

    return compact('url', 'method', 'headers', 'body', 'cookies');
}

$parsed = parseCurl($rawCurl);

if (empty($parsed['url'])) {
    echo "❌ Gagal parse URL. Pastikan curl command valid.\n";
    exit(1);
}

echo "✅ Parsed: " . $parsed['method'] . ' ' . $parsed['url'] . "\n";

// ============================================================
// 3. EKSEKUSI
// ============================================================
function executeRequest($parsed) {
    $ch = curl_init($parsed['url']);
    $COOKIE_STRING = 'device_id=ai-auto-answer; _gcl_au=1.1.1667493223.1781169157; sessionFirstTouchPath=/id/en; ab180ClientId=ai-auto-answer; _ga=GA1.1.1442324188.1781169167; pastJobSearchConditions=%5B%7B%22keyword%22%3A%22System%20Engineer%22%2C%22country%22%3A%22ID%22%2C%22locationName%22%3A%22All%20Cities%2FProvinces%22%2C%22lowestLocationLevel%22%3A%221%22%7D%2C%7B%22keyword%22%3A%22IT%20Trainer%22%2C%22country%22%3A%22ID%22%2C%22locationName%22%3A%22Jabodetabek%22%2C%22locationId%22%3A%22JABODETABEK%22%7D%2C%7B%22keyword%22%3A%22IT%20Trainer%22%2C%22country%22%3A%22ID%22%2C%22locationName%22%3A%22All%20Cities%2FProvinces%22%2C%22lowestLocationLevel%22%3A%221%22%7D%5D; session=Fe26.2**2dc26d0c7d4cb49468fbf992d3433c23ff7d8e6864eef2a848baad245f0d0ae6*gPfV-fgmmyuRulGSmSomGg*lzlM09X9HQtusTtC8JdsfA02ELFBrt52HAzCKmGChzLwE5pWMDaR9Q8E-YidfEVB**bf7c9e799e9187d637b71a44a8d29290d1c3905c2a72c89cbf4ff1f12286b0a7*sEyFvq7Rs9hpc6pQj7ONY81I9cNiFvlwj5p-DsJuAD0; _ga_WMM977BJLD=GS2.1.s1782210180$o2$g0$t1782210184$j56$l0$h0; g_state={"i_l":0,"i_ll":1782210186816,"i_b":"UFHuhziTX+wPne75/DGX+McXn7i4YCD5cmdOg1PV85Q","i_e":{"enable_itp_optimization":24},"i_et":1782210186816}; ridge_migration_metadata__taplokerbyglints=%7B%22version%22%3A%221.11.12%22%7D; sessionLastTouchPath=/id/opportunities/jobs/recommended; glints_tracking_id=ai-auto-answer; sessionIsLastTouch=false; traceInfo=%7B%22expInfo%22%3A%22%22%2C%22requestId%22%3A%22971905640a641b9c6289e763a5631761%22%7D; _ga_FQ75P4PXDH=GS2.1.s1782245294$o21$g1$t1782252662$j47$l0$h0;';

    if (!empty($COOKIE_STRING)) {
        curl_setopt($ch, CURLOPT_COOKIE, $COOKIE_STRING);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

    $cipherList = 'TLS_AES_128_GCM_SHA256:TLS_AES_256_GCM_SHA384:TLS_CHACHA20_POLY1305_SHA256:' .
        'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:' .
        'ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:' .
        'ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:' .
        'ECDHE-RSA-AES128-SHA:ECDHE-RSA-AES256-SHA:AES128-GCM-SHA256:AES256-GCM-SHA384';
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, $cipherList);
    curl_setopt($ch, CURLOPT_SSL_EC_CURVES, 'X25519:P-256:P-384');
    curl_setopt($ch, CURLOPT_SSL_ENABLE_ALPN, true);

    $method = $parsed['method'];
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($parsed['body'] !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $parsed['body']);
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($parsed['body'] !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $parsed['body']);
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    $defaultHeaders = [
        'Accept: application/json, text/plain, */*',
        'Content-Type: application/json;charset=UTF-8',
        'DNT: 1',
        'Origin: https://glints.com',
        'Referer: https://glints.com/',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-origin',
    ];
    $allHeaders = $defaultHeaders;
    foreach ($parsed['headers'] as $h) {
        $key = strtolower(explode(':', $h, 2)[0]);
        $found = false;
        foreach ($allHeaders as $existing) {
            if (strtolower(explode(':', $existing, 2)[0]) === $key) {
                $found = true;
                break;
            }
        }
        if (!$found) $allHeaders[] = $h;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);

    if (!empty($parsed['cookies'])) curl_setopt($ch, CURLOPT_COOKIE, $parsed['cookies']);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'response' => $response,
        'error' => $error,
        'success' => $httpCode >= 200 && $httpCode < 300
    ];
}

$result = executeRequest($parsed);

// ============================================================
// 4. TAMPILKAN HASIL
// ============================================================
echo "📡 HTTP Status: " . $result['status'] . "\n";
if ($result['error']) {
    echo "❌ cURL Error: " . $result['error'] . "\n";
} else {
    $json = json_decode($result['response'], true);
    if ($json) {
        echo "✅ Response (JSON):\n";
        print_r($json);
    } else {
        echo "📄 Response (raw):\n" . $result['response'] . "\n";
    }
}
