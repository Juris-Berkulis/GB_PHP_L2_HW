<?php

namespace JurisBerkulis\GbPhpL2Hw\UnitTests\Blog\Commands;

use PHPUnit\Framework\Attributes\DataProvider;
use JurisBerkulis\GbPhpL2Hw\Blog\Commands\Arguments;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\ArgumentsException;
use PHPUnit\Framework\TestCase;

class ArgumentsTest extends TestCase
{

    /**
     * @throws ArgumentsException
     */
    public function testItReturnsArgumentsValueByName(): void
    {
        // Подготовка
        $arguments = new Arguments(['some_key' => 'some_value']);

        // Действие
        $value = $arguments->get('some_key');

        // Проверка
        $this->assertEquals('some_value', $value);
    }

    /**
     * @throws ArgumentsException
     */
    public function testItReturnsValuesAsStrings(): void
    {
        // Создаём объект с числом в качестве значения аргумента
        $arguments = new Arguments(['some_key' => 123]);

        $value = $arguments->get('some_key');

        // Проверяем значение и тип
        $this->assertSame('123', $value);

        // Проверяем, что число стало строкой
        $this->assertIsString($value);

    }

    public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
    {
        // Подготавливаем объект с пустым набором данных
        $arguments = new Arguments([]);

        // Описываем тип ожидаемого исключения
        $this->expectException(ArgumentsException::class);

        // и его сообщение
        $this->expectExceptionMessage("Нет такого аргумента: some_key");

        // Выполняем действие, приводящее к выбрасыванию исключения
        $arguments->get('some_key');
    }

    /**
     * Провайдер данных
     */
    public static function argumentsProvider(): iterable
    {
        // Первое значение будет передано в тест первым аргументом,
        // второе значение будет передано в тест вторым аргументом
        return [
            ['some_string', 'some_string'], // Тестовый набор #0
            [' some_string', 'some_string'], // Тестовый набор #1
            [' some_string ', 'some_string'], // Тестовый набор #2
            [123, '123'], // Тестовый набор #3
            [12.3, '12.3'], // Тестовый набор #4
        ];
    }

    /**
     * Связываем тест с провайдером данных с помощью аннотации #[DataProvider("argumentsProvider")]
     *
     * У теста два агрумента
     *
     * В одном тестовом наборе из провайдера данных два значения
     *
     * @throws ArgumentsException
     */
    #[DataProvider("argumentsProvider")]
    public function testItConvertsArgumentsToStrings(
        $inputValue,
        $expectedValue
    ): void
    {
        // Подставляем первое значение из тестового набора
        $arguments = new Arguments(['some_key' => $inputValue]);
        $value = $arguments->get('some_key');

        // Сверяем со вторым значением из тестового набора
        $this->assertEquals($expectedValue, $value);
    }

}
