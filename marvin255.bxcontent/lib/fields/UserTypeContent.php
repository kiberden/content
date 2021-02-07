<?php

namespace marvin255\bxcontent\fields;

use marvin255\bxcontent\SnippetManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use CJSCore;

Loc::loadMessages(__FILE__);

/**
 * Пользовательское поле, для которого додавляется js конструктор, что позволяет
 * создавать сложный html: слайдеры, аккордеоны и т.д.
 */
class UserTypeContent
{
    /**
     * Возвращает описание поля для регистрации обработчика.
     *
     * @return array
     */
    public function GetUserTypeDescription()
    {
        return [
            'USER_TYPE_ID' => 'Marvin255BxcontentUf',
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => Loc::getMessage('BX_CONTENT_PROPERTY_TYPE_NAME'),
            'BASE_TYPE' => 'string',
        ];
    }

    /**
     * Возвращает форму для настройки поля в административной части.
     *
     * @param bool|array $arUserField
     * @param array      $arHtmlControl
     * @param bool       $bVarsFromForm
     *
     * @return string
     */
    public function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
    {
        $return = '';
        $allSnippets = SnippetManager::getInstance()->getSnippetsList();
        if ($allSnippets) {
            $checked = isset($arUserField['SETTINGS']['allowed_snippets'])
                ? $arUserField['SETTINGS']['allowed_snippets']
                : [];
            $return .= '<tr>';
            $return .= '<td style="vertical-align: top;">' . Loc::getMessage('BX_CONTENT_SELECT_SNIPPETS') . ':</td>';
            $return .= '<td>';
            foreach ($allSnippets as $key => $snippet) {
                $isChecked = in_array($key, $checked);
                $return .= '<div style="margin: 0 0 0.3em;">';
                $return .= '<label>';
                $return .= '<input type="checkbox" value="' . htmlentities($key) . '" name="' . $arHtmlControl['NAME'] . '[allowed_snippets][]"' . ($isChecked ? ' checked' : '') . '>';
                $return .= ' ' . htmlspecialchars($snippet->getLabel());
                $return .= '</label>';
                $return .= '</div>';
            }
            $return .= '</td>';
            $return .= '</tr>';
        }

        return $return;
    }

    /**
     * Метод возвращает массив с дополнительными настройками свойства.
     *
     * @param array $arFields
     *
     * @return array
     */
    public function PrepareSettings($arUserField)
    {
        return isset($arUserField['SETTINGS'])
            ? $arUserField['SETTINGS']
            : [];
    }

    /**
     * Возвращает html для поля для ввода, которое отбразится в административной части.
     *
     * @param array $field   Свойства поля из настроек административной части
     * @param array $control Массив с именами для элементов поля из битрикса
     *
     * @return string
     */
    public function GetEditFormHTML($field, $control)
    {
        CJSCore::Init(['jquery']);
        SnippetManager::getInstance()->registerAssets(Asset::getInstance());

        $id = 'Marvin255BxcontentUf-' . intval($field['ID']);
        $options = isset($field['SETTINGS']) && is_array($field['SETTINGS'])
            ? json_encode($field['SETTINGS'])
            : 'null';

        $return = '<textarea style="display: none;" id="' . $id . '" name="' . htmlentities($control['NAME']) . '">';
        $return .= htmlentities(isset($field['VALUE']) ? json_encode(json_decode($field['VALUE'], true)) : '');
        $return .= '</textarea>';
        $return .= "<script>jQuery(document).on('ready', function () { jQuery('#{$id}').marvin255bxcontent({$options}); });</script>";

        return $return;
    }

    /**
     * Возвращает описание колонки в базе данных, которая будет создана для сущности.
     */
    public function GetDBColumnType($field)
    {
        return 'text';
    }

    /**
     * Преобразуем массив в строку перед сохранением результатов поля.
     *
     * @return string
     */
    public function OnBeforeSave($arUserField, $value)
    {
        $value = is_array($value) || is_object($value)
            ? json_encode($value, JSON_UNESCAPED_UNICODE)
            : json_encode(json_decode($value, true), JSON_UNESCAPED_UNICODE);

        return $value;
    }
}
