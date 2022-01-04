<?php

/**
 *
 * @author Rafael Arend
 *
 **/

namespace Http;

class Request
{
    public readonly array $headers;
    public readonly array $data;
    public readonly string $rewritenUri;

    public function __construct()
    {
        $this->setHeaders();
        $this->rewriteUri();
        $this->setData();
    }

    private function setHeaders(): void
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            $explode = explode('_', $key);
            $keyFormated = '';

            foreach ($explode as $key => $chunk) {
                if (!$key) {
                    $keyFormated = strtolower($chunk);
                } else {
                    $keyFormated .= ucfirst(strtolower($chunk));
                }
            }

            $headers[$keyFormated] = $value;
        }

        $this->headers = $headers;
    }

    private function rewriteUri(): void
    {
        $uri = $this->requestUri;

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $this->rewritenUri = rawurldecode($uri);
    }

    private function setData(): void
    {
        $this->data = strtoupper($this->requestMethod) === 'POST' ? $_POST : $_GET;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function missing(string $key): bool
    {
        return !isset($this->data[$key]);
    }

    public function exists(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function __get(string $name): mixed
    {
        return $this->headers[$name] ?? null;
    }
}
