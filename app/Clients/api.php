<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use App\Support\QueryHelper;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;

class api {
    protected ?string $token;
    protected ?string $cookie;
    protected array $headers;

    public function __construct(?string $token = null, ?string $cookie = null) {}

    public function api()
    {
        $COOKIE_STRING = 'device_id=9abd1401-74f1-43ec-84e7-8e2a2f468524; _gcl_au=1.1.1667493223.1781169157; sessionFirstTouchPath=/id/en; ab180ClientId=124b47e9-48c9-4692-b6cc-08375f072cfd; _ga=GA1.1.1442324188.1781169167; pastJobSearchConditions=%5B%7B%22keyword%22%3A%22System%20Engineer%22%2C%22country%22%3A%22ID%22%2C%22locationName%22%3A%22All%20Cities%2FProvinces%22%2C%22lowestLocationLevel%22%3A%221%22%7D%2C%7B%22keyword%22%3A%22IT%20Trainer%22%2C%22country%22%3A%22ID%22%2C%22locationName%22%3A%22Jabodetabek%22%2C%22locationId%22%3A%22JABODETABEK%22%7D%2C%7B%22keyword%22%3A%22IT%20Trainer%22%2C%22country%22%3A%22ID%22%2C%22locationName%22%3A%22All%20Cities%2FProvinces%22%2C%22lowestLocationLevel%22%3A%221%22%7D%5D; session=Fe26.2**2dc26d0c7d4cb49468fbf992d3433c23ff7d8e6864eef2a848baad245f0d0ae6*gPfV-fgmmyuRulGSmSomGg*lzlM09X9HQtusTtC8JdsfA02ELFBrt52HAzCKmGChzLwE5pWMDaR9Q8E-YidfEVB**bf7c9e799e9187d637b71a44a8d29290d1c3905c2a72c89cbf4ff1f12286b0a7*sEyFvq7Rs9hpc6pQj7ONY81I9cNiFvlwj5p-DsJuAD0; _ga_WMM977BJLD=GS2.1.s1782210180$o2$g0$t1782210184$j56$l0$h0; g_state={"i_l":0,"i_ll":1782210186816,"i_b":"UFHuhziTX+wPne75/DGX+McXn7i4YCD5cmdOg1PV85Q","i_e":{"enable_itp_optimization":24},"i_et":1782210186816}; ridge_migration_metadata__taplokerbyglints=%7B%22version%22%3A%221.11.12%22%7D; sessionLastTouchPath=/id/opportunities/jobs/recommended; glints_tracking_id=23e5b13c-07f4-44ef-8672-03e6d8e76f2e; sessionIsLastTouch=false; traceInfo=%7B%22expInfo%22%3A%22%22%2C%22requestId%22%3A%22971905640a641b9c6289e763a5631761%22%7D; _ga_FQ75P4PXDH=GS2.1.s1782245294$o21$g1$t1782252662$j47$l0$h0;';

        return Http::withHeaders($this->headers)
            ->baseUrl($this->host)
            ->timeout(20)
            ->connectTimeout(10)
//            ->withHeaders(['Transfer-Encoding' => ''])
            ->withOptions([
//                'allow_redirects' => false,
                'curl' => [

                CURLOPT_POST => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_CIPHER_LIST => 'TLS_AES_128_GCM_SHA256:TLS_AES_256_GCM_SHA384:TLS_CHACHA20_POLY1305_SHA256:' .
                    'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:' .
                    'ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:' .
                    'ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:' .
                    'ECDHE-RSA-AES128-SHA:ECDHE-RSA-AES256-SHA:AES128-GCM-SHA256:AES256-GCM-SHA384',
                CURLOPT_SSL_EC_CURVES => 'X25519:P-256:P-384',
                CURLOPT_SSL_ENABLE_ALPN => true,
                CURLOPT_STDERR => fopen('php://stderr', 'w'),
                CURLOPT_VERBOSE => true,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ]]);
    }

