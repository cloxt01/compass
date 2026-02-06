<?php 

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use App\Support\QueryHelper;
use Illuminate\Http\Client\PendingRequest;

class api {
    protected ?string $token;
    protected ?string $cookie;
    protected array $headers;

    public function __construct(?string $token = null, ?string $cookie = null) {}

    private function api()
    {
        return Http::withHeaders($this->headers)
        ->baseUrl($this->host)
        ->acceptJson()
        ->timeout(20)
        ->connectTimeout(10)
        ->withOptions(['curl' => [
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_ENABLE_ALPN => false,
        CURLOPT_SSL_ENABLE_NPN => false,
    ]]);
    }

    public function get(string $path, array $params = []): array
    {
        try {
            $response = $this->api()->get($path, $params);

            if ($response->failed()) {
                return [
                    'url' => $this->host.$path,
                    'params' => $params,
                    'status' => 'error',
                    'http_code' => $response->status(),
                    'data' => $response->json() ?? $response->body()
                ];
            }

            return [
                'status' => 'success',
                'http_code' => $response->status(),
                'data' => $response->json()
            ];
        } catch (RequestException $e) {
            return [
                'status' => 'error',
                'http_code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function post(string $path, array $data = []): array
    {
        try {

            $response = $this->api()->post($path, $data);

            if ($response->failed()) {
                return [
                    'status' => 'error',
                    'http_code' => $response->status(),
                    'data' => $response->json() ?? $response->body()
                ];
            }

            return [
                'status' => 'success',
                'http_code' => $response->status(),
                'data' => $response->json()
            ];
        } catch (RequestException $e) {
            return [
                'status' => 'error',
                'http_code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function graphql(string $operation, array $variables = [], array $options = []): array {}
}