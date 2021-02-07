<?php

namespace marvin255\bxcontent\controls;

/**
 * Поле для ввода, которое отображается в виде строки
 * с возможностью загрузить файл чере интерфейс битрикса.
 *
 * После загрузки пробует отобразить файл как изображение.
 */
class Image extends File
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'image';
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $return = parent::jsonSerialize();

        $return['width'] = $this->getSetting('width')
            ? $this->getSetting('width')
            : null;
        $return['height'] = $this->getSetting('height')
            ? $this->getSetting('height')
            : null;

        return $return;
    }
}
