<?php

use marvin255\bxcontent\SnippetManager;
use marvin255\bxcontent\Exception;
use Bitrix\Main\Event;

require_once __DIR__ . '/lib/Autoloader.php';

$snippetManager = SnippetManager::getInstance(true);

$snippetManager->setListInitializationCallback(function ($snippetManager) {
    $event = new Event(
        'marvin255.bxcontent',
        'collectSnippets',
        ['snippetManager' => $snippetManager]
    );
    $event->send();
    foreach ($event->getResults() as $eventResult) {
        if ($eventResult->getType() === EventResult::ERROR) {
            throw new Exception(
                'Get error while collecting snippets: '
                . implode(',', $eventResult->getErrorMessages())
            );
        }
    }
});

$snippetManager->addJs('/bitrix/js/marvin255.bxcontent/plugin.js');
$snippetManager->addJs('/bitrix/js/marvin255.bxcontent/controls.js');
$snippetManager->addCss('/bitrix/css/marvin255.bxcontent/plugin.css');
