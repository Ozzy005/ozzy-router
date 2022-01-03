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

    public function isEmpty(string $key): bool
    {
        return empty($this->data[$key]);
    }

    public function isNotEmpty(string $key): bool
    {
        return !empty($this->data[$key]);
    }

    public function isSet(string $key): bool
    {
        return isset($key, $this->data);
    }

    public function isNotSet(string $key): bool
    {
        return !isset($key, $this->data);
    }

    public function get(string $key): string|null
    {
        return $this->data[$key] ?? null;
    }

    public function exists(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function __get(string $name): string|null
    {
        return $this->headers[$name] ?? null;
    }
}
