<?php

namespace marvin255\bxcontent\packs\bootstrap;

use marvin255\bxcontent\packs\Pack;
use marvin255\bxcontent\controls\Input;
use marvin255\bxcontent\controls\Editor;
use marvin255\bxcontent\controls\Combine;

/**
 * Сниппет для аккордеона.
 *
 * Html будет создан на основе bootstrap Collapse. Входит в пак готовых сниппетов.
 */
class Collapse extends Pack
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
            'label' => 'Аккордеон',
            'controls' => [],
        ];

        $return['controls'][] = new Combine([
            'name' => 'items',
            'label' => 'Блоки',
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
        return 'bootstrap.collapse';
    }

    /**
     * @inheritdoc
     */
    protected function renderInternal(array $snippetValues)
    {
        $return = '';
        if (!empty($snippetValues['items']) && is_array($snippetValues['items'])) {
            $id = 'bootstrap-collapse-' . static::$idCounter;
            ++static::$idCounter;

            $key = 0;
            $items = '';
            foreach ($snippetValues['items'] as $item) {
                if (empty($item['caption']) || empty($item['text'])) {
                    continue;
                }

                $items .= '<div class="panel panel-default">';
                $items .= '<div class="panel-heading" role="tab" id="' . $id . '-heading-' . $key . '">';
                $items .= '<h4 class="panel-title">';
                $items .= '<a role="button" data-toggle="collapse" data-parent="#' . $id . '" href="#' . $id . '-collapse-' . $key . '" aria-expanded="true" aria-controls="' . $id . '-collapse-' . $key . '">';
                $items .= htmlentities($item['caption']);
                $items .= '</a>';
                $items .= '</h4>';
                $items .= '</div>';
                $items .= '<div id="' . $id . '-collapse-' . $key . '" class="panel-collapse collapse' . ($key === 0 ? ' in' : '') . '" role="tabpanel" aria-labelledby="' . $id . '-heading-' . $key . '">';
                $items .= '<div class="panel-body">';
                $items .= $item['text'];
                $items .= '</div>';
                $items .= '</div>';
                $items .= '</div>';

                ++$key;
            }

            if ($items) {
                $return .= '<div class="panel-group" id="' . $id . '" role="tablist" aria-multiselectable="true">';
                $return .= $items;
                $return .= '</div>';
            }
        }

        return $return;
    }
}
