<?php



namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
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

    public function __construct(
        ?string $token = null,
        ?string $cookie = null
    ) {
        $this->token = $token;
        $this->cookie = $cookie;
        $this->sessionId = '';
        $this->userAgent = config('compass.user_agent');
        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
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
            $response = $this->post($this->host . '/graphql', $payload);


            $out = [];

            switch ($response['status']) {
                case 'success':
                    $out['ok'] = true;
                    $out['http_code'] = $response['http_code'];
                    $out['data'] = $response['data'];
                    break;

                case 'system_error':
                case 'connection_error':
                    $out['ok'] = false;
                    $out['type'] = $response['status'];
                    $out['http_code'] = $response['http_code'];
                    $out['message'] = $response['message'];
                    break;

                case 'http_error':
                    $out['ok'] = false;
                    $out['type'] = 'http_error';
                    $out['http_code'] = $response['http_code'];
                    $out['data'] = $response['data'];
                    break;

                default:
                    $out['ok'] = false;
                    $out['type'] = 'unknown';
                    $out['message'] = 'Terjadi kesalahan yang tidak terdefinisi';
                    break;
            }

            if ($options['debug']) {
                $out['debug'] = [
                    'request' => [
                        'url' => $this->host . '/graphql',
                        'body' => $payload,
                        'headers' => $this->headers,
                    ],
                    'response' => [
                        'status' => $response['status'] ?? null,
                        'http_code' => $response['http_code'] ?? null,
                        'body' => $response['data'] ?? $response['message'] ?? null,
                    ],
                ];
            }

            if ($options['cookies']) {
                $out['cookies'] = null;
            }
            Log::info("GraphQL operation '$operation' executed with status: " . ($out['ok'] ? 'success' : 'failure') . ", type: " . ($out['type'] ?? 'none') . ", http_code: " . ($out['http_code'] ?? 'none'));

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
            'type' => 'graphql_exception',
            'message' => $e->getMessage(),
            ];
        }
    }
}
