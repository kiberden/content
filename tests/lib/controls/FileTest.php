<?php

namespace marvin255\bxcontent\tests\lib\snippets;

class FileTest extends \PHPUnit_Framework_TestCase
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
            'file',
            $input->getType()
        );
    }

    public function testGetName()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
        ];

        $input = $this->getTestedObject($arConfig);

        $this->assertSame(
            $arConfig['name'],
            $input->getName()
        );
    }

    public function testGetLabel()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
        ];

        $input = $this->getTestedObject($arConfig);

        $this->assertSame(
            $arConfig['label'],
            $input->getLabel()
        );
    }

    public function testIsMultiple()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
        ];

        $input = $this->getTestedObject($arConfig);

        $this->assertSame(
            $arConfig['multiple'],
            $input->isMultiple()
        );
    }

    public function testJsonSerialize()
    {
        $arConfig = [
            'type' => 'file',
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'allowedExtensions' => ['png', 'jpg'],
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

    public function testGetAllowedExtensions()
    {
        $ext = 'ext_' . mt_rand();
        $ext1 = 'ext_1_' . mt_rand();
        $ext2 = 'ext_2_' . mt_rand();
        $arConfig = [
            'type' => 'file',
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'allowedExtensions' => "{$ext},{$ext1},{$ext2},",
        ];

        $input = $this->getTestedObject($arConfig);

        $this->assertSame(
            [$ext, $ext1, $ext2],
            $input->getAllowedExtensions()
        );
    }

    public function testEmptyNameException()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'multiple' => true,
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'name');
        $snippet = $this->getTestedObject($arConfig);
    }

    public function testEmptyLabelException()
    {
        $arConfig = [
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'label');
        $snippet = $this->getTestedObject($arConfig);
    }

    protected function getTestedObject(array $config)
    {
        return new \marvin255\bxcontent\controls\File($config);
    }
}
