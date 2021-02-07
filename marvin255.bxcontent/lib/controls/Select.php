<?php

namespace marvin255\bxcontent\controls;

use marvin255\bxcontent\Exception;

/**
 * Поле для ввода, которое отображается в виде списка с выбором из вариантов.
 */
class Select extends Base
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'select';
    }

    /**
     * Возвращает список для отображения.
     *
     * @return array
     */
    public function getList()
    {
        return $this->getSetting('list');
    }

    /**
     * Возвращает название для пустой опции в невыбранном списке.
     *
     * @return array
     */
    public function getPrompt()
    {
        return $this->getSetting('prompt');
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $return = parent::jsonSerialize();
        $return['list'] = $this->getList();
        $return['prompt'] = $this->getPrompt();

        return $return;
    }

    /**
     * @inheritdoc
     */
    protected function check(array $settings)
    {
        $settings = parent::check($settings);

        if (!isset($settings['list']) || !is_array($settings['list'])) {
            throw new Exception(
                "Contol's list parameter must be an array instance"
            );
        }

        return $settings;
    }
}
