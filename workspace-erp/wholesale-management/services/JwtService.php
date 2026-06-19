<?php
namespace WholesaleErp\Services;

if (!defined('ABSPATH')) exit;

class JwtService {
    private static $secret = 'wholesale_erp_jwt_secret_key_2024';
    private static $expiry = 86400 * 7; // 7 days

    public static function generate(array $payload): string {
        $header  = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload['exp'] = time() + self::$expiry;
        $payload['iat'] = time();
        $b64Header  = self::b64url_encode($header);
        $b64Payload = self::b64url_encode(json_encode($payload));
        $signature  = hash_hmac('sha256', $b64Header . '.' . $b64Payload, self::$secret, true);
        $b64Sig     = self::b64url_encode($signature);
        return $b64Header . '.' . $b64Payload . '.' . $b64Sig;
    }

    public static function verify(string $token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;
        $signature = hash_hmac('sha256', $parts[0] . '.' . $parts[1], self::$secret, true);
        $b64Sig    = self::b64url_encode($signature);
        if (!hash_equals($b64Sig, $parts[2])) return false;
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
        if (empty($payload) || (isset($payload['exp']) && $payload['exp'] < time())) return false;
        return $payload;
    }

    private static function b64url_encode(string $data): string {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
