<?php

namespace marvin255\bxcontent\tests\lib\packs\bootstrap;

class TabsTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConfig()
    {
        $testLabel = 'label_' . mt_rand();

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);
        \marvin255\bxcontent\packs\bootstrap\Tabs::setTo($manager, ['label' => $testLabel]);

        $this->assertInstanceOf(
            '\marvin255\bxcontent\packs\bootstrap\Tabs',
            $manager->get('bootstrap.tabs')
        );

        $this->assertSame(
            $testLabel,
            $manager->get('bootstrap.tabs')->getLabel()
        );
    }

    public function testRenderView()
    {
        $renderArray = ['key_' . mt_rand() => 'value_' . mt_rand()];

        $view = $this->getMockBuilder('\marvin255\bxcontent\views\ViewInterface')
            ->setMethods(['render'])
            ->getMock();
        $view->expects($this->once())
            ->method('render')
            ->with($this->equalTo($renderArray));

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);
        \marvin255\bxcontent\packs\bootstrap\Tabs::setTo($manager, ['view' => $view]);
        $manager->get('bootstrap.tabs')->render($renderArray);
    }

    public function testRenderInternal()
    {
        $renderArray = [
            'items' => [
                0 => [
                    'text' => 'text_' . mt_rand(),
                    'caption' => 'caption_' . mt_rand(),
                ],
                1 => [
                    'text' => 'text_1_' . mt_rand(),
                    'caption' => 'caption_1_' . mt_rand(),
                ],
                2 => [
                    'text' => 'text_2_' . mt_rand(),
                ],
            ],
        ];

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);
        \marvin255\bxcontent\packs\bootstrap\Tabs::setTo($manager);

        $rendered = $manager->get('bootstrap.tabs')->render($renderArray);

        $this->assertContains(
            $renderArray['items'][0]['caption'],
            $rendered
        );
        $this->assertContains(
            $renderArray['items'][0]['text'],
            $rendered
        );

        $this->assertContains(
            $renderArray['items'][1]['caption'],
            $rendered
        );
        $this->assertContains(
            $renderArray['items'][1]['text'],
            $rendered
        );

        $this->assertNotContains(
            $renderArray['items'][2]['text'],
            $rendered
        );
    }
}
