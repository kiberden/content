<?php

namespace marvin255\bxcontent\packs\bootstrap;

use marvin255\bxcontent\packs\Pack;
use marvin255\bxcontent\controls\Input;
use marvin255\bxcontent\controls\Editor;
use marvin255\bxcontent\controls\Combine;

/**
 * Сниппет для табов.
 *
 * Html будет создан на основе bootstrap tabs. Входит в пак готовых сниппетов.
 */
class Tabs extends Pack
{
    /**
     * Счетчик для получения уникальных идентификаторов слайдеров.
     *
     * @var int
     */
    protected static $idCounter = 0;

    /**
     * @inheritdoc
     */
    protected function getDefaultSettings()
    {
        global $APPLICATION;

        $return = [
            'label' => 'Табы',
            'controls' => [],
        ];

        $return['controls'][] = new Combine([
            'name' => 'items',
            'label' => 'Табы',
            'multiple' => true,
            'elements' => [
                new Input(['name' => 'caption', 'label' => 'Заголовок']),
                new Editor(['name' => 'text', 'label' => 'Содержимое']),
            ],
        ]);

        return $return;
    }

    /**
     * @inheritdoc
     */
    protected function getCodeForManager()
    {
        return 'bootstrap.tabs';
    }

    /**
     * @inheritdoc
     */
    protected function renderInternal(array $snippetValues)
    {
        $return = '';
        if (!empty($snippetValues['items']) && is_array($snippetValues['items'])) {
            $id = 'bootstrap-tabs-' . static::$idCounter;
            ++static::$idCounter;

            $key = 0;
            $menu = '';
            $items = '';
            foreach ($snippetValues['items'] as $item) {
                if (empty($item['caption'])) {
                    continue;
                }

                $menu .= '<li role="presentation"' . ($key === 0 ? ' class="active"' : '') . '>';
                $menu .= '<a href="#' . $id . '-' . $key . '" aria-controls="home" role="tab" data-toggle="tab">';
                $menu .= htmlentities($item['caption']);
                $menu .= '</a>';
                $menu .= '</li>';

                $items .= '<div role="tabpanel" class="tab-pane' . ($key === 0 ? ' active' : '') . '" id="' . $id . '-' . $key . '">';
                $items .= isset($item['text']) ? $item['text'] : '';
                $items .= '</div>';

                ++$key;
            }

            if ($items) {
                $return .= '<div>';
                $return .= '<ul class="nav nav-tabs" role="tablist">';
                $return .= $menu;
                $return .= '</ul>';
                $return .= '<div class="tab-content">';
                $return .= $items;
                $return .= '</div>';
                $return .= '</div>';
            }
        }

        return $return;
    }
}
