<?php

namespace marvin255\bxcontent\controls;

/**
 * Поле для ввода, которое отображается в виде WYSIWYG редактора.
 */
class Editor extends Base
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'editor';
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $return = parent::jsonSerialize();
        $return['template'] = $this->getTemplate();

        return $return;
    }

    /**
     * Возвращает шаблон для создания поля в js.
     *
     * В битриксе довольно эксцентричный js, который нельзя вызвать из js, а
     * приходится вызывать исключительно из php. Функция создает шаблон со
     * специальными плейсхолдерами для подстановки в js.
     *
     * @return string
     */
    protected function getTemplate()
    {
        ob_start();
        ob_implicit_flush(false);
        \CFileMan::AddHTMLEditorFrame(
            '_____name_____',
            '',
            '_____type_____',
            'html',
            [
                'height' => 450,
                'width' => '100%',
            ],
            'N',
            0,
            '',
            '',
            false,
            true,
            false,
            [
                'toolbarConfig' => 'admin',
                'hideTypeSelector' => 'Y',
            ]
        );

        return ob_get_clean();
    }
}
