<?php

namespace App\Clients;

use App\Support\QueryHelper;
use Illuminate\Support\Facades\Log;

class GlintsAPI extends api
{
    public const provider = 'glints';
    public string $host = 'https://glints.com/api';
    protected ?string $token;
    protected ?string $cookie;
    protected string $sessionId;
    public array $headers;

    public function __construct(?string $token = null, ?string $cookie = null)
    {
        // Call parent to set token, cookie, and base HTTP headers
        parent::__construct($token, $cookie);

        // Override host set by parent (if any) with Glints-specific host
        $this->setHost($this->host);

        // Store for later use
        $this->token   = $token ?: '';
        $this->cookie  = $cookie ?: '';
        $this->sessionId = '';

        // Custom headers (User-Agent is intentionally omitted – PHP-Impersonate sets it automatically)
        $this->headers = [
            'Cookie'           => $this->cookie,
            'Accept'           => 'application/json, text/plain, */*',
            'Content-Type'     => 'application/json;charset=UTF-8',
            'DNT'              => '1',
            'Traceparent'      => '00-889f8a1dbdbed28e2a7e4c3eb278a9a2-9fc3d53fed4eef13-01',
            'Origin'           => 'https://glints.com',
            'Accept-Language'  => 'id',
            'Referer'          => 'https://glints.com/',
            'Sec-CH-UA'        => '"Not(A:Brand";v="8", "Chromium";v="144", "Google Chrome";v="144"',
            'Sec-CH-UA-Mobile' => '?0',
            'Sec-CH-UA-Platform' => '"Windows"',
        ];
    }

    /**
     * Perform a GraphQL query/mutation.
     *
     * @param string $operation Operation name
     * @param array  $variables Variables
     * @param array  $options   'headers'|'cookies'|'debug'|'isv2'
     */
    public function graphql(string $operation, array $variables = [], array $options = []): array
    {
        $options = array_merge([
            'headers' => false,
            'cookies' => false,
            'debug'   => false,
            'isv2'    => false,
        ], $options);

        // Build GraphQL payload
        $payload = [
            "operationName" => $operation,
            "variables"     => QueryHelper::buildGraphQLVariables($this, $operation, $variables) ?? new \stdClass(),
            "query"         => QueryHelper::loadGraphQLQuery($this, $operation),
        ];

        Log::info("Glints GraphQL Payload: " . json_encode($payload));

        // Determine URL
        $url = $options['isv2']
            ? '/v2-alc/graphql?op=' . $operation
            : '/graphql?op=' . $operation;

        $response = $this->post($url, $payload);

        $decoded = $response['data'] ?? null;

        $out = [];

        if ($response['status'] === 'connection_error') {
            return [
                'ok'        => false,
                'type'      => 'connection_error',
                'http_code' => 0,
                'data'      => $response['message'] ?? 'Connection failed',
            ];
        }

        if ($response['status'] === 'http_error') {
            $out['ok']        = false;
            $out['type']      = 'http_error';
            $out['http_code'] = $response['http_code'];
            $out['data']      = $decoded;
        } elseif (isset($decoded['errors'])) {
            $out['ok']        = false;
            $out['type']      = 'graphql_error';
            $out['http_code'] = 200;
            $out['errors']    = $decoded['errors'];
        } else {
            $out['ok']        = true;
            $out['http_code'] = 200;
            $out['data']      = $decoded['data'] ?? $decoded;
        }

        if ($options['headers']) {
            $out['headers'] = $response['headers'] ?? [];
        }

        if ($options['cookies']) {
            $cookies = [];
            $setCookie = $response['headers']['Set-Cookie'] ?? $response['headers']['set-cookie'] ?? [];
            if (is_array($setCookie)) {
                foreach ($setCookie as $cookieString) {
                    // Simple parse: name=value until first semicolon
                    if (preg_match('/^([^=]+)=([^;]+)/', $cookieString, $m)) {
                        $cookies[] = [
                            'name'  => trim($m[1]),
                            'value' => trim($m[2]),
                        ];
                    }
                }
            }
            $out['cookies'] = $cookies;
        }

        // Debug info
        if ($options['debug']) {
            $out['debug'] = [
                'request' => [
                    'url'     => $this->host . $url,
                    'body'    => $payload,
                    'headers' => $this->headers,
                ],
                'response' => [
                    'status'  => $response['http_code'],
                    'body'    => $response['data'] ?? $response['data'],
                    'headers' => $response['headers'] ?? [],
                ],
            ];
        }

        return $out;
    }
}