    public function get(string $path, array $params = []): array
    {
        try {
            $res = $this->api()->get($path, $params)->throw();
            return [
                'status' => 'success',
                'headers' => $res->headers(),
                'http_code' => $res->status(),
                'data' => $res->json()
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return [
                'status' => 'connection_error',
                'http_code' => 0,
                'message' => 'Gagal terhubung ke server (Timeout/Down): ' . $e->getMessage()
            ];
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return [
                'status' => 'http_error',
                'http_code' => $e->response->status(),
                'data' => $e->response->json() ?: $e->response->body()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'system_error',
                'http_code' => 500,
                'message' => 'Internal logic error: ' . $e->getMessage()
            ];
        }
    }
    private function rawCurl(string $url, string $method = 'GET', ?string $body = null): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_STDERR, fopen('php://stderr', 'w'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST,
            'TLS_AES_128_GCM_SHA256:TLS_AES_256_GCM_SHA384:TLS_CHACHA20_POLY1305_SHA256:' .
            'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:' .
            'ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:' .
            'ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:' .
            'ECDHE-RSA-AES128-SHA:ECDHE-RSA-AES256-SHA:AES128-GCM-SHA256:AES256-GCM-SHA384'
        );
        curl_setopt($ch, CURLOPT_SSL_EC_CURVES, 'X25519:P-256:P-384');
        curl_setopt($ch, CURLOPT_SSL_ENABLE_ALPN, true);

        // Cookie via CURLOPT_COOKIE (bukan di header)
        $COOKIE_STRING = 'device_id=9abd1401-74f1-43ec-84e7-8e2a2f468524; _gcl_au=1.1.1667493223.1781169157; sessionFirstTouchPath=/id/en; ab180ClientId=124b47e9-48c9-4692-b6cc-08375f072cfd; _ga=GA1.1.1442324188.1781169167; pastJobSearchConditions=%5B%7B%22keyword%22%3A%22System%20Engineer%22%2C%22country%22%3A%22ID%22%2C%22locationName%22%3A%22All%20Cities%2FProvinces%22%2C%22lowestLocationLevel%22%3A%221%22%7D%2C%7B%22keyword%22%3A%22IT%20Trainer%22%2C%22country%22%3A%22ID%22%2C%22locationName%22%3A%22Jabodetabek%22%2C%22locationId%22%3A%22JABODETABEK%22%7D%2C%7B%22keyword%22%3A%22IT%20Trainer%22%2C%22country%22%3A%22ID%22%2C%22locationName%22%3A%22All%20Cities%2FProvinces%22%2C%22lowestLocationLevel%22%3A%221%22%7D%5D; session=Fe26.2**2dc26d0c7d4cb49468fbf992d3433c23ff7d8e6864eef2a848baad245f0d0ae6*gPfV-fgmmyuRulGSmSomGg*lzlM09X9HQtusTtC8JdsfA02ELFBrt52HAzCKmGChzLwE5pWMDaR9Q8E-YidfEVB**bf7c9e799e9187d637b71a44a8d29290d1c3905c2a72c89cbf4ff1f12286b0a7*sEyFvq7Rs9hpc6pQj7ONY81I9cNiFvlwj5p-DsJuAD0; _ga_WMM977BJLD=GS2.1.s1782210180$o2$g0$t1782210184$j56$l0$h0; g_state={"i_l":0,"i_ll":1782210186816,"i_b":"UFHuhziTX+wPne75/DGX+McXn7i4YCD5cmdOg1PV85Q","i_e":{"enable_itp_optimization":24},"i_et":1782210186816}; ridge_migration_metadata__taplokerbyglints=%7B%22version%22%3A%221.11.12%22%7D; sessionLastTouchPath=/id/opportunities/jobs/recommended; glints_tracking_id=23e5b13c-07f4-44ef-8672-03e6d8e76f2e; sessionIsLastTouch=false; traceInfo=%7B%22expInfo%22%3A%22%22%2C%22requestId%22%3A%22971905640a641b9c6289e763a5631761%22%7D; _ga_FQ75P4PXDH=GS2.1.s1782245294$o21$g1$t1782252662$j47$l0$h0;';

        curl_setopt($ch, CURLOPT_COOKIE, $COOKIE_STRING);

        // Headers tanpa Cookie
        $headers = [];
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === 'cookie') continue;
            $headers[] = "$key: $value";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) return ['status' => 'connection_error', 'http_code' => 0, 'message' => $error];

        $decoded = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300) {
            return ['status' => 'success', 'http_code' => $httpCode, 'data' => $decoded];
        }
        return ['status' => 'http_error', 'http_code' => $httpCode, 'data' => $decoded];
    }

    public function getRaw(string $url, array $params = []): array
    {
        if (!empty($params)) $url .= '?' . http_build_query($params);
        return $this->rawCurl($url, 'GET');
    }

    public function postRaw(string $url, string $body): array
    {
        return $this->rawCurl($url, 'POST', $body);
    }
    public function post(string $path, array $data = []): array
    {
        try {
            $res = $this->api()->post($path, $data)->throw();
            return [
                'status' => 'success',
                'headers' => $res->headers(),
                'http_code' => $res->status(),
                'data' => $res->json()
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return [
                'status' => 'connection_error',
                'http_code' => 0,
                'message' => 'Gagal terhubung ke server (Timeout/Down): ' . $e->getMessage()
            ];
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return [
                'status' => 'http_error',
                'http_code' => $e->response->status(),
                'data' => $e->response->json() ?: $e->response->body()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'system_error',
                'http_code' => 500,
                'message' => 'Internal logic error: ' . $e->getMessage()
            ];
        }
    }

    public function graphql(string $operation, array $variables = [], array $options = []): array {}
}
