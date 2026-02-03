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
        $value = trim($value);
        if ($value === '') {
            return false;
        }
        $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'Y/m/d'];
        $dt = null;
        foreach ($formats as $fmt) {
            $tmp = DateTimeImmutable::createFromFormat($fmt, $value);
            if ($tmp !== false) {
                // ensure parsed parts match input to avoid partial parses
                if ($tmp->format($fmt) === $value) {
                    $dt = $tmp;
                    break;
                }
            }
        }
        if (!$dt) {
            // try generic parse as last resort
            try {
                $tmp = new DateTimeImmutable($value);
                $dt = $tmp;
            } catch (\Throwable $e) {
                return false;
            }
        }
        $now = new DateTimeImmutable('now');
        if ($dt > $now) {
            return false;
        }
        $age = $now->diff($dt)->y;
        return $age >= 13 && $age <= 90;
    }
}
