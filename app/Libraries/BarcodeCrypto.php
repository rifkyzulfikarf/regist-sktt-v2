<?php

namespace App\Libraries;

use Config\Encryption;

class BarcodeCrypto
{
    private const VERSION = 'v1';

    public function encryptParticipantNumber(string $participantNumber): string
    {
        $key   = $this->getKey();
        $iv    = random_bytes(16);
        $plain = trim($participantNumber);

        $ciphertext = openssl_encrypt($plain, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($ciphertext === false) {
            throw new \RuntimeException('Gagal mengenkripsi nomor peserta.');
        }

        $mac   = hash_hmac('sha256', $iv . $ciphertext, $key, true);
        $token = $this->base64UrlEncode($iv . $mac . $ciphertext);

        return self::VERSION . ':' . $token;
    }

    public function decryptPayload(string $payload): ?string
    {
        $payload = trim($payload);
        if ($payload === '') {
            return null;
        }

        if (! str_contains($payload, ':')) {
            return null;
        }

        [$version, $token] = explode(':', $payload, 2);
        if ($version !== self::VERSION || $token === '') {
            return null;
        }

        $blob = $this->base64UrlDecode($token);
        if ($blob === null || strlen($blob) <= 48) {
            return null;
        }

        $iv         = substr($blob, 0, 16);
        $mac        = substr($blob, 16, 32);
        $ciphertext = substr($blob, 48);

        $key = $this->getKey();
        $calculatedMac = hash_hmac('sha256', $iv . $ciphertext, $key, true);

        if (! hash_equals($mac, $calculatedMac)) {
            return null;
        }

        $plain = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($plain === false) {
            return null;
        }

        return trim($plain);
    }

    private function getKey(): string
    {
        $secret = env('barcode.secret', '');

        if ($secret === '') {
            /** @var Encryption $encryption */
            $encryption = config('Encryption');
            $secret = $encryption->key;
        }

        if ($secret === '') {
            $secret = 'please-change-barcode-secret';
        }

        return hash('sha256', $secret, true);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): ?string
    {
        $base64 = strtr($data, '-_', '+/');
        $padding = strlen($base64) % 4;

        if ($padding > 0) {
            $base64 .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($base64, true);

        return $decoded === false ? null : $decoded;
    }
}
