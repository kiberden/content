<?php

namespace marvin255\bxcontent\tests\lib\snippets;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    public function testGetType()
    {
        $arConfig = [
            'type' => 'type_' . mt_rand(),
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'list' => [
                'li_key_' . mt_rand() => 'li_value_' . mt_rand(),
            ],
        ];

        $input = new \marvin255\bxcontent\controls\Select($arConfig);

        $this->assertSame(
            'select',
            $input->getType()
        );
    }

    public function testGetName()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'list' => [
                'li_key_' . mt_rand() => 'li_value_' . mt_rand(),
            ],
        ];

        $input = new \marvin255\bxcontent\controls\Select($arConfig);

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
            'list' => [
                'li_key_' . mt_rand() => 'li_value_' . mt_rand(),
            ],
        ];

        $input = new \marvin255\bxcontent\controls\Select($arConfig);

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
            'list' => [
                'li_key_' . mt_rand() => 'li_value_' . mt_rand(),
            ],
        ];

        $input = new \marvin255\bxcontent\controls\Select($arConfig);

        $this->assertSame(
            $arConfig['multiple'],
            $input->isMultiple()
        );
    }

    public function testGetList()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'list' => [
                'li_key_' . mt_rand() => 'li_value_' . mt_rand(),
            ],
        ];

        $input = new \marvin255\bxcontent\controls\Select($arConfig);

        $this->assertSame(
            $arConfig['list'],
            $input->getList()
        );
    }

    public function testGetPrompt()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'list' => [
                'li_key_' . mt_rand() => 'li_value_' . mt_rand(),
            ],
            'prompt' => 'prompt_' . mt_rand(),
        ];

        $input = new \marvin255\bxcontent\controls\Select($arConfig);

        $this->assertSame(
            $arConfig['prompt'],
            $input->getPrompt()
        );
    }

    public function testJsonSerialize()
    {
        $arConfig = [
            'type' => 'select',
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'list' => [
                'li_key_' . mt_rand() => 'li_value_' . mt_rand(),
            ],
            'prompt' => 'prompt_' . mt_rand(),
        ];
        ksort($arConfig);

        $input = new \marvin255\bxcontent\controls\Select($arConfig);

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
            'list' => [
                'li_key_' . mt_rand() => 'li_value_' . mt_rand(),
            ],
            'prompt' => 'prompt_' . mt_rand(),
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'name');
        $snippet = new \marvin255\bxcontent\controls\Select($arConfig);
    }

    public function testEmptyLabelException()
    {
        $arConfig = [
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'list' => [
                'li_key_' . mt_rand() => 'li_value_' . mt_rand(),
            ],
            'prompt' => 'prompt_' . mt_rand(),
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'label');
        $snippet = new \marvin255\bxcontent\controls\Select($arConfig);
    }

    public function testEmptyListException()
    {
        $arConfig = [
            'name' => 'name_' . mt_rand(),
            'label' => 'label_' . mt_rand(),
            'multiple' => true,
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'list');
        $snippet = new \marvin255\bxcontent\controls\Select($arConfig);
    }
}
