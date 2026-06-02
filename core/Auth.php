<?php

require_once __DIR__ . "/helpers.php";

class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION["user"] = [
            "id" => $user["id"],
            "name" => $user["name"],
            "role" => $user["role"]
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_unset();

        session_destroy();

        redirect("index.php?page=auth&action=login");
    }

    public static function check(): bool
    {
        return isset($_SESSION["user"]);
    }

    public static function currentUser(): ?array
    {
        return $_SESSION["user"] ?? null;
    }

    public static function role(): string
    {
        return $_SESSION["user"]["role"] ?? "";
    }

    public static function requireRole(string ...$roles): void
    {
        if (!self::check()) {
            redirect("index.php?page=auth&action=login");
        }

        if (!in_array(self::role(), $roles, true)) {
            redirect("views/errors/403.php");
        }
    }
}