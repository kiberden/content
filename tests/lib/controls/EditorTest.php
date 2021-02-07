<?php

namespace marvin255\bxcontent\tests\lib\snippets;

class EditorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetType()
    {
        $arConfig = [
            'type' => 'type_' . mt_rand(),
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
        ];

        $input = new \marvin255\bxcontent\controls\Editor($arConfig);

        $this->assertSame(
            'editor',
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

        $input = new \marvin255\bxcontent\controls\Editor($arConfig);

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

        $input = new \marvin255\bxcontent\controls\Editor($arConfig);

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

        $input = new \marvin255\bxcontent\controls\Editor($arConfig);

        $this->assertSame(
            $arConfig['multiple'],
            $input->isMultiple()
        );
    }

    public function testJsonSerialize()
    {
        $arConfig = [
            'type' => 'editor',
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
        ];

        $input = new \marvin255\bxcontent\controls\Editor($arConfig);

        $arConfig['template'] = 'CFileMan::AddHTMLEditorFrame';
        ksort($arConfig);

        $return = $input->jsonSerialize();
        ksort($return);
        $this->assertSame(
            $arConfig,
            $return
        );
    }

    public function testEmptyNameException()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'multiple' => true,
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'name');
        $snippet = new \marvin255\bxcontent\controls\Editor($arConfig);
    }

    public function testEmptyLabelException()
    {
        $arConfig = [
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'label');
        $snippet = new \marvin255\bxcontent\controls\Editor($arConfig);
    }
}
