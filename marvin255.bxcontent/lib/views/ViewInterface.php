<?php

namespace marvin255\bxcontent\views;

/**
 * Интерфейс для объекта, который должен отобразить сниппет.
 */
interface ViewInterface
{
    /**
     * Возвращет строку с html для сниппета, заданного в массиве.
     *
     * @param array $snippetValues Массив вида "название поля => значение поля" для создания сниппета
     *
     * @return string
     */
    public function render(array $snippetValues);
}
