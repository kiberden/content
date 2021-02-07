<?php

namespace marvin255\bxcontent\tests\lib\snippets;

class ImageTest extends FileTest
{
    public function testGetType()
    {
        $arConfig = [
            'type' => 'type_' . mt_rand(),
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
        ];

        $input = $this->getTestedObject($arConfig);

        $this->assertSame(
            'image',
            $input->getType()
        );
    }

    public function testJsonSerialize()
    {
        $arConfig = [
            'type' => 'image',
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'allowedExtensions' => ['png', 'jpg'],
            'width' => mt_rand(),
            'height' => mt_rand(),
        ];

        $input = $this->getTestedObject($arConfig);

        $arConfig['template'] = 'CAdminFileDialog::ShowScript';
        ksort($arConfig);

        $return = $input->jsonSerialize();
        ksort($return);
        $this->assertSame(
            $arConfig,
            $return
        );
    }

    protected function getTestedObject(array $config)
    {
        return new \marvin255\bxcontent\controls\Image($config);
    }
}
