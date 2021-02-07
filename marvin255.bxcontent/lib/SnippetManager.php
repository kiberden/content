<?php

namespace marvin255\bxcontent;

use marvin255\bxcontent\snippets\SnippetInterface;
use Bitrix\Main\Page\Asset;
use JsonSerializable;

/**
 * Менеджер доступных сниппетов для контента.
 *
 * Так, как нет никакой возможности передать менеджера в инстанс поля, то
 * реализует Singleton, что, в принципе, не особо нужно было бы.
 */
class SnippetManager implements JsonSerializable
{
    /**
     * Объект для реализации singleton.
     *
     * @var \marvin255\bxcontent\SnippetManager
     */
    private static $instance = null;
    /**
     * Массив зарегистрированных сниппетов.
     *
     * @var array
     */
    protected $snippets = [];
    /**
     * Список js файлов, которые нужно зарегистрировать для текущего набора сниппетов.
     *
     * @var array
     */
    protected $js = [];
    /**
     * Список css файлов, которые нужно зарегистрировать для текущего набора сниппетов.
     *
     * @var array
     */
    protected $css = [];
    /**
     * Флаг, который указывает, что список сниппетов был иницализирован.
     *
     * @var bool
     */
    protected $isListInitialized = false;
    /**
     * Коллбэк, который будет запущен перед инициализацией списка.
     *
     * @var callbable|null
     */
    protected $listInitializationCallback;

    /**
     * Возвращает объект singleton, если он уже создан, либо создает новый
     * и возвращает новый.
     *
     * @param bool $refresh Флаг, который указывает, что инстанс нужно пересоздать заново
     *
     * @return \marvin255\bxcontent\SnippetManager
     */
    public static function getInstance($refresh = false)
    {
        return self::$instance === null || $refresh
            ? self::$instance = new self
            : self::$instance;
    }

    /**
     * Реализация singleton. Запрещает создание новых объектов.
     */
    private function __construct()
    {
    }

    /**
     * Добавляет коллбэк для инициализации сниппета.
     *
     * @param callable $callback
     *
     * @return \marvin255\bxcontent\SnippetManager
     *
     * @throws \marvin255\bxcontent\Exception
     */
    public function setListInitializationCallback($callback)
    {
        if ($this->isListInitialized) {
            throw new Exception('Snippet list already initialized');
        }
        if (!is_callable($callback)) {
            throw new Exception('Callback parameter must be a callable instance');
        }

        $this->listInitializationCallback = $callback;

        return $this;
    }

    /**
     * Добавляет новый сниппет.
     *
     * @param string                                         $name    Название сниппета
     * @param \marvin255\bxcontent\snippets\SnippetInterface $snippet Объект сниппета
     *
     * @return \marvin255\bxcontent\SnippetManager
     *
     * @throws \marvin255\bxcontent\Exception
     */
    public function set($name, SnippetInterface $snippet)
    {
        $name = $this->normalizeSnippetName($name);
        if (!$name) {
            throw new Exception('Empty snippet name');
        }
        $this->snippets[$name] = $snippet;

        return $this;
    }

    /**
     * Удаляет сниппет.
     *
     * @param string $name Название сниппета
     *
     * @return \marvin255\bxcontent\SnippetManager
     *
     * @throws \marvin255\bxcontent\Exception
     */
    public function remove($name)
    {
        if ($this->get($name) === null) {
            throw new Exception(
                "Can't find snippet with type {$name} to unset"
            );
        }
        $name = $this->normalizeSnippetName($name);
        unset($this->snippets[$name]);

        return $this;
    }

    /**
     * Возвращает сниппет по его имени.
     *
     * @param string $name Название сниппета
     *
     * @return \marvin255\bxcontent\snippets\SnippetInterface|null
     */
    public function get($name)
    {
        $name = $this->normalizeSnippetName($name);
        $list = $this->getSnippetsList();

        return isset($list[$name]) ? $list[$name] : null;
    }

