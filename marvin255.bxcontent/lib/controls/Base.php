<?php

namespace marvin255\bxcontent\controls;

use marvin255\bxcontent\SettingsTrait;
use marvin255\bxcontent\Exception;

/**
 * Базовый класс для поля ввода, которое будет отображено в административной
 * части.
 */
abstract class Base implements ControlInterface
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
    public function getName()
    {
        return $this->getSetting('name');
    }

    /**
     * @inheritdoc
     */
    public function isMultiple()
    {
        return (bool) $this->getSetting('multiple');
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'type' => $this->getType(),
            'label' => $this->getLabel(),
            'multiple' => $this->isMultiple(),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function check(array $settings)
    {
        if (empty($settings['name']) || trim($settings['name']) === '') {
            throw new Exception('Contol\'s name can\'t be empty');
        }

        if (empty($settings['label']) || trim($settings['label']) === '') {
            throw new Exception('Contol\'s label can\'t be empty');
        }

        return $settings;
    }
}
