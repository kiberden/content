<?php

namespace marvin255\bxcontent\views;

use CMain;

/**
 * Объект, который вызывает компонент Битрикса для того, чтобы отобразить сниппет.
 */
class Component implements ViewInterface
{
    /**
     * Ссылка на объект приложения Битрикса.
     *
     * @var \CMain
     */
    protected $app = null;
    /**
     * Строка с названием компонента для отображения.
     *
     * @var string
     */
    protected $component = '';
    /**
     * Строка с названием шаблона компонента для отображения.
     *
     * @var string
     */
    protected $template = null;

    /**
     * Конструктор.
     *
     * @param CMain  $app       Ссылка на объект приложения Битрикса
     * @param string $component Строка с названием компонента для отображения
     * @param string $template  Строка с названием шаблона компонента для отображения
     */
    public function __construct(CMain $app, $component, $template = '')
    {
        $this->app = $app;
        $this->component = $component;
        $this->template = $template;
    }

    /**
     * @inheritdoc
     */
    public function render(array $snippetValues)
    {
        ob_start();
        ob_implicit_flush(false);
        $this->app->IncludeComponent(
            $this->component,
            $this->template,
            $snippetValues,
            ['HIDE_ICONS' => 'Y']
        );

        return ob_get_clean();
    }
}
