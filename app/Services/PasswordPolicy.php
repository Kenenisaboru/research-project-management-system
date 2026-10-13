<?php

namespace App\Services;

use Illuminate\Validation\Rules\Password;

/**
 * Class PasswordPolicy
 *
 * Enforces institutional security baseline rules for user passwords.
 * Rules: min 10 characters, mixed case (uppercase & lowercase), numbers, special symbols.
 */
class PasswordPolicy
{
    /**
     * Default password complexity rule.
     *
     * @return Password
     */
    public static function rule(): Password
    {
        return Password::min(10)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();
    }

    /**
     * Check if a given password meets the complexity requirements.
     *
     * @param  string  $password
     * @return bool
     */
    public static function validate(string $password): bool
    {
        if (strlen($password) < 10) {
            return false;
        }

        // At least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        // At least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        // At least one numeric digit
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        // At least one special symbol
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Return detailed strength validation results.
     *
     * @param  string  $password
     * @return array{valid: bool, errors: array<int, string>}
     */
    public static function validateStrength(string $password): array
    {
        $errors = [];
        if (strlen($password) < 10) {
            $errors[] = 'Password must be at least 10 characters.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one numeric digit.';
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special symbol.';
        }

        return [
            'valid'  => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Check if the user password is older than 90 days.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public static function isExpired(\App\Models\User $user): bool
    {
        if (empty($user->password_changed_at)) {
            return false;
        }

        return $user->password_changed_at->diffInDays(now()) >= 90;
    }
}
