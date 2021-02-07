<?php

namespace marvin255\bxcontent\tests\lib\snippets;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLabel()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'controls' => [
                $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
                    ->getMock(),
            ],
        ];

        $snippet = new \marvin255\bxcontent\snippets\Base($arConfig);

        $this->assertSame(
            $arConfig['label'],
            $snippet->getLabel()
        );
    }

    public function testGetControls()
    {
        $controlKey = 'controls_key_' . mt_rand();
        $control = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $control->method('getName')->will($this->returnValue($controlKey));

        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'controls' => [$control],
        ];

        $snippet = new \marvin255\bxcontent\snippets\Base($arConfig);

        $this->assertSame(
            [$controlKey => $control],
            $snippet->getControls()
        );
    }

    public function testIsSearchable()
    {
        $controlKey = 'controls_key_' . mt_rand();
        $control = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $control->method('getName')->will($this->returnValue($controlKey));

        $arConfigDefault = [
            'label' => 'label_' . mt_rand(),
            'controls' => [$control],
        ];
        $arConfigSearchable = [
            'label' => 'label_' . mt_rand(),
            'controls' => [$control],
            'is_searchable' => true,
        ];
        $arConfigNotSearchable = [
            'label' => 'label_' . mt_rand(),
            'controls' => [$control],
            'is_searchable' => false,
        ];

        $defaultSnippet = new \marvin255\bxcontent\snippets\Base($arConfigDefault);
        $searchableSnippet = new \marvin255\bxcontent\snippets\Base($arConfigSearchable);
        $notsearchableSnippet = new \marvin255\bxcontent\snippets\Base($arConfigNotSearchable);

        $this->assertSame(true, $defaultSnippet->isSearchable());
        $this->assertSame(true, $searchableSnippet->isSearchable());
        $this->assertSame(false, $notsearchableSnippet->isSearchable());
    }

    public function testRender()
    {
        $controlKey = 'controls_key_' . mt_rand();
        $control = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $control->method('getName')->will($this->returnValue($controlKey));

        $viewData = [
            'view_key_' . mt_rand() => 'view_value_' . mt_rand(),
            'view_key_1_' . mt_rand() => 'view_value_1_' . mt_rand(),
        ];
        $viewRendered = 'rendered_' . mt_rand();
        $view = $this->getMockBuilder('\marvin255\bxcontent\views\ViewInterface')
            ->getMock();
        $view->expects($this->once())
            ->method('render')
            ->with($this->equalTo($viewData))
            ->will($this->returnValue($viewRendered));

        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'controls' => [$control],
            'view' => $view,
        ];

        $snippet = new \marvin255\bxcontent\snippets\Base($arConfig);

        $this->assertSame(
            $viewRendered,
            $snippet->render($viewData)
        );
    }

    public function testEmptyLabelException()
    {
        $arConfig = [
            'controls' => [
                $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
                    ->getMock(),
            ],
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'label');
        $snippet = new \marvin255\bxcontent\snippets\Base($arConfig);
    }

    public function testEmptyControlsException()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'controls');
        $snippet = new \marvin255\bxcontent\snippets\Base($arConfig);
    }

    public function testNonArrayControlsException()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'controls' => 123,
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'controls');
        $snippet = new \marvin255\bxcontent\snippets\Base($arConfig);
    }

    public function testWrongControlsInstanceException()
    {
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'controls' => ['test_key' => 123],
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'test_key');
        $snippet = new \marvin255\bxcontent\snippets\Base($arConfig);
    }

    public function testNameDoublingControlsException()
    {
        $controlKey = 'controls_key_' . mt_rand();
        $control = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $control->method('getName')->will($this->returnValue($controlKey));

        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'controls' => [
                $control,
                $control,
            ],
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', $controlKey);
        $snippet = new \marvin255\bxcontent\snippets\Base($arConfig);
    }

    public function testWrongViewInstanceException()
    {
        $controlKey = 'controls_key_' . mt_rand();
        $control = $this->getMockBuilder('\marvin255\bxcontent\controls\ControlInterface')
            ->getMock();
        $control->method('getName')->will($this->returnValue($controlKey));
        $arConfig = [
            'label' => 'label_' . mt_rand(),
            'controls' => [$control],
            'view' => 123,
        ];

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'view');
        $snippet = new \marvin255\bxcontent\snippets\Base($arConfig);
    }
}
