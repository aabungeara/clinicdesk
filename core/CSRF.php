<?php

class CSRF
{
    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));

        $_SESSION["csrf_token"] = $token;

        return $token;
    }

    public static function validateToken(string $token): bool
    {
        if (
            !isset($_SESSION["csrf_token"])
        ) {
            return false;
        }

        return hash_equals(
            $_SESSION["csrf_token"],
            $token
        );
    }
}