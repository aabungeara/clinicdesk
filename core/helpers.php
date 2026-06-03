<?php

function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

function sanitize(string $value): string
{
    return htmlspecialchars(
        trim($value),
        ENT_QUOTES,
        "UTF-8"
    );
}

function formatDate(string $date): string
{
    return date("d-m-Y", strtotime($date));
}

function formatTime(string $time): string
{
    return date("h:i A", strtotime($time));
}

function h(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, "UTF-8");
}

function url(string $path = ''): string
{
    $baseUrl = "http://localhost/clinicdesk"; 
    
    return $baseUrl . '/' . ltrim($path, '/');
}