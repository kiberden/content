<?php

namespace marvin255\bxcontent\packs\bootstrap;

use marvin255\bxcontent\packs\Pack;
use marvin255\bxcontent\controls\Input;
use marvin255\bxcontent\controls\Textarea;
use marvin255\bxcontent\controls\Image;
use marvin255\bxcontent\controls\Combine;

/**
 * Сниппет для слайдера.
 *
 * Html будет создан на основе bootstrap slider. Входит в пак готовых сниппетов.
 */
class Carousel extends Pack
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
        $return = [
            'label' => 'Слайдер',
            'controls' => [],
        ];

        $return['controls'][] = new Combine([
            'name' => 'items',
            'label' => 'Слайды',
            'multiple' => true,
            'elements' => [
                new Image(['name' => 'image', 'label' => 'Изображение']),
                new Input(['name' => 'caption', 'label' => 'Заголовок']),
                new Input(['name' => 'link', 'label' => 'Ссылка']),
                new Textarea(['name' => 'text', 'label' => 'Текст на слайде']),
            ],
        ]);

        return $return;
    }

    /**
     * @inheritdoc
     */
    protected function getCodeForManager()
    {
        return 'bootstrap.carousel';
    }

    /**
     * @inheritdoc
     */
    protected function renderInternal(array $snippetValues)
    {
        $return = '';
        if (!empty($snippetValues['items']) && is_array($snippetValues['items'])) {
            $id = 'bootstrap-carousel-' . static::$idCounter;
            ++static::$idCounter;

            $indicators = '';
            $slides = '';
            $sliderKey = 0;
            foreach ($snippetValues['items'] as $slide) {
                if (empty($slide['image'])) {
                    continue;
                }

                $indicators .= '<li data-target="#' . $id . '" data-slide-to="' . $sliderKey . '"';
                if ($sliderKey === 0) {
                    $indicators .= ' class="active"';
                }
                $indicators .= '></li>';

                $slides .= '<div class="item';
                if ($sliderKey === 0) {
                    $slides .= ' active';
                }
                $slides .= '">';
                if (!empty($slide['link'])) {
                    $slides .= '<a href="' . htmlentities($slide['link']) . '">';
                }
                $slides .= '<img src="' . htmlentities($slide['image']) . '"';
                if (!empty($slide['caption'])) {
                    $slides .= ' alt="' . htmlentities($slide['caption']) . '"';
                }
                $slides .= '>';
                if (!empty($slide['link'])) {
                    $slides .= '</a>';
                }
                if (!empty($slide['caption']) || !empty($slide['text'])) {
                    $slides .= '<div class="carousel-caption">';
                    if (!empty($slide['caption'])) {
                        $slides .= '<h3>' . htmlentities($slide['caption']) . '</h3>';
                    }
                    if (!empty($slide['text'])) {
                        $slides .= '<p>' . htmlentities($slide['text']) . '</p>';
                    }
                    $slides .= '</div>';
                }
                $slides .= '</div>';

                ++$sliderKey;
            }

            if ($slides) {
                $return .= '<div id="' . $id . '" class="carousel slide" data-ride="carousel">';

                $return .= '<ol class="carousel-indicators">';
                $return .= $indicators;
                $return .= '</ol>';

                $return .= '<div class="carousel-inner" role="listbox">';
                $return .= $slides;
                $return .= '</div>';

                $return .= '<a class="left carousel-control" href="#' . $id . '" role="button" data-slide="prev">';
                $return .= '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>';
                $return .= '<span class="sr-only">Предыдущий</span>';
                $return .= '</a>';

                $return .= '<a class="right carousel-control" href="#' . $id . '" role="button" data-slide="next">';
                $return .= '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
                $return .= '<span class="sr-only">Следующий</span>';
                $return .= '</a>';

                $return .= '</div>';
            }
        }

        return $return;
    }
}
