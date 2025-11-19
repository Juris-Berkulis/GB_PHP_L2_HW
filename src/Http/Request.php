<?php

namespace JurisBerkulis\GbPhpL2Hw\Http;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;

class Request
{

    public function __construct(
        /**
         * Аргумент, соответствующий суперглобальной переменной $_GET
         */
        private array $get,

        /**
         * Аргумент, соответствующий суперглобальной переменной $_SERVER
         */
        private array $server
    ) {
    }

    /**
     * Метод для получения пути запроса
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
     * Метод для получения значения определённого параметра строки запроса
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
     * Метод для получения значения определённого заголовка
     * @throws HttpException
     */
    public function header(string $header): string
    {
        // В суперглобальном массиве $_SERVER
        // имена заголовков имеют префикс 'HTTP_',
        // а знаки подчёркивания заменены на минусы
        $headerName = mb_strtoupper("http_". str_replace('-', '_', $header));

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

}
