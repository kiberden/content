<?php

namespace marvin255\bxcontent\tests\lib\snippets;

class ComponentTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $component = 'component_' . mt_rand();
        $template = 'template_' . mt_rand();
        $renderData = [
            'view_key_' . mt_rand() => 'view_value_' . mt_rand(),
            'view_key_1_' . mt_rand() => 'view_value_1_' . mt_rand(),
        ];
        $rendered = 'rendered_' . mt_rand();

        $application = $this->getMockBuilder('\CMain')
            ->setMethods(['IncludeComponent'])
            ->getMock();
        $application->method('IncludeComponent')
            ->with($this->equalTo($component), $this->equalTo($template), $this->equalTo($renderData))
            ->will($this->returnCallback(function () use ($rendered) {
                echo $rendered;
            }));

        $view = new \marvin255\bxcontent\views\Component(
            $application,
            $component,
            $template
        );

        $this->assertSame(
            $rendered,
            $view->render($renderData)
        );
    }
}
