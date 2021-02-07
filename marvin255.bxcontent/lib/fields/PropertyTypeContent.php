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
class PropertyTypeContent
{
    /**
     * Возвращает описание поля для регистрации обработчика.
     *
     * @return array
     */
    public static function GetUserTypeDescription()
    {
        return [
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'Marvin255Bxcontent',
            'DESCRIPTION' => Loc::getMessage('BX_CONTENT_PROPERTY_TYPE_NAME'),
            'GetPropertyFieldHtml' => [__CLASS__, 'getPropertyFieldHtml'],
            'GetSearchContent' => [__CLASS__, 'getSearchContent'],
            'ConvertToDB' => [__CLASS__, 'convertToDB'],
            'GetPublicViewHTML' => [__CLASS__, 'getPublicViewHTML'],
            'GetSettingsHTML' => [__CLASS__, 'getSettingsHTML'],
            'PrepareSettings' => [__CLASS__, 'prepareSettings'],
        ];
    }

    /**
     * Проеобразовываем массив в json перед сохранением.
     *
     * @param array $arProperty Массив с описанием свойства
     * @param array $value      Массив со значениями
     *
     * @return array
     */
    public static function convertToDB($arProperty, $value)
    {
        $value['VALUE'] = is_array($value['VALUE']) || is_object($value['VALUE'])
            ? json_encode($value['VALUE'], JSON_UNESCAPED_UNICODE)
            : json_encode(json_decode($value['VALUE'], true), JSON_UNESCAPED_UNICODE);

        return $value;
    }

    /**
     * Вывод поля в публичной части со сформированным html.
     *
     * @param array $arProperty Массив с описанием свойства
     * @param array $value      Массив со значениями
     *
     * @return array
     */
    public static function getPublicViewHTML($arProperty, $value)
    {
        return isset($value['VALUE'])
            ? SnippetManager::getInstance()->render($value['VALUE'])
            : null;
    }

    /**
     * Вывод поля в публичной части со сформированным html.
     *
     * @param array $arProperty         Массив с описанием свойства
     * @param array $value              Массив со значениями
     * @param array $strHTMLControlName Пустой массив
     *
     * @return array
     */
    public static function getSearchContent($arProperty, $value, $strHTMLControlName)
    {
        return isset($value['VALUE'])
            ? SnippetManager::getInstance()->render($value['VALUE'], true)
            : null;
    }

    /**
     * Возвращает форму для настройки поля в административной части.
     *
     * @param array $arProperty         Массив с описанием свойства
     * @param array $strHTMLControlName Имя элемента управления для заполнения настроек свойства
     * @param array $arPropertyFields   В параметре arPropertyFields можно вернуть дополнительные флаги управления формой
     *
     * @return string
     */
    public static function getSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $return = '';
        $allSnippets = SnippetManager::getInstance()->getSnippetsList();
        if ($allSnippets) {
            $checked = isset($arProperty['USER_TYPE_SETTINGS']['allowed_snippets'])
                ? $arProperty['USER_TYPE_SETTINGS']['allowed_snippets']
                : [];
            $return .= '<tr>';
            $return .= '<td style="vertical-align: top;">' . Loc::getMessage('BX_CONTENT_SELECT_SNIPPETS') . ':</td>';
            $return .= '<td>';
            foreach ($allSnippets as $key => $snippet) {
                $isChecked = in_array($key, $checked);
                $return .= '<div style="margin: 0 0 0.3em;">';
                $return .= '<label>';
                $return .= '<input type="checkbox" value="' . htmlentities($key) . '" name="' . $strHTMLControlName['NAME'] . '[allowed_snippets][]"' . ($isChecked ? ' checked' : '') . '>';
                $return .= ' ' . htmlspecialchars($snippet->getLabel());
                $return .= '</label>';
                $return .= '</div>';
            }
            $return .= '</td>';
            $return .= '</tr>';
        }

        $arPropertyFields = [
            'HIDE' => ['DEFAULT_VALUE'],
        ];

        return $return;
    }

    /**
     * Метод возвращает либо массив с дополнительными настройками свойства,
     * либо весь набор настроек, включая стандартные.
     *
     * @param array $arFields
     *
     * @return array
     */
    public static function prepareSettings($arFields)
    {
        return isset($arFields['USER_TYPE_SETTINGS'])
            ? $arFields['USER_TYPE_SETTINGS']
            : [];
    }

    /**
     * Возвращает html для поля для ввода, которое отбразится в административной части.
     *
     * @param array $arProperty         Свойства поля из настроек административной части
     * @param array $value              Массив со значениями поля из битрикса
     * @param array $strHTMLControlName Массив с именами для элементов поля из битрикса
     *
     * @return string
     */
    public static function getPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        CJSCore::Init(['jquery']);
        SnippetManager::getInstance()->registerAssets(Asset::getInstance());

        $id = 'Marvin255Bxcontent-' . intval($arProperty['ID']);
        $options = isset($arProperty['USER_TYPE_SETTINGS']) && is_array($arProperty['USER_TYPE_SETTINGS'])
            ? json_encode($arProperty['USER_TYPE_SETTINGS'])
            : 'null';

        $return = '<textarea style="display: none;" id="' . $id . '" name="' . htmlentities($strHTMLControlName['VALUE']) . '">';
        $return .= htmlentities(json_encode(json_decode($value['VALUE'], true)));
        $return .= '</textarea>';
        $return .= "<script>jQuery(document).on('ready', function () { jQuery('#{$id}').marvin255bxcontent({$options}); });</script>";

        return $return;
    }
}
