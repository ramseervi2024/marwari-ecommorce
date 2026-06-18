<?php
namespace GymErpApi\Services;
class JwtService {
    private static $secret = 'gym_erp_secret_key_12345';
    public static function generate(array $payload): string {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload['exp'] = time() + (86400 * 7); // 7 days
        $b64Header = str_replace(['+','/','='], ['-','_',''], base64_encode($header));
        $b64Payload = str_replace(['+','/','='], ['-','_',''], base64_encode(json_encode($payload)));
        $signature = hash_hmac('sha256', $b64Header . "." . $b64Payload, self::$secret, true);
        $b64Sig = str_replace(['+','/','='], ['-','_',''], base64_encode($signature));
        return $b64Header . "." . $b64Payload . "." . $b64Sig;
    }
    public static function verify(string $token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;
        $signature = hash_hmac('sha256', $parts[0] . "." . $parts[1], self::$secret, true);
        $b64Sig = str_replace(['+','/','='], ['-','_',''], base64_encode($signature));
        if (!hash_equals($b64Sig, $parts[2])) return false;
        $payload = json_decode(base64_decode(str_replace(['-','_'], ['+','/'], $parts[1])), true);
        if (isset($payload['exp']) && $payload['exp'] < time()) return false;
        return $payload;
    }
}
