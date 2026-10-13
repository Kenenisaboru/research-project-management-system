<?php

namespace App\Services;

/**
 * Class TotpService
 *
 * Implements RFC 6238 Time-Based One-Time Password (TOTP) and RFC 4648 Base32 encoding
 * without external library dependencies for institutional two-factor authentication.
 */
class TotpService
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a new cryptographically secure random Base32 secret key.
     *
     * @param  int  $length
     * @return string
     */
    public static function generateSecret(int $length = 16): string
    {
        $secret = '';
        $max = strlen(self::BASE32_CHARS) - 1;
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, $max)];
        }
        return $secret;
    }

    /**
     * Calculate the 6-digit TOTP code for a secret at a given timestamp.
     *
     * @param  string  $secret
     * @param  int|null  $timestamp
     * @return string
     */
    public static function getCode(string $secret, ?int $timestamp = null): string
    {
        $timestamp = $timestamp ?? time();
        $timeSlice = (int) floor($timestamp / 30);

        $secretKey = self::base32Decode($secret);
        $timeBytes = pack('N*', 0) . pack('N*', $timeSlice);

        $hash = hash_hmac('sha1', $timeBytes, $secretKey, true);
        $offset = ord(substr($hash, -1)) & 0x0F;

        $hashPart = substr($hash, $offset, 4);
        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a submitted TOTP code against the secret key (allows +/- 1 time step drift).
     *
     * @param  string  $secret
     * @param  string  $code
     * @param  int  $driftSteps
     * @return bool
     */
    public static function verify(string $secret, string $code, int $driftSteps = 1): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        $currentTime = time();
        for ($i = -$driftSteps; $i <= $driftSteps; $i++) {
            $calculated = self::getCode($secret, $currentTime + ($i * 30));
            if (hash_equals($calculated, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Alias for getCode() at current timestamp.
     *
     * @param  string  $secret
     * @return string
     */
     public static function getCurrentCode(string $secret): string
     {
         return self::getCode($secret);
     }

    /**
     * Alias for verify().
     *
     * @param  string  $secret
     * @param  string  $code
     * @param  int  $driftSteps
     * @return bool
     */
    public static function verifyCode(string $secret, string $code, int $driftSteps = 1): bool
    {
        return self::verify($secret, $code, $driftSteps);
    }

    /**
     * Generate an otpauth:// URI compatible with Google Authenticator, Microsoft Authenticator, Authy.
     *
     * @param  string  $accountName
     * @param  string  $secret
     * @param  string  $issuer
     * @return string
     */
    public static function getOtpAuthUrl(string $accountName, string $secret, string $issuer = 'Gambella University RPMS'): string
    {
        return 'otpauth://totp/' . rawurlencode($issuer) . ':' . rawurlencode($accountName)
            . '?secret=' . $secret
            . '&issuer=' . rawurlencode($issuer)
            . '&algorithm=SHA1&digits=6&period=30';
    }

    /**
     * Generate a set of 8 random single-use recovery codes.
     *
     * @param  int  $count
     * @param  int  $length
     * @return array<int, string>
     */
    public static function generateRecoveryCodes(int $count = 8, int $length = 10): array
    {
        $codes = [];
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $count; $i++) {
            $code = '';
            for ($j = 0; $j < $length; $j++) {
                $code .= $chars[random_int(0, $max)];
            }
            $codes[] = substr($code, 0, 5) . '-' . substr($code, 5, 5);
        }

        return $codes;
    }

    /**
     * Decode a Base32 string to binary data.
     *
     * @param  string  $b32
     * @return string
     */
    public static function base32Decode(string $b32): string
    {
        $b32 = strtoupper(preg_replace('/[^A-Z2-7]/i', '', $b32));
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0, $len = strlen($b32); $i < $len; $i++) {
            $val = strpos(self::BASE32_CHARS, $b32[$i]);
            if ($val === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }
}
