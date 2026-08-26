<?php
declare(strict_types=1);

function google_auth_url(): string
{
    $_SESSION['google_oauth_state'] = bin2hex(random_bytes(16));
    $params = [
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'access_type' => 'online',
        'prompt' => 'select_account',
        'state' => $_SESSION['google_oauth_state'],
    ];
    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

function google_fetch(string $url, array $postFields = []): array
{
    $options = [
        'http' => [
            'method' => $postFields ? 'POST' : 'GET',
            'header' => $postFields ? "Content-Type: application/x-www-form-urlencoded\r\n" : '',
            'content' => $postFields ? http_build_query($postFields) : null,
            'ignore_errors' => true,
            'timeout' => 10,
        ],
    ];
    $response = @file_get_contents($url, false, stream_context_create($options));
    $data = $response === false ? null : json_decode($response, true);
    if (!is_array($data)) {
        throw new RuntimeException('Could not reach Google. Please try again.');
    }
    return $data;
}

// Exchanges the OAuth code for the signed-in Google profile (sub, email, name).
function google_handle_callback(): array
{
    $code = (string)($_GET['code'] ?? '');
    $state = (string)($_GET['state'] ?? '');
    $expectedState = (string)($_SESSION['google_oauth_state'] ?? '');
    unset($_SESSION['google_oauth_state']);

    if (!$code || !$state || !$expectedState || !hash_equals($expectedState, $state)) {
        throw new RuntimeException('Google sign-in failed. Please try again.');
    }

    $token = google_fetch('https://oauth2.googleapis.com/token', [
        'code' => $code,
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code',
    ]);
    if (empty($token['access_token'])) {
        throw new RuntimeException('Google sign-in failed. Please try again.');
    }

    $profile = google_fetch('https://openidconnect.googleapis.com/v1/userinfo?access_token=' . urlencode((string)$token['access_token']));
    if (empty($profile['sub']) || empty($profile['email'])) {
        throw new RuntimeException('Google sign-in failed. Please try again.');
    }

    return $profile;
}
