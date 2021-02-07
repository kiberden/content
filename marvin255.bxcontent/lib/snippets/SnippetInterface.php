<?php

namespace marvin255\bxcontent\snippets;

/**
 * Интерфейс для построения законченой части контента - сниппета. Например,
 * сниппет слайдера или сниппет аккордеона.
 */
interface SnippetInterface
{
    /**
     * Возвращет человекочитаемую метку сниппета.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Возвращет массив с полями для ввода, которые будут выводиться в админке.
     *
     * @return array
     */
    public function getControls();

    /**
     * Возвращает правду, если контент сниппета должен индексироваться для поиска.
     *
     * @return bool
     */
    public function isSearchable();

    /**
     * Возвращет строку с html для сниппета, заданного в массиве.
     *
     * @param array $snippetValues Массив вида "название поля => значение поля" для создания сниппета
     *
     * @return string
     */
    public function render(array $snippetValues);
}
