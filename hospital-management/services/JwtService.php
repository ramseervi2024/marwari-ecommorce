<?php
namespace HospitalManagementApi\Services;

class JwtService {
    
    /**
     * Generate a new JWT token.
     */
    public static function generateToken(array $payload, int $expiry = 86400): string {
        $secret = get_option('hospital_management_jwt_secret');
        if (empty($secret)) {
            $secret = bin2hex(random_bytes(32));
            update_option('hospital_management_jwt_secret', $secret);
        }
        
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        
        // Add expiration claim
        $payload['exp'] = time() + $expiry;
        $payload['iat'] = time();

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Validate and decode a JWT token.
     */
    public static function validateToken(string $token): ?array {
        $secret = get_option('hospital_management_jwt_secret');
        if (empty($secret)) {
            return null;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        list($header_encoded, $payload_encoded, $signature_encoded) = $parts;

        // Verify Signature
        $signature_raw = self::base64UrlDecode($signature_encoded);
        $expected_signature = hash_hmac('sha256', $header_encoded . "." . $payload_encoded, $secret, true);

        if (!hash_equals($signature_raw, $expected_signature)) {
            return null;
        }

        // Decode Payload
        $payload_json = self::base64UrlDecode($payload_encoded);
        $payload = json_decode($payload_json, true);

        if (!$payload) {
            return null;
        }

        // Check Expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null; // Expired
        }

        return $payload;
    }

    /**
     * Base64URL Encoder Helper.
     */
    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64URL Decoder Helper.
     */
    private static function base64UrlDecode(string $data): string {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
