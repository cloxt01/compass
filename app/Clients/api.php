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

        return Http::withHeaders($this->headers)
            ->baseUrl($this->host)
            ->timeout(20)
            ->connectTimeout(10)
            ->withOptions([
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
//                CURLOPT_STDERR => fopen('php://stderr', 'w'),
//                CURLOPT_VERBOSE => true,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
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
