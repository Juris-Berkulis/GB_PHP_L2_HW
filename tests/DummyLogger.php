<?php

namespace JurisBerkulis\GbPhpL2Hw\UnitTests;

use Psr\Log\LoggerInterface;
use Stringable;

/**
 * Тестовый класс, имитирующий интерфейс логгера "LoggerInterface" из PSR-3
 */
class DummyLogger implements LoggerInterface
{

    /**
     * Система не может быть использована
     *
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function emergency(
        Stringable|string $message, array $context = []
    ): void
    {
    }

    /**
     * Требуется немедленная реакция
     *
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function alert(
        Stringable|string $message, array $context = []
    ): void
    {
    }

    /**
     * Критическое состояние.
     *
     *  Например, компонент системы недоступен
     *  или выброшено неожиданное исключение
     *
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function critical(
        Stringable|string $message, array $context = []
    ): void
    {
    }

    /**
     * Ошибка во время исполнения программы, не требующая
     * немедленной реакции; обычно должна логироваться и наблюдаться
     *
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function error(
        Stringable|string $message, array $context = []
    ): void
    {
    }

    /**
     * Исключительное событие, не являющееся ошибкой
     *
     * Например, использование устаревшего контракта,
     * нежелательные действия, которые не обязательно неправильны
     *
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function warning(
        Stringable|string $message, array $context = []
    ): void
    {
    }

    /**
     * Нормальное, однако значительное событие
     *
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function notice(
        Stringable|string $message, array $context = []
    ): void
    {
    }

    /**
     * События, представляющие некоторый интерес
     *
     * Например, пользователь авторизовался в системе; SQL-запрос
     *
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function info(
        Stringable|string $message, array $context = []
    ): void
    {
    }

    /**
     * Детальная отладочная информация
     *
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function debug(
        Stringable|string $message, array $context = []
    ): void
    {
    }

    /**
     * Логирование с произвольным уровнем
     *
     * @param $level
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function log(
        $level, Stringable|string $message, array $context = []
    ): void
    {
    }

}
