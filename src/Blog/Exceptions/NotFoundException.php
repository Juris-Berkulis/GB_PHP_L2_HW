<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Класс не существует
 *
 * Согласно PSR-11, исключение, описывающее ситуацию,
 * когда объект не найден в контейнере,
 * должно реализовать контракт NotFoundExceptionInterface
 */
class NotFoundException extends AppException implements NotFoundExceptionInterface
{

}
