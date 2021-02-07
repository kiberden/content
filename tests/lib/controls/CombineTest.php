<?php

namespace marvin255\bxcontent\tests\lib\snippets;

class CombineTest extends \PHPUnit_Framework_TestCase
{
    public function testGetType()
    {
        $elementName = 'element_name_' . mt_rand();
        $element = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $element->method('getName')->will($this->returnValue($elementName));

        $arConfig = [
            'type' => 'type_' . mt_rand(),
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'elements' => [
                $elementName => $element,
            ],
        ];

        $input = new \marvin255\bxcontent\controls\Combine($arConfig);

        $this->assertSame(
            'combine',
            $input->getType()
        );
    }

    public function testGetName()
    {
        $elementName = 'element_name_' . mt_rand();
        $element = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $element->method('getName')->will($this->returnValue($elementName));

        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'elements' => [
                $elementName => $element,
            ],
        ];

        $input = new \marvin255\bxcontent\controls\Combine($arConfig);

        $this->assertSame(
            $arConfig['name'],
            $input->getName()
        );
    }

    public function testGetLabel()
    {
        $elementName = 'element_name_' . mt_rand();
        $element = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $element->method('getName')->will($this->returnValue($elementName));

        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'elements' => [
                $elementName => $element,
            ],
        ];

        $input = new \marvin255\bxcontent\controls\Combine($arConfig);

        $this->assertSame(
            $arConfig['label'],
            $input->getLabel()
        );
    }

    public function testIsMultiple()
    {
        $elementName = 'element_name_' . mt_rand();
        $element = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $element->method('getName')->will($this->returnValue($elementName));

        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'elements' => [
                $elementName => $element,
            ],
        ];

        $input = new \marvin255\bxcontent\controls\Combine($arConfig);

        $this->assertSame(
            $arConfig['multiple'],
            $input->isMultiple()
        );
    }

    public function testJsonSerialize()
    {
        $elementName = 'element_name_' . mt_rand();
        $element = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $element->method('getName')->will($this->returnValue($elementName));

        $arConfig = [
            'type' => 'combine',
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'elements' => [
                $elementName => $element,
            ],
        ];
        ksort($arConfig);

        $input = new \marvin255\bxcontent\controls\Combine($arConfig);

        $return = $input->jsonSerialize();
        ksort($return);
        $this->assertSame(
            $arConfig,
            $return
        );
    }

    public function testEmptyNameException()
    {
        $elementName = 'element_name_' . mt_rand();
        $element = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $element->method('getName')->will($this->returnValue($elementName));

        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'multiple' => true,
            'elements' => [
                $elementName => $element,
            ],
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'name');
        $snippet = new \marvin255\bxcontent\controls\Combine($arConfig);
    }

    public function testEmptyLabelException()
    {
        $elementName = 'element_name_' . mt_rand();
        $element = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $element->method('getName')->will($this->returnValue($elementName));

        $arConfig = [
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'elements' => [
                $elementName => $element,
            ],
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'label');
        $snippet = new \marvin255\bxcontent\controls\Combine($arConfig);
    }

    public function testEmptyElementsException()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'elements');
        $snippet = new \marvin255\bxcontent\controls\Combine($arConfig);
    }

    public function testWrongElementInstanceException()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'elements' => ['test' => 123],
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'test');
        $snippet = new \marvin255\bxcontent\controls\Combine($arConfig);
    }

    public function testWrongElementNameDoublingException()
    {
        $elementName = 'element_name_' . mt_rand();

        $element = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $element->method('getName')->will($this->returnValue($elementName));

        $element2 = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $element2->method('getName')->will($this->returnValue($elementName));

        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'name' => 'name_' . mt_rand(),
            'multiple' => true,
            'elements' => [
                $element,
                $element2,
            ],
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', $elementName);
        $snippet = new \marvin255\bxcontent\controls\Combine($arConfig);
    }
}