    /**
     * Возвращает список всех сниппетов, зарегистрированных на данный момент в
     * менеджере.
     *
     * @return array
     */
    public function getSnippetsList()
    {
        if (!$this->isListInitialized) {
            $this->isListInitialized = true;
            if ($this->listInitializationCallback) {
                call_user_func($this->listInitializationCallback, $this);
            }
        }

        return $this->snippets;
    }

    /**
     * Приводит данные менеджера сниппетов к представлению, которое будет отправлено в json.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $return = [];
        foreach ($this->getSnippetsList() as $name => $snippet) {
            $return[$name] = [
                'label' => $snippet->getLabel(),
                'controls' => $snippet->getControls(),
            ];
        }

        return $return;
    }

    /**
     * Возвращает набор js, которые зарегистриует данны объект.
     *
     * @return array
     */
    public function getJs()
    {
        return $this->js;
    }

    /**
     * Задает список js для регистрации.
     *
     * @param array $js
     *
     * @return \marvin255\bxcontent\SnippetManager
     *
     * @throws \marvin255\bxcontent\Exception
     */
    public function setJs(array $js)
    {
        $this->js = [];
        foreach ($js as $script) {
            $this->addJs($script);
        }

        return $this;
    }

    /**
     * Добавляет скрипт к списку для регистрации.
     *
     * @param string $script
     *
     * @return \marvin255\bxcontent\SnippetManager
     *
     * @throws \marvin255\bxcontent\Exception
     */
    public function addJs($script)
    {
        if (trim($script) === '') {
            throw new Exception("Script name can't be blank");
        }
        $this->js[] = $script;

        return $this;
    }

    /**
     * Возвращает набор css, которые зарегистриует данны объект.
     *
     * @return array
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * Задает список css для регистрации.
     *
     * @param array $css
     *
     * @return \marvin255\bxcontent\SnippetManager
     *
     * @throws \marvin255\bxcontent\Exception
     */
    public function setCss(array $css)
    {
        $this->css = [];
        foreach ($css as $style) {
            $this->addCss($style);
        }

        return $this;
    }

    /**
     * Добавляет стили к списку для регистрации.
     *
     * @param string $style
     *
     * @return \marvin255\bxcontent\SnippetManager
     *
     * @throws \marvin255\bxcontent\Exception
     */
    public function addCss($style)
    {
        if (trim($style) === '') {
            throw new Exception("Stylesheet name can't be blank");
        }
        $this->css[] = $style;

        return $this;
    }

    /**
     * Регистриует все ассеты для отображения полей в админке.
     *
     * @param \Bitrix\Main\Page\Asset $asset Менеджер ассетов битрикса
     *
     * @return \marvin255\bxcontent\SnippetManager
     */
    public function registerAssets(Asset $asset)
    {
        $js = $this->getJs();
        foreach ($js as $script) {
            $asset->addJs($script, true);
        }

        $css = $this->getCss();
        foreach ($css as $style) {
            $asset->addString('<link rel="stylesheet" type="text/css" href="' . $style . '">', true);
        }

        $managerData = "<script>$.fn.marvin255bxcontent('registerSnippets', ";
        $managerData .= json_encode($this);
        $managerData .= ');</script>';
        $asset->addString($managerData, true);
    }

    /**
     * Отображает все эелементы из массива или строки с валидным json в соответствии
     * с настройками конкретных сниппетов.
     *
     * @param array|string $toRender
     * @param bool|null    $isSearchable
     *
     * @return string
     */
    public function render($toRender, $isSearchable = null)
    {
        $return = '';
        $toRender = is_string($toRender) ? json_decode($toRender, true) : $toRender;
        if (is_array($toRender)) {
            foreach ($toRender as $item) {
                if (empty($item['type']) || !($snippet = $this->get($item['type']))) {
                    continue;
                }
                if ($isSearchable !== null && $snippet->isSearchable() !== (bool) $isSearchable) {
                    continue;
                }
                unset($item['type']);
                $return .= (string) $snippet->render($item);
            }
        }

        return $return;
    }

    /**
     * Приводит имена сниппетов в единообразный вид.
     *
     * @param string $name
     *
     * @return string
     */
    protected function normalizeSnippetName($name)
    {
        return strtolower(trim($name));
    }
}
