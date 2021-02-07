<?php

namespace marvin255\bxcontent\controls;

use marvin255\bxcontent\Exception;

/**
 * Поле для ввода, которое объединяет в себе несколько других полей.
 */
class Combine extends Base
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'combine';
    }

    /**
     * Возвращает список скомбинированных элементов.
     *
     * @return array
     */
    public function getElements()
    {
        return $this->getSetting('elements');
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $return = parent::jsonSerialize();
        $return['elements'] = $this->getElements();

        return $return;
    }

    /**
     * @inheritdoc
     */
    protected function check(array $settings)
    {
        $settings = parent::check($settings);

        if (empty($settings['elements']) || !is_array($settings['elements'])) {
            throw new Exception('Contol\'s elements must be empty a non empty array instance');
        } else {
            $elements = [];
            foreach ($settings['elements'] as $key => $control) {
                if (!($control instanceof ControlInterface)) {
                    throw new Exception("Control with key {$key} must be a ControlInterface instance");
                } elseif (isset($elements[$control->getName()])) {
                    throw new Exception('Control with name ' . $control->getName() . ' already exists');
                }
                $elements[$control->getName()] = $control;
            }
            $settings['elements'] = $elements;
        }

        return $settings;
    }
}
