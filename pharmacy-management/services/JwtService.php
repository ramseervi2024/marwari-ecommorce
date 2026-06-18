<?php
namespace PharmacyErpApi\Services;

if (!defined('ABSPATH')) exit;

class JwtService {
    private static function getSecret(): string {
        return defined('AUTH_KEY') ? AUTH_KEY : 'pharmacy_erp_jwt_secret_key_2024';
    }

    public static function generate(int $userId, string $role): string {
        $header  = self::b64url(json_encode(['alg'=>'HS256','typ'=>'JWT']));
        $payload = self::b64url(json_encode(['sub'=>$userId,'role'=>$role,'iat'=>time(),'exp'=>time()+86400*7]));
        $sig     = self::b64url(hash_hmac('sha256', "$header.$payload", self::getSecret(), true));
        return "$header.$payload.$sig";
    }

    public static function verify(string $token): ?array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        [$header, $payload, $sig] = $parts;
        $expected = self::b64url(hash_hmac('sha256', "$header.$payload", self::getSecret(), true));
        if (!hash_equals($expected, $sig)) return null;
        $data = json_decode(self::b64url_decode($payload), true);
        if (!$data || ($data['exp'] ?? 0) < time()) return null;
        return $data;
    }

    private static function b64url(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    private static function b64url_decode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
