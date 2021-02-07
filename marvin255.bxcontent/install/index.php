<?php

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);

class marvin255_bxcontent extends CModule
{
    public function __construct()
    {
        $arModuleVersion = [];

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'marvin255.bxcontent';
        $this->MODULE_NAME = Loc::getMessage('BX_CONTENT_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('BX_CONTENT_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('BX_CONTENT_MODULE_PARTNER_NAME');
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installFiles();
        $this->installDB();
    }

    public function doUninstall()
    {
        $this->unInstallFiles();
        $this->uninstallDB();
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    /**
     * Вносит в базу данных изменения, требуемые модулем
     *
     * @return bool
     */
    public function installDB()
    {
        $eventManager = EventManager::getInstance();
        foreach ($this->getEventsList() as $event) {
            $res = $eventManager->registerEventHandlerCompatible(
                $event['FROM_MODULE_ID'],
                $event['EVENT_TYPE'],
                $this->MODULE_ID,
                $event['TO_CLASS'],
                $event['TO_METHOD'],
                $event['SORT']
            );
        }
    }

    /**
     * Удаляет из базы данных изменения, требуемые модулем
     *
     * @return bool
     */
    public function uninstallDB()
    {
        $eventManager = EventManager::getInstance();
        foreach ($this->getEventsList() as $event) {
            $eventManager->unRegisterEventHandler(
                $event['FROM_MODULE_ID'],
                $event['EVENT_TYPE'],
                $this->MODULE_ID,
                $event['TO_CLASS'],
                $event['TO_METHOD']
            );
        }
    }

    /**
     * Копирует файлы модуля в битрикс
     *
     * @return bool
     */
    public function installFiles()
    {
        CopyDirFiles(
            $this->getInstallatorPath() . '/js',
            Application::getDocumentRoot() . '/bitrix/js/' . $this->MODULE_ID,
            true,
            true
        );

        CopyDirFiles(
            $this->getInstallatorPath() . '/css',
            Application::getDocumentRoot() . '/bitrix/css/' . $this->MODULE_ID,
            true,
            true
        );

        CopyDirFiles(
            $this->getInstallatorPath() . '/components',
            $this->getComponentPath('components') . '/' . $this->MODULE_ID,
            true,
            true
        );

        return true;
    }

    /**
     * Удаляет файлы модуля из битрикса.
     *
     * @return bool
     */
    public function unInstallFiles()
    {
        Directory::deleteDirectory(Application::getDocumentRoot() . '/bitrix/js/' . $this->MODULE_ID);
        Directory::deleteDirectory(Application::getDocumentRoot() . '/bitrix/css/' . $this->MODULE_ID);
        Directory::deleteDirectory($this->getComponentPath('components') . '/' . $this->MODULE_ID);

        return true;
    }

    /**
     * Возвращает список событий, которые должны быть установлены для данного модуля.
     *
     * @return array
     */
    protected function getEventsList()
    {
        return [
            [
                'FROM_MODULE_ID' => 'main',
                'EVENT_TYPE' => 'OnUserTypeBuildList',
                'TO_CLASS' => '\marvin255\bxcontent\fields\UserTypeContent',
                'TO_METHOD' => 'GetUserTypeDescription',
                'SORT' => '1800',
            ],
            [
                'FROM_MODULE_ID' => 'iblock',
                'EVENT_TYPE' => 'OnIBlockPropertyBuildList',
                'TO_CLASS' => '\marvin255\bxcontent\fields\PropertyTypeContent',
                'TO_METHOD' => 'GetUserTypeDescription',
                'SORT' => '1800',
            ],
        ];
    }

    /**
     * Возвращает путь к папке с модулем
     *
     * @return string
     */
    public function getInstallatorPath()
    {
        return str_replace('\\', '/', __DIR__);
    }

    /**
     * Возвращает путь к папке, в которую будут установлены компоненты модуля.
     *
     * @param string $type тип компонентов для установки (components, js, admin и т.д.)
     *
     * @return string
     */
    public function getComponentPath($type = 'components')
    {
        if ($type === 'admin') {
            $base = Application::getDocumentRoot() . '/bitrix';
        } else {
            $base = dirname(dirname(dirname($this->getInstallatorPath())));
        }

        return $base . '/' . str_replace(['/', '.'], '', $type);
    }
}
