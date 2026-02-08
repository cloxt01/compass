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
            CURLOPT_SSL_ENABLE_NPN => false
        ]]);
    }

    public function get(string $path, array $params = []): array
    {
        try {
            $res = $this->api()->get($path, $params)->throw();
            return [
                'status' => 'success',
                'http_code' => $res->status(),
                'data' => $res->json()
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return [
                'status' => 'connection_error',
                'http_code' => 0,
                'message' => 'Gagal terhubung ke server (Timeout/Down)'
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
                'http_code' => $res->status(),
                'data' => $res->json()
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return [
                'status' => 'connection_error',
                'http_code' => 0,
                'message' => 'Gagal terhubung ke server (Timeout/Down)'
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