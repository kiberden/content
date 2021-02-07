<?php

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/marvin255.bxcontent/lib/Autoloader.php';

// далее определены классы моков для статических вызовов битрикса
class CAdminFileDialog
{
    public static function ShowScript(array $data)
    {
        echo 'CAdminFileDialog::ShowScript';
    }
}

class CFileMan
{
    public static function AddHTMLEditorFrame()
    {
        echo 'CFileMan::AddHTMLEditorFrame';
    }
}
