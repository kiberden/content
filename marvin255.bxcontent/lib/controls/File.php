<?php

namespace marvin255\bxcontent\controls;

use CAdminFileDialog;

/**
 * Поле для ввода, которое отображается в виде строки
 * с возможностью загрузить файл чере интерфейс битрикса.
 */
class File extends Base
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'file';
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $return = parent::jsonSerialize();
        $return['template'] = $this->getTemplate();
        $return['allowedExtensions'] = $this->getAllowedExtensions();

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
        $cAdminFileDialog = [
            'event' => '_____clickEvent_____',
            'arResultDest' => [
                'ELEMENT_ID' => '_____elementId_____',
            ],
            'arPath' => [
                'PATH' => '/upload',
            ],
            'select' => 'F',
            'operation' => 'O',
            'showUploadTab' => true,
            'showAddToMenuTab' => false,
            'allowAllFiles' => false,
            'SaveConfig' => false,
        ];

        if ($exts = $this->getAllowedExtensions()) {
            $cAdminFileDialog['fileFilter'] = implode(',', $exts);
        }

        ob_start();
        ob_implicit_flush(false);
        CAdminFileDialog::ShowScript($cAdminFileDialog);

        return ob_get_clean();
    }

    /**
     * Возвращает список расширений файлов, которые можно загрузить с помощью этого поля.
     *
     * @return null|array
     */
    public function getAllowedExtensions()
    {
        return $this->getSetting('allowedExtensions');
    }

    /**
     * @inheritdoc
     */
    protected function check(array $settings)
    {
        $settings = parent::check($settings);

        if (!empty($settings['allowedExtensions'])) {
            if (!is_array($settings['allowedExtensions'])) {
                $settings['allowedExtensions'] = explode(',', $settings['allowedExtensions']);
            }
            $settings['allowedExtensions'] = array_diff(array_map('trim', $settings['allowedExtensions']), ['']);
        }

        return $settings;
    }
}
