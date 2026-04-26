<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Exception;

class SecureRequest extends Controller
{
    const PROVIDER_TEST = 1;
    const PROVIDER_LIVE = 2;

    private string $publicCertPath;
    private string $privateCertPath;
    private int $provider;
    private string $baseUrl;

    public function __construct()
    {
        $this->provider = $this->resolveProvider();
        $this->setCertificatePaths();

        $this->baseUrl = rtrim(
            $this->provider === self::PROVIDER_LIVE
                ? env('API_LIVE_BASE_URL')
                : env('API_TEST_BASE_URL'),
            '/'
        );
    }

    private function resolveProvider(): int
    {
        $tp = config('globals.global_api_tp');

        if (Session::get('global_api_tp')) {
            $tp = Session::get('global_api_tp');
        }

        $did = request()->get('did', 0);
        if ($did > 0) {
            $tp = (int) $did;
        }

        return (int) $tp;
    }

    private function setCertificatePaths(): void
    {
        switch ($this->provider) {

            case self::PROVIDER_LIVE:
                $this->publicCertPath  = base_path(env('API_LIVE_CERT'));
                $this->privateCertPath = base_path(env('API_LIVE_KEY'));
                break;

            case self::PROVIDER_TEST:
            default:
                $this->publicCertPath  = base_path(env('API_TEST_CERT'));
                $this->privateCertPath = base_path(env('API_TEST_KEY'));
                break;
        }

        if (
            empty($this->publicCertPath) ||
            empty($this->privateCertPath) ||
            !file_exists($this->publicCertPath) ||
            !file_exists($this->privateCertPath)
        ) {
            throw new Exception('SSL certificate files not found.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS
    |--------------------------------------------------------------------------
    */

    public function get(string $endpoint, array $params = [], bool $decodeJson = true)
    {
        $url = $this->buildUrl($endpoint, $params);

        // echo $url;
        // exit;

        return $this->sendRequest('GET', $url, [], false, $decodeJson);
    }

    public function post(
        string $endpoint,
        array $data = [],
        bool $sendJson = false,
        bool $decodeJson = true
    ) {
        $url = $this->buildUrl($endpoint);
        return $this->sendRequest('POST', $url, $data, $sendJson, $decodeJson);
    }

    /*
    |--------------------------------------------------------------------------
    | INTERNAL HELPERS
    |--------------------------------------------------------------------------
    */

    private function buildUrl(string $endpoint, array $params = []): string
    {
        $endpoint = '/' . ltrim($endpoint, '/');
        $url = $this->baseUrl . $endpoint;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    private function sendRequest(
        string $method,
        string $url,
        array $data = [],
        bool $sendJson = false,
        bool $decodeJson = true
    ) {
        $headers = [
            'Accept: application/json',
        ];

        return $this->rawCurlRequest(
            $method,
            $url,
            $data,
            $sendJson,
            $headers,
            $decodeJson
        );
    }

    private function rawCurlRequest(
        string $method,
        string $url,
        array $data = [],
        bool $sendJson = false,
        array $headers = [],
        bool $decodeJson = true
    ) {

        $ch = curl_init();

        if ($sendJson) {
            $headers[] = 'Content-Type: application/json';
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSLCERT        => $this->publicCertPath,
            CURLOPT_SSLKEY         => $this->privateCertPath,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                $sendJson ? json_encode($data) : http_build_query($data)
            );
        }

        $body = curl_exec($ch);

        if ($body === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Curl transport error: {$error}");
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // Decode safely
        $decoded = null;
        if ($decodeJson && $body !== '') {
            $decoded = json_decode($body, true);
        }

        // Handle non-2xx responses properly
        if ($httpCode < 200 || $httpCode >= 300) {

            Log::error('API Error', [
                'url' => $url,
                'status' => $httpCode,
                'response' => $body
            ]);

            $errorMessage = $decoded['errors'][0]['description']
                ?? $decoded['message']
                ?? $body
                ?? 'Unknown API error';

            throw new Exception(
                "API {$httpCode}: {$errorMessage}"
            );
        }

        return $decodeJson ? $decoded : $body;
    }
}
