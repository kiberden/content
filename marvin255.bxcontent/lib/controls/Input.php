<?php

namespace marvin255\bxcontent\controls;

/**
 * Поле для ввода, которое отображается в виде строки.
 */
class Input extends Base
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'input';
    }
}
