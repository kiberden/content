<?php

namespace marvin255\bxcontent\snippets;

use marvin255\bxcontent\controls\ControlInterface;
use marvin255\bxcontent\SettingsTrait;
use marvin255\bxcontent\Exception;
use marvin255\bxcontent\views\ViewInterface;

/**
 * Базовый сниппет, получает данные из массива в конструкторе
 * и проверяет их на валидность.
 */
class Base implements SnippetInterface
{
    use SettingsTrait;

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->getSetting('label');
    }

    /**
     * @inheritdoc
     */
    public function getControls()
    {
        return $this->getSetting('controls');
    }

    /**
     * @inheritdoc
     */
    public function isSearchable()
    {
        return $this->getSetting('is_searchable') === null
            ? true
            : (bool) $this->getSetting('is_searchable');
    }

    /**
     * @inheritdoc
     */
    public function render(array $snippetValues)
    {
        $view = $this->getSetting('view');

        return $view ? $view->render($snippetValues) : '';
    }

    /**
     * @inheritdoc
     */
    protected function check(array $settings)
    {
        if (empty($settings['label']) || trim($settings['label']) === '') {
            throw new Exception('Snippet\'s label can\'t be empty');
        }

        if (empty($settings['controls']) || !is_array($settings['controls'])) {
            throw new Exception('Snippet\'s controls must be a non empty array instance');
        } else {
            $controls = [];
            foreach ($settings['controls'] as $key => $control) {
                if (!($control instanceof ControlInterface)) {
                    throw new Exception("Control with key {$key} must be a ControlInterface instance");
                } elseif (isset($controls[$control->getName()])) {
                    throw new Exception('Control with name ' . $control->getName() . ' already exists');
                }
                $controls[$control->getName()] = $control;
            }
            $settings['controls'] = $controls;
        }

        if (!empty($settings['view']) && !($settings['view'] instanceof ViewInterface)) {
            throw new Exception('Snippet\'s view must be a ViewInterface instance');
        }

        return $settings;
    }
}
