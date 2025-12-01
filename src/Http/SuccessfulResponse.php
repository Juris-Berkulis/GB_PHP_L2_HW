<?php declare(strict_types=1);

namespace JurisBerkulis\GbPhpL2Hw\Http;

/**
 * Класс успешного ответа
 */
class SuccessfulResponse extends Response
{

    protected const bool SUCCESS = true;

    /**
     * Успешный ответ содержит массив с данными (по умолчанию: пустой)
     * @param array $data
     */
    public function __construct(
        private readonly array $data = []
    )
    {
    }

    /**
     * Возвратить полезные данные ответа
     * @description Реализация абстрактного метода родительского класса
     * @return array[]
     */
    protected function payload(): array
    {
        return ['data' => $this->data];
    }

}
