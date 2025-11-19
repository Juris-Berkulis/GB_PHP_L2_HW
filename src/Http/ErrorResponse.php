<?php

namespace JurisBerkulis\GbPhpL2Hw\Http;

/**
 * Класс неуспешного ответа
 */
class ErrorResponse extends Response
{

    protected const bool SUCCESS = false;

    /**
     * Неуспешный ответ содержит строку с причиной неуспеха (по умолчанию: 'Что-то пошло не так')
     * @param string $reason
     */
    public function __construct(
        private string $reason = 'Что-то пошло не так'
    ) {
    }

    /**
     * Возвратить полезные данные ответа
     * @description Реализация абстрактного метода родительского класса
     * @return string[]
     */
    protected function payload(): array
    {
        return ['reason' => $this->reason];
    }

}
