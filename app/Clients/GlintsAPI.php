<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use App\Support\QueryHelper;

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
        $this->token = $token;
        $this->cookie = $cookie;
        $this->sessionId = '';
        $this->headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',
            'Accept' => '*/*',
            'Content-Type' => 'application/json',
            'Cookie' => $cookie,
            'DNT' => '1',
            'Traceparent' => '00-2334d811047b919f3a4ac1f3fb1accf4-904abac004e541f1-01',
            'Origin' => 'https://glints.com',
            'Referer' => 'https://glints.com/',
            'Authorization' => 'Bearer ' . 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6InRHZjM0TU1GNUpmdUFaX1R4RjhBbiJ9.eyJodHRwOi8vc2Vlay9jbGFpbXMvaWRlbnRpdHlfaWQiOiJhdXRoMHw2OTBlZDRiN2FkNTkxNzEyNjBmMGFiMDAiLCJodHRwOi8vc2Vlay9jbGFpbXMvY291bnRyeSI6IklEIiwiaHR0cDovL3NlZWsvY2xhaW1zL2JyYW5kIjoiam9ic3RyZWV0IiwiaHR0cDovL3NlZWsvY2xhaW1zL2V4cGVyaWVuY2UiOiJjYW5kaWRhdGUiLCJodHRwOi8vc2Vlay9jbGFpbXMvdXNlcl9pZCI6IjU5MDE0NTk4MCIsImlzcyI6Imh0dHBzOi8vbG9naW4uc2Vlay5jb20vIiwic3ViIjoiYXV0aDB8NjkwZWQ0YjdhZDU5MTcxMjYwZjBhYjAwIiwiYXVkIjpbImh0dHBzOi8vc2Vlay9hcGkvY2FuZGlkYXRlIiwiaHR0cHM6Ly9zZWVrYW56Lm9ubGluZWF1dGgucHJvZC5vdXRmcmEueHl6L3VzZXJpbmZvIl0sImlhdCI6MTc2MjU4NTAxMywiZXhwIjoxNzYyNTg4NjEzLCJzY29wZSI6Im9wZW5pZCBwcm9maWxlIGVtYWlsIG9mZmxpbmVfYWNjZXNzIiwiYXpwIjoiOE9WaHB2dGFJOW41UVZFUUszWDV5ZnNtQ2JyckxYZkUifQ.jp1teJLaBx95AhYdZHI44vmEXOC8uOGSlC5tnoHLWJeSEhhc9pNQfBEPZBb8n26dhgSvL-b8Kvgca-uO-XgbYdbsdWVyoKwJgT3-xKnkWjl6mNhhX53_TZGt27yZkJqv5WWvHQiBRolkzYrYcj37_eETlMQ-zz31ftisiQBFrfF-FmcLziZZPM2ch7uGsGYPfmZvivIRKet4l6bxRs7pe8qy4wE7HxZScYEgE_kpdbSyChC83g5_gdssbI1GGo2RVdfghYAaB2Wv8s5M8QvpgpvS8lqRDE1Xx61CTsnv6UxAAVx6xwQOSk8o-fhOpR2kGJ-TH16ccxKP2iCx1Ow6Tw',
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
        print_r($this->headers);
        print_r($payload);

        try {
            $response = $this->api()->post($this->host . '/graphql?op='. $operation , $payload);
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
