<?php

namespace Licenzo;

class Client
{
    private string $apiUrl;

    public function __construct(string $apiUrl)
    {
        $this->apiUrl = rtrim($apiUrl, '/');
    }

    /**
     * Activate a license
     */
    public function activate(License $license, array $meta): array
    {
        return $this->sendRequest('activation', $license, $meta);
    }

    /**
     * Check a license using stored hash
     */
    public function check(License $license, array $meta): array
    {
        if (!$license->getHash()) {
            throw new \Exception("Hash is missing. Activation required first.");
        }

        return $this->sendRequest('check', $license, $meta, $license->getHash());
    }

    /**
     * Deactivate a license
     */
    public function deactivate(License $license): array
    {
        if (!$license->getHash()) {
            throw new \Exception("Hash is missing. Activation required first.");
        }

        return $this->sendRequest('deactivation', $license, [], $license->getHash());
    }

    /**
     * Internal request sender
     */
    private function sendRequest(string $action, License $license, array $meta = [], ?string $hash = null): array
    {
        $url = $this->apiUrl . '/' . $action . '/'
            . urlencode($license->getLicense()) . '/'
            . urlencode($license->getProduct()) . '/'
            . urlencode($license->getVariation() ?? '');

        $payload = [
            'meta' => $meta,
            'hash' => $hash
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);

        $data = json_decode($response, true);

        if (!is_array($data)) {
            throw new \Exception('Invalid response from API');
        }

        if (isset($data['hash'])) {
            $license->setHash($data['hash']);
        }

        return $data;
    }
}
