<?php

namespace marvin255\bxcontent\packs\bootstrap;

use marvin255\bxcontent\packs\Pack;
use marvin255\bxcontent\controls\Input;
use marvin255\bxcontent\controls\Editor;
use marvin255\bxcontent\controls\Image;
use marvin255\bxcontent\controls\Combine;

/**
 * Сниппет для медиа объектов.
 *
 * Html будет создан на основе bootstrap media object. Входит в пак готовых сниппетов.
 */
class Media extends Pack
{
    /**
     * @inheritdoc
     */
    protected function getDefaultSettings()
    {
        global $APPLICATION;

        $return = [
            'label' => 'Медиа объекты',
            'controls' => [],
        ];

        $return['controls'][] = new Combine([
            'name' => 'items',
            'label' => 'Блоки',
            'multiple' => true,
            'elements' => [
                new Image(['name' => 'image', 'label' => 'Изображение']),
                new Input(['name' => 'link', 'label' => 'Ссылка']),
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
        return 'bootstrap.media';
    }

    /**
     * @inheritdoc
     */
    protected function renderInternal(array $snippetValues)
    {
        $return = '';
        if (!empty($snippetValues['items']) && is_array($snippetValues['items'])) {
            $key = 0;
            $items = '';
            foreach ($snippetValues['items'] as $item) {
                if (empty($item['caption']) || empty($item['text'])) {
                    continue;
                }

                $items .= '<div class="media">';
                if (!empty($item['image'])) {
                    $items .= '<div class="media-left">';
                    if (!empty($item['link'])) {
                        $items .= '<a href="' . htmlentities($item['link']) . '">';
                    }
                    $items .= '<img class="media-object" src="' . htmlentities($item['image']) . '" alt="' . htmlentities($item['caption']) . '">';
                    if (!empty($item['link'])) {
                        $items .= '</a>';
                    }
                    $items .= '</div>';
                }
                $items .= '<div class="media-body">';
                $items .= '<h4 class="media-heading">';
                if (!empty($item['link'])) {
                    $items .= '<a href="' . htmlentities($item['link']) . '">';
                }
                $items .= htmlentities($item['caption']);
                if (!empty($item['link'])) {
                    $items .= '</a>';
                }
                $items .= '</h4>';
                $items .= $item['text'];
                $items .= '</div>';
                $items .= '</div>';

                ++$key;
            }

            if ($items) {
                $return .= $items;
            }
        }

        return $return;
    }
}
