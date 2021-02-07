<?php

namespace marvin255\bxcontent;

/**
 * Трэйт для объекта, который можно настраивать при создании.
 */
trait SettingsTrait
{
    /**
     * Настройки объекта, вида "название поля => значение".
     *
     * @var array
     */
    protected $settings = [];

    /**
     * Конструктор. Задает настройки объекта из массива.
     *
     * @param array $settings Настройки поля ввода, вида "название поля => значение"
     */
    public function __construct(array $settings = [])
    {
        $this->config($this->check($settings));
    }

    /**
     * Возвращает значение настройки по ее названию.
     *
     * @param string $name Название настройки
     *
     * @return mixed
     */
    protected function getSetting($name)
    {
        return isset($this->settings[$name]) ? $this->settings[$name] : null;
    }

    /**
     * Задает настройки объекта из массива.
     *
     * @param array $settings Настройки объекта, вида "название поля => значение"
     *
     * @return \marvin255\bxcontent\controls\ControlInterface
     */
    protected function config(array $settings)
    {
        $this->settings = $settings;

        return $this;
    }
}
