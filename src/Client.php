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
    public function activate(License $license, array $meta): ?string
    {
        return $this->sendRequest($license, $meta);
    }

    /**
     * Check the license with stored hash
     */
    public function check(License $license, array $meta): ?string
    {
        if (!$license->getHash()) {
            throw new \Exception("Hash is missing. Activation required first.");
        }

        return $this->sendRequest($license, $meta, $license->getHash());
    }

    /**
     * Internal request sender
     */
    private function sendRequest(License $license, array $meta, ?string $hash = null): ?string
    {
        $url = $this->apiUrl . '/activation/' 
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

        if (!isset($data['hash'])) {
            throw new \Exception('Invalid response from API');
        }

        $license->setHash($data['hash']);
        return $data['hash'];
    }
}
