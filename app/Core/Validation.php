<?php

declare(strict_types=1);

namespace App\Core;

use DateTimeImmutable;

final class Validation
{
    public static function username(string $value): bool
    {
        return (bool)preg_match('/^[a-zA-Z0-9_.]{6,20}$/', $value);
    }

    public static function password(string $value): bool
    {
        if (strlen($value) < 8 || strlen($value) > 20) {
            return false;
        }
        return (bool)preg_match('/[A-Za-z]/', $value) && (bool)preg_match('/\d/', $value);
    }

    public static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function phone(string $value): bool
    {
        return (bool)preg_match('/^\d{2}\s\d\s\d{4}-\d{4}$/', $value);
    }

    public static function birthDate(string $value): bool
    {
        $dt = DateTimeImmutable::createFromFormat('d-m-Y', $value);
        if (!$dt) {
            return false;
        }
        $now = new DateTimeImmutable('now');
        $age = $now->diff($dt)->y;
        return $age >= 13 && $age <= 90;
    }
}
