<?php

namespace JurisBerkulis\GbPhpL2Hw\Http;

use JsonException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;

readonly class Request
{

    public function __construct(
        /**
         * Аргумент, соответствующий суперглобальной переменной $_GET
         */
        private array  $get,

        /**
         * Аргумент, соответствующий суперглобальной переменной $_SERVER
         */
        private array  $server,

        /**
         * Аргумент для хранения тела запроса
         */
        private string $body,
    )
    {
    }

    /**
     * Получить путь api-запроса
     * @example Напрмер, для http://example.com/some/page?x=1&y=acb путём будет строка '/some/page'
     * @throws HttpException
     */
    public function path(): string
    {
        // В суперглобальном массиве $_SERVER
        // значение URI хранится под ключом REQUEST_URI
        if (!array_key_exists('REQUEST_URI', $this->server)) {
            // Если мы не можем получить URI - бросаем исключение
            throw new HttpException('Невозможно получить путь запроса');
        }

        // Используем встроенную в PHP функцию parse_url
        $components = parse_url($this->server['REQUEST_URI']);

        if (!is_array($components) || !array_key_exists('path', $components)) {
            // Если мы не можем получить путь - бросаем исключение
            throw new HttpException('Невозможно получить путь запроса');
        }

        return $components['path'];
    }

    /**
     * Получить значение определённого параметра строки api-запроса
     * @example Напрмер, для http://example.com/some/page?x=1&y=acb значением параметра x будет строка '1'
     * @throws HttpException
     */
    public function query(string $param): string
    {
        if (!array_key_exists($param, $this->get)) {
            // Если нет такого параметра в запросе - бросаем исключение
            throw new HttpException(
                "В запросе отсутствует параметр: $param"
            );
        }

        $value = trim($this->get[$param]);

        if (empty($value)) {
            // Если значение параметра пусто - бросаем исключение
            throw new HttpException(
                "В запросе пустой параметр запроса: $param"
            );
        }

        return $value;
    }

    /**
     * Получить значение определённого заголовка api-запроса
     * @throws HttpException
     */
    public function header(string $header): string
    {
        // В суперглобальном массиве $_SERVER
        // имена заголовков имеют префикс 'HTTP_',
        // а знаки подчёркивания заменены на минусы
        $headerName = mb_strtoupper("http_" . str_replace('-', '_', $header));

        if (!array_key_exists($headerName, $this->server)) {
            // Если нет такого заголовка - бросаем исключение
            throw new HttpException("В запросе нет такого заголовка: $header");
        }

        $value = trim($this->server[$headerName]);

        if (empty($value)) {
            // Если значение заголовка пусто - бросаем исключение
            throw new HttpException("Пустой заголовок в запросе: $header");
        }

        return $value;
    }

    /**
     * Получить массив, сформированный из json-форматированного тела api-запроса
     * @return array
     * @throws HttpException
     */
    public function jsonBody(): array
    {
        try {
            // Пытаемся декодировать json
            $data = json_decode(
                $this->body,
                // Декодируем в ассоциативный массив
                associative: true,
                // Бросаем исключение при ошибке
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (JsonException) {
            throw new HttpException("Невозможно декодировать json");
        }

        if (!is_array($data)) {
            throw new HttpException("Не является массивом/объектом в JSON");
        }

        return $data;
    }

    /**
     * Получить значение отдельного поля из json-форматированного тела api-запроса
     * @param string $field
     * @return mixed
     * @throws HttpException
     */
    public function jsonBodyField(string $field): mixed
    {
        $data = $this->jsonBody();

        if (!array_key_exists($field, $data)) {
            throw new HttpException("Нет поля: $field");
        }

        if (empty($data[$field])) {
            throw new HttpException("Пустое поле: $field");
        }

        return $data[$field];
    }

    /**
     * Получить метод (GET, POST, DELETE и т.д.) api-запроса
     * @throws HttpException
     */
    public function method(): string
    {
        // В суперглобальном массиве $_SERVER HTTP-метод хранится под ключом REQUEST_METHOD
        if (!array_key_exists('REQUEST_METHOD', $this->server)) {
            // Если мы не можем получить метод - бросаем исключение
            throw new HttpException('Невозможно получить метод из запроса');
        }

        return $this->server['REQUEST_METHOD'];
    }

}
