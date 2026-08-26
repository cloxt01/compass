<?php

namespace App\Clients;

use Illuminate\Support\Facades\Log;
use Raza\PHPImpersonate\PHPImpersonate;
use Raza\PHPImpersonate\Exception\RequestException;

class api
{
    protected string $host;
    protected ?string $token;
    protected ?string $cookie;
    protected array $headers;

    public function __construct(?string $token = null, ?string $cookie = null)
    {
        $this->token  = $token;
        $this->cookie = $cookie;
        $this->headers = [];
    }

    /**
     * Set the base URL for API requests.
     */
    public function setHost(string $host): self
    {
        $this->host = rtrim($host, '/');
        return $this;
    }

    /**
     * Create a configured PHPImpersonate client instance,
     * mimicking the original SSL/TLS and HTTP/2 settings.
     */
    protected function client(): PHPImpersonate
    {
        return new PHPImpersonate(
            browser: 'chrome136',
            timeout: 20,
            curlOptions: [
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_CONNECTTIMEOUT  => 10,
                CURLOPT_SSL_CIPHER_LIST =>
                    'TLS_AES_128_GCM_SHA256:TLS_AES_256_GCM_SHA384:TLS_CHACHA20_POLY1305_SHA256:' .
                    'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:' .
                    'ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:' .
                    'ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:' .
                    'ECDHE-RSA-AES128-SHA:ECDHE-RSA-AES256-SHA:AES128-GCM-SHA256:AES256-GCM-SHA384',
                CURLOPT_SSL_EC_CURVES   => 'X25519:P-256:P-384',
                CURLOPT_SSL_ENABLE_ALPN => true,
                CURLOPT_SSLVERSION      => CURL_SSLVERSION_TLSv1_2,
                CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_2_0,
            ]
        );
    }

    /**
     * Merge default headers with any extra headers.
     */
    protected function mergeHeaders(array $extra = []): array
    {
        return array_merge($this->headers, $extra);
    }

    /**
     * Normalize header keys to canonical HTTP form.
     * This ensures the library recognises Content-Type, Authorization, Cookie, etc.
     */
    protected function normalizeHeaders(array $headers): array
    {
        $canonicalMap = [
            'content-type'  => 'Content-Type',
            'authorization' => 'Authorization',
            'cookie'        => 'Cookie',
            'user-agent'    => 'User-Agent',
            'accept'        => 'Accept',
            'referer'       => 'Referer',
            // Add others as needed
        ];

        foreach ($headers as $key => $value) {
            $lower = strtolower($key);
            if (isset($canonicalMap[$lower]) && $key !== $canonicalMap[$lower]) {
                $headers[$canonicalMap[$lower]] = $value;
                unset($headers[$key]);
            }
        }
        return $headers;
    }

    /**
     * Build a full URL from a path and optional query parameters.
     */
    protected function url(string $path, array $params = []): string
    {
        $url = $this->host . '/' . ltrim($path, '/');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    public function get(string $path, array $params = []): array
    {
        try {
            $headers = $this->normalizeHeaders($this->mergeHeaders());

            $response = $this->client()->sendGet(
                $this->url($path, $params),
                $headers
            );

            if (!$response->isSuccess()) {
                $data = $response->body();
                try {
                    $data = $response->json();
                } catch (\JsonException $e) {
                }
                return [
                    'status'    => 'http_error',
                    'http_code' => $response->status(),
                    'data'      => $data,
                ];
            }

            return [
                'status'    => 'success',
                'headers'   => $response->headers(),
                'http_code' => $response->status(),
                'data'      => $response->json(),
            ];
        } catch (RequestException $e) {
            return [
                'status'    => 'connection_error',
                'http_code' => 0,
                'message'   => 'Gagal terhubung ke server (Timeout/Down): ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'status'    => 'system_error',
                'http_code' => 500,
                'message'   => 'Internal logic error: ' . $e->getMessage(),
            ];
        }
    }

    public function post(string $path, array $data = []): array
    {
        try {
            $headers = $this->normalizeHeaders($this->mergeHeaders());

            // Fallback Content-Type if not present
            if (!isset($headers['Content-Type'])) {
                $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            }

            $response = $this->client()->sendPost(
                $this->url($path),
                $data,
                $headers
            );

            if (!$response->isSuccess()) {
                $body = $response->body();
                try {
                    $body = $response->json();
                } catch (\JsonException $e) {
                }
                return [
                    'status'    => 'http_error',
                    'http_code' => $response->status(),
                    'data'      => $body,
                ];
            }

            return [
                'status'    => 'success',
                'headers'   => $response->headers(),
                'http_code' => $response->status(),
                'data'      => $response->json(),
            ];
        } catch (RequestException $e) {
            return [
                'status'    => 'connection_error',
                'http_code' => 0,
                'message'   => 'Gagal terhubung ke server (Timeout/Down): ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'status'    => 'system_error',
                'http_code' => 500,
                'message'   => 'Internal logic error: ' . $e->getMessage(),
            ];
        }
    }

    public function graphql(string $operation, array $variables = [], array $options = []): array
    {
        return [];
    }
}
