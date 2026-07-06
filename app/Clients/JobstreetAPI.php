<?php

namespace App\Clients;

use App\Exceptions\UnknownOperation;
use Illuminate\Support\Facades\Log;
use App\Support\QueryHelper;

class JobstreetAPI extends api
{
    public const provider = 'jobstreet';
    protected string $host = 'https://id.jobstreet.com';
    protected ?string $token;
    protected ?string $cookie;
    protected array $headers;
    protected string $userAgent;

    public function __construct(?string $token = null, ?string $cookie = null)
    {
        // Parent constructor sets up token, cookie and empty base headers
        parent::__construct($token, $cookie);

        // Override host and store token/cookie locally
        $this->host    = 'https://id.jobstreet.com';
        $this->token   = $token;
        $this->cookie  = $cookie;
        $this->sessionId = '';

        // Custom user agent (kept for compatibility; PHP‑Impersonate may override it)
        $this->userAgent = config('compass.user_agent');

        // Override parent headers completely with Jobstreet‑specific ones
        $this->headers = [
            'Accept'              => 'application/json',
            'Content-Type'        => 'application/json',
            'Authorization'       => 'Bearer ' . $this->token,
            'X-Seek-Site'         => 'Chalice',
            'X-Seek-Ec-Visitorid' => $this->sessionId,
            'X-Seek-Ec-Sessionid' => $this->sessionId,
            'Referer'             => $this->host . '/',
            'User-Agent'          => $this->userAgent,
            'Cookie'              => $this->cookie,
        ];
    }

    public function graphql(
        string $operation,
        array $variables = [],
        array $options = []
    ): array {
        $options = array_merge([
            'headers' => false,
            'cookies' => false,
            'debug'   => false,
        ], $options);

        // Build the GraphQL payload
        $payload = [
            "operationName" => $operation,
            "variables"     => QueryHelper::buildGraphQLVariables($this, $operation, $variables) ?? new \stdClass(),
            "query"         => QueryHelper::loadGraphQLQuery($this, $operation),
        ];

        if ($operation === 'jobDetailsWithPersonalised') {
            Log::info(json_encode($payload));
        }

        // Call parent’s post() with a relative path – the parent will build the full URL
        $response = $this->post('/graphql', $payload);

        $out = [];

        // Match the original switch behaviour exactly
        switch ($response['status']) {
            case 'success':
                $out['ok']        = true;
                $out['http_code'] = $response['http_code'];
                $out['data']      = $response['data'];
                break;

            case 'system_error':
            case 'connection_error':
                $out['ok']        = false;
                $out['type']      = $response['status'];
                $out['http_code'] = $response['http_code'];
                $out['message']   = $response['message'];
                break;

            case 'http_error':
                $out['ok']        = false;
                $out['type']      = 'http_error';
                $out['http_code'] = $response['http_code'];
                $out['data']      = $response['data'];
                break;

            default:
                $out['ok']      = false;
                $out['type']    = 'unknown';
                $out['message'] = 'Terjadi kesalahan yang tidak terdefinisi';
                break;
        }

        if ($options['debug']) {
            $out['debug'] = [
                'request' => [
                    'url'     => $this->host . '/graphql',
                    'body'    => $payload,
                    'headers' => $this->headers,
                ],
                'response' => [
                    'status'    => $response['status'] ?? null,
                    'http_code' => $response['http_code'] ?? null,
                    'body'      => $response['data'] ?? $response['message'] ?? null,
                ],
            ];
        }

        if ($options['cookies']) {
            $out['cookies'] = null;
        }
 
        Log::info("GraphQL operation '$operation' executed with status: " . ($out['ok'] ? 'success' : 'failure') . ", type: " . ($out['type'] ?? 'none') . ", http_code: " . ($out['http_code'] ?? 'none'));

        return $out;

        // Note: The original try‑catch blocks for RequestException, UnknownOperation, etc.
        // are no longer needed because parent::post() already handles all exceptions internally
        // and returns a standardized array. Those exceptions will never bubble up.
    }
}
