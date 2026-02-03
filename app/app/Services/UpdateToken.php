<?php

namespace App\Services\Jobstreet;


use App\Applications\Contracts\TokenProvider;
use App\Applications\Contracts\SessionProvider;
use App\Clients\SessionManager;

class UpdateToken
{
    protected SessionManager $session;

    public function __construct(SessionManager $session, TokenProvider $tokenProvider, SessionProvider $sessionProvider)
    {
        $this->session = $session;
        $this->tokenProvider = $tokenProvider;
        $this->sessionProvider = $sessionProvider;
    }

    public function update_token(): array
    {
        $response = $this->session->get_token();
        
        if ($response['status'] !== 'success' || !isset($response['data']['access_token'])) {
            return [
                'status' => 'error',
                'message' => 'Failed to refresh token',
                'data' => $response['data'] ?? null
            ];
        }
        
        $stored = $this->tokenProvider->storeToken($response['data']);
        return [
            'status' => $stored ? 'success' : 'error',
            'message' => $stored ? 'Token updated successfully' : 'Failed to store token',
            'data' => $response['data'] ?? null
        ];
    }
    
}
