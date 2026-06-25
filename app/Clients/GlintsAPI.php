<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use App\Support\QueryHelper;
use Illuminate\Support\Facades\Log;
use function Pest\Laravel\json;

class GlintsAPI extends api
{
    public const provider = 'glints';
    public string $host = 'https://glints.com/api';
    protected ?string $token;
    protected ?string $cookie;
    public array $headers;

    public function __construct(
        ?string $token = null,
        ?string $cookie = null,
    ) {
        $this->token = $token ?: '';
        $this->cookie = $cookie ?: '';
        $this->sessionId = '';

        $this->headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
            'Accept' => 'application/json, text/plain, */*',
            'Content-Type' => 'application/json;charset=UTF-8',
            'DNT' => '1',
            'Traceparent' => '00-889f8a1dbdbed28e2a7e4c3eb278a9a2-9fc3d53fed4eef13-01',
            'Origin' => 'https://glints.com',
            'Accept-Language' => 'id',
            'Referer' => 'https://glints.com/id/opportunities/jobs/engineering/db5cc606-3458-4591-9369-bde9b020f0ae/apply?utm_referrer=fyp&traceInfo=d06f88314420a59edd2d7d9a0e6501c2',
//            'Authorization' => 'Bearer ' . $this->token,
            'Sec-CH-UA' => '"Not(A:Brand";v="8", "Chromium";v="144", "Google Chrome";v="144"',
            'Sec-CH-UA-Mobile' => '?0',
            'Sec-CH-UA-Platform' => '"Windows"',
        ];
    }

//    public function graphql(
//        string $operation,
//        array $variables = [],
//        array $options = []
//    ): array {
//        $options = array_merge([
//            'headers' => false,
//            'cookies' => false,
//            'debug' => false
//        ], $options);
//        $payload = [
//            "operationName" => $operation,
//            "variables" => QueryHelper::buildGraphQLVariables($this, $operation, $variables) ?? new \stdClass(),
//            "query" => QueryHelper::loadGraphQLQuery($this, $operation)
//        ];
//
//        try {
//            if(isset($options['isv2']) && $options['isv2']  == true) {
//                $url = $this->host . '/v2-alc/graphql?op='. $operation;
//            } else {
//                $url = $this->host . '/graphql?op='. $operation;
//            }
//            $response = $this->post($url, $payload);
//            Log::info(json_encode($response));
//        } catch (RequestException $e) {
//            return [
//                'ok' => false,
//                'type' => 'http_error',
//                'http_code' => $e->response ? $e->response->status() : 500,
//                'data' => $e->getMessage(),
//            ];
//        }
//        $decoded = $response->json() ?? null;
//
//        $out = [];
//
//        if (!$response->successful()) {
//            $out['ok'] = false;
//            $out['type'] = 'http_error';
//            $out['http_code'] = $response->status();
//            $out['data'] = $decoded ?? $response->body();
//        } elseif (isset($decoded['errors'])) {
//            $out['ok'] = false;
//            $out['type'] = 'graphql_error';
//            $out['http_code'] = 200;
//            $out['errors'] = $decoded['errors'];
//        } else {
//            $out['ok'] = true;
//            $out['http_code'] = 200;
//            $out['data'] = $decoded['data'];
//        }
//
//        // Options
//
//        if ($options['headers']) {
//            $out['headers'] = $response->headers();
//        }
//
//        if ($options['debug']) {
//            $out['debug'] = [
//                'request' => [
//                    'url' => $this->host . '/graphql',
//                    'body' => $payload,
//                    'headers' => $this->headers,
//                ],
//                'response' => [
//                    'status' => $response->status(),
//                    'body' => $response->body(),
//                    'headers' => $response->headers(),
//                ],
//            ];
//        }
//
//        if ($options['cookies']) {
//            $out['cookies'] = $response->cookies();
//        }
//
//        return $out;
//    }
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

        if (isset($options['isv2']) && $options['isv2'] == true) {
            $url = $this->host . '/v2-alc/graphql?op=' . $operation;
        } else {
            $url = $this->host . '/graphql?op=' . $operation;
        }

        $response = $this->postRaw($url, json_encode($payload));
        Log::info(json_encode($response));

        $decoded = $response['data'] ?? null;
        $out = [];

        if ($response['status'] === 'connection_error') {
            return [
                'ok' => false,
                'type' => 'connection_error',
                'http_code' => 0,
                'data' => $response['message'] ?? 'Connection failed',
            ];
        }

        if ($response['status'] === 'http_error') {
            $out['ok'] = false;
            $out['type'] = 'http_error';
            $out['http_code'] = $response['http_code'];
            $out['data'] = $decoded;
        } elseif (isset($decoded['errors'])) {
            $out['ok'] = false;
            $out['type'] = 'graphql_error';
            $out['http_code'] = 200;
            $out['errors'] = $decoded['errors'];
        } else {
            $out['ok'] = true;
            $out['http_code'] = 200;
            $out['data'] = $decoded['data'] ?? $decoded;
        }

        if ($options['debug']) {
            $out['debug'] = [
                'request' => [
                    'url' => $url,
                    'body' => $payload,
                    'headers' => $this->headers,
                ],
                'response' => [
                    'status' => $response['http_code'],
                    'body' => $response['data'],
                ],
            ];
        }

        return $out;
    }
}
