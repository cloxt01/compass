<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use App\Support\QueryHelper;
use Illuminate\Support\Facades\Log;

class GlintsAPI extends api
{
    public const provider = 'glints';
    protected string $host = 'https://glints.com/api';
    protected ?string $token;
    protected ?string $cookie;
    protected array $headers;

    public function __construct(
        ?string $token = null,
        ?string $cookie = null,
    ) {
        $this->token = $token ?: '';
        $this->cookie = $cookie ?: '';
        $this->sessionId = '';
        $this->headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',
            'Accept' => '*/*',
            'Content-Type' => 'application/json',
            'Cookie' => $this->cookie,
            'DNT' => '1',
            'Traceparent' => '00-2334d811047b919f3a4ac1f3fb1accf4-904abac004e541f1-01',
            'Origin' => 'https://glints.com',
            'Referer' => 'https://glints.com/',
            'Authorization' => 'Bearer ' . $this->token,
            'Sec-CH-UA' => '"Not(A:Brand";v="8", "Chromium";v="144", "Google Chrome";v="144"',
            'Sec-CH-UA-Mobile' => '?0',
            'Sec-CH-UA-Platform' => '"Windows"',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
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
            'debug' => false
        ], $options);
        $payload = [
            "operationName" => $operation,
            "variables" => QueryHelper::buildGraphQLVariables($this, $operation, $variables) ?? new \stdClass(),
            "query" => QueryHelper::loadGraphQLQuery($this, $operation)
        ];
        print_r($this->host . '/graphql?op='. $operation );

        try {
            if(isset($options['isv2']) && $options['isv2']  == true) {
                $url = $this->host . '/v2-alc/graphql?op='. $operation;
            } else {
                $url = $this->host . '/graphql?op='. $operation;
            }
            $response = $this->api()->post($url, $payload);
            Log::info(json_encode($response));
        } catch (RequestException $e) {
            return [
                'ok' => false,
                'type' => 'http_error',
                'http_code' => $e->response ? $e->response->status() : 500,
                'data' => $e->getMessage(),
            ];
        }
        $decoded = $response->json() ?? null;

        $out = [];

        if (!$response->successful()) {
            $out['ok'] = false;
            $out['type'] = 'http_error';
            $out['http_code'] = $response->status();
            $out['data'] = $decoded ?? $response->body();
        } elseif (isset($decoded['errors'])) {
            $out['ok'] = false;
            $out['type'] = 'graphql_error';
            $out['http_code'] = 200;
            $out['errors'] = $decoded['errors'];
        } else {
            $out['ok'] = true;
            $out['http_code'] = 200;
            $out['data'] = $decoded['data'];
        }

        // Options

        if ($options['headers']) {
            $out['headers'] = $response->headers();
        }

        if ($options['debug']) {
            $out['debug'] = [
                'request' => [
                    'url' => $this->host . '/graphql',
                    'body' => $payload,
                    'headers' => $this->headers,
                ],
                'response' => [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers(),
                ],
            ];
        }

        if ($options['cookies']) {
            $out['cookies'] = $response->cookies();
        }

        return $out;
    }


}
