<?php

namespace Marvin255Bxcontent;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use CBitrixComponent;

/**
 * Класс для компонента: Отображение содержимого сниппета.
 */
class Snippet extends CBitrixComponent
{
    /**
     * {@inheritdoc}
     *
     * @throws \Bitrix\Main\LoaderException
     */
    public function onPrepareComponentParams($p)
    {
        if (!Loader::includeModule('marvin255.bxcontent')) {
            throw new LoaderException("Can't load module marvin255.bxcontent");
        }

        return parent::onPrepareComponentParams($p);
    }

    /**
     * @inheritdoc
     */
    public function executeComponent()
    {
        //в результирующий массив передаем "сырые" параметры
        foreach ($this->arParams as $key => $value) {
            if (mb_strpos($key, '~') === 0) {
                $this->arResult[mb_substr($key, 1)] = $value;
            }
        }

        $this->includeComponentTemplate();
    }
}
