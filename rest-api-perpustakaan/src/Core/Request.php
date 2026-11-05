<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private string $method;
    private string $uri;
    private array $body;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $this->body   = $this->parseBody();
    }

    public function getMethod(): string { return $this->method; }
    public function getUri(): string { return $this->uri; }
    public function getBody(): array { return $this->body; }

    private function parseBody(): array
    {
        if (!in_array($this->method, ['POST', 'PUT', 'PATCH'], true)) {
            return [];
        }

        $raw  = file_get_contents('php://input');
        $json = json_decode($raw ?: '', true);

        return is_array($json) ? $json : $_POST;
    }
}