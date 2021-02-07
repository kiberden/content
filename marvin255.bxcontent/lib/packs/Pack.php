<?php

namespace marvin255\bxcontent\packs;

use marvin255\bxcontent\snippets\Base;
use marvin255\bxcontent\SnippetManager;

/**
 * Сниппет, который входит в пак сниппетов и является преднастроенным базовым
 * сниппетом.
 */
abstract class Pack extends Base
{
    /**
     * Возвращает список настроек по умолчанию для сладйера.
     *
     * @return array
     */
    abstract protected function getDefaultSettings();

    /**
     * Возвращает код сниппета для менеджера сниппетов.
     *
     * @return string
     */
    abstract protected function getCodeForManager();

    /**
     * Возвращает строку с html сниппета.
     *
     * @param array $snippetValues
     *
     * @return string
     */
    abstract protected function renderInternal(array $snippetValues);

    /**
     * @inheritdoc
     */
    public function __construct(array $settings = [])
    {
        $defaultSettings = $this->getDefaultSettings();
        parent::__construct(array_merge($defaultSettings, $settings));
    }

    /**
     * @inheritdoc
     */
    public function render(array $snippetValues)
    {
        $return = '';
        if ($view = $this->getSetting('view')) {
            $return = parent::render($snippetValues);
        } else {
            $return = $this->renderInternal($snippetValues);
        }

        return $return;
    }

    /**
     * Добавялет данный сниппет в менеджер сниппетов.
     *
     * @param \marvin255\bxcontent\SnippetManager $manager
     */
    public static function setTo(SnippetManager $manager, $settings = [])
    {
        $item = new static($settings);
        $manager->set($item->getCodeForManager(), $item);
    }
}
