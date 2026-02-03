<?php



namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use App\Exceptions\UnknownOperation;
use App\Support\QueryHelper;

class JobstreetAPI extends api
{
    public const provider = 'jobstreet';
    protected string $host = 'https://id.jobstreet.com';
    protected ?string $token;
    protected ?string $cookie;
    protected array $headers;

    public function __construct(
        ?string $token = null,
        ?string $cookie = null
    ) {
        $this->token = $token;
        $this->cookie = $cookie;
        $this->sessionId = '';
        $this->userAgent = config('compass.user_agent');
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->token,
            'X-Seek-Site' => 'Chalice',
            'X-Seek-Ec-Visitorid' => $this->sessionId,
            'X-Seek-Ec-Sessionid' => $this->sessionId,
            'Referer' => $this->host . '/',
            'User-Agent' => $this->userAgent,
            'Cookie' => $this->cookie
        ];
    }

    public function graphql(
        string $operation,
        array $variables = [],
        array $options = []
    ): array {
        try {
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
            $response = $this->api()->post($this->host . '/graphql', $payload);

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
        } catch (RequestException $e) {
            return [
            'ok' => false,
            'type' => 'request_exception',
            'message' => $e->getMessage(),
            ];
        } catch(UnknownOperation $e) {
            return [
            'ok' => false,
            'type' => 'unknown_operation',
            'message' => $e->getMessage(),
            ];
        } catch(\Exception $e) {
            return [
            'ok' => false,
            'type' => 'general_exception',
            'message' => $e->getMessage(),
            ];
        }
    }
}