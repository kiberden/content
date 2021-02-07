<?php

namespace marvin255\bxcontent\controls;

/**
 * Поле для ввода, которое отображается в виде textarea.
 */
class Textarea extends Base
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'textarea';
    }
}
