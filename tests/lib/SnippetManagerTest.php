<?php

namespace marvin255\bxcontent\tests\lib;

class SnippetManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetListInitializationCallbackAlreadyInitializedException()
    {
        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);
        $manager->getSnippetsList();

        $this->setExpectedException('\marvin255\bxcontent\Exception');
        $manager->setListInitializationCallback(function ($manager) {});
    }

    public function testSetListInitializationCallbackNotCallableException()
    {
        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);

        $this->setExpectedException('\marvin255\bxcontent\Exception');
        $manager->setListInitializationCallback(123);
    }

    public function testSetListInitializationCallback()
    {
        $name = 'type_' . mt_rand();
        $snippet = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')
            ->getMock();

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);
        $manager->setListInitializationCallback(function ($manager) use ($name, $snippet) {
            $manager->set($name, $snippet);
        });

        $this->assertSame($snippet, $manager->get($name));
        $this->assertSame([$name => $snippet], $manager->getSnippetsList());
    }

    public function testSet()
    {
        $name = 'type_' . mt_rand();
        $name1 = 'type_2_' . mt_rand();
        $snippet = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')
            ->getMock();

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);

        $this->assertSame(
            $manager,
            $manager->set($name, $snippet)
        );
        $this->assertSame(
            $snippet,
            $manager->get($name)
        );
        $this->assertSame(
            null,
            $manager->get($name1)
        );
    }

    public function testSetEmptySnippetNameException()
    {
        $snippet = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')
            ->getMock();

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);

        $this->setExpectedException('\marvin255\bxcontent\Exception', 'name');
        $manager->set('', $snippet);
    }

    public function testRemove()
    {
        $name = 'type_' . mt_rand();
        $snippet = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')
            ->getMock();

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);

        $manager->set($name, $snippet);
        $this->assertSame(
            $manager,
            $manager->remove($name)
        );
        $this->assertSame(
            null,
            $manager->get($name)
        );
    }

    public function testRemoveEmptySnippetException()
    {
        $name = 'type_' . mt_rand();
        $snippet = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')
            ->getMock();

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);

        $this->setExpectedException('\marvin255\bxcontent\Exception', $name);
        $manager->remove($name);
    }

    public function testGetSnippetsList()
    {
        $name = 'type_' . mt_rand();
        $name1 = 'type_1_' . mt_rand();
        $name2 = 'type_2_' . mt_rand();
        $snippet = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')
            ->getMock();
        $snippet1 = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')
            ->getMock();
        $snippet2 = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')
            ->getMock();

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);

        $this->assertSame(
            [],
            $manager->getSnippetsList()
        );

        $manager->set($name, $snippet);
        $manager->set($name2, $snippet2);
        $manager->set($name1, $snippet1);
        $manager->remove($name);

        $toCheck = $manager->getSnippetsList();
        ksort($toCheck);
        $etalon = [$name1 => $snippet1, $name2 => $snippet2];
        ksort($etalon);

        $this->assertSame(
            $etalon,
            $toCheck
        );
    }

    public function testJsonSerialize()
    {
        $name = 'type_' . mt_rand();
        $controls = ['control_' . mt_rand()];
        $label = 'label_' . mt_rand();

        $etalon = [
            $name => [
                'label' => $label,
                'controls' => $controls,
            ],
        ];
        ksort($etalon[$name]);

        $snippet = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')->getMock();
        $snippet->method('getControls')->will($this->returnValue($controls));
        $snippet->method('getLabel')->will($this->returnValue($label));

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);

        $return = $manager->set($name, $snippet)->jsonSerialize();
        foreach ($return as &$item) {
            ksort($item);
        }
        $this->assertSame(
            $etalon,
            $return
        );
    }

    public function testSetJs()
    {
        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);
        $manager->addJs('test3');

        $this->assertSame(
            $manager,
            $manager->setJs(['test1', 'test2'])
        );
        $this->assertSame(
            ['test1', 'test2'],
            $manager->getJs()
        );
    }

    public function testAddJs()
    {
        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);

        $this->assertSame(
            $manager,
            $manager->addJs('test1')
        );
        $manager->addJs('test2');
        $this->assertSame(
            ['test1', 'test2'],
            $manager->getJs()
        );
    }

    public function testAddJsEmptyNameException()
    {
        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);
        $this->setExpectedException('\marvin255\bxcontent\Exception');
        $manager->addJs('');
    }

    public function testSetCss()
    {
        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);
        $manager->addCss('test3');

        $this->assertSame(
            $manager,
            $manager->setCss(['test1', 'test2'])
        );
        $this->assertSame(
            ['test1', 'test2'],
            $manager->getCss()
        );
    }

    public function testAddCss()
    {
        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);

        $this->assertSame(
            $manager,
            $manager->addCss('test1')
        );
        $manager->addCss('test2');
        $this->assertSame(
            ['test1', 'test2'],
            $manager->getCss()
        );
    }

    public function testAddCssEmptyNameException()
    {
        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);
        $this->setExpectedException('\marvin255\bxcontent\Exception');
        $manager->addCss('');
    }

    public function testRegisterAssets()
    {
        $parameterName = 'parameter_' . mt_rand();
        $name = 'type_' . mt_rand();
        $controls = ['control_' . mt_rand()];
        $label = 'label_' . mt_rand();
        $js1 = 'js_' . mt_rand();
        $js2 = 'js_2_' . mt_rand();
        $css1 = 'css_' . mt_rand();
        $css2 = 'css_2_' . mt_rand();

        $etalon = [
            $name => [
                'label' => $label,
                'controls' => $controls,
            ],
        ];

        $snippet = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')->getMock();
        $snippet->method('getControls')->will($this->returnValue($controls));
        $snippet->method('getLabel')->will($this->returnValue($label));

        $asset = $this->getMockBuilder('\Bitrix\Main\Page\Asset')
            ->setMethods(['addString', 'addJs', 'addCss'])
            ->getMock();
        $asset->expects($this->at(0))
            ->method('addJs')
            ->with($this->stringContains($js1), $this->equalTo(true));
        $asset->expects($this->at(1))
            ->method('addJs')
            ->with($this->stringContains($js2), $this->equalTo(true));
        $asset->expects($this->at(2))
            ->method('addString')
            ->with($this->stringContains($css1), $this->equalTo(true));
        $asset->expects($this->at(3))
            ->method('addString')
            ->with($this->stringContains($css2), $this->equalTo(true));
        $asset->expects($this->at(4))
            ->method('addString')
            ->with($this->stringContains(json_encode($etalon)), $this->equalTo(true));

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);

        $manager->addJs($js1)->addJs($js2);
        $manager->addCss($css1)->addCss($css2);

        $manager->set($name, $snippet)->registerAssets($asset, $parameterName);
    }

    public function testRender()
    {
        $toRender = [
            ['type' => 'snippet_1', 'key' => 'value_' . mt_rand()],
            ['type' => 'snippet_2', 'key_2' => 'value_2_' . mt_rand()],
            ['type' => 'snippet_3', 'key_3' => 'value_3_' . mt_rand()],
        ];
        $return1 = 'return_' . mt_rand();
        $return3 = 'return_3_' . mt_rand();

        $snippet = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')->getMock();
        $snippet->method('render')
            ->with($this->equalTo(['key' => $toRender[0]['key']]))
            ->will($this->returnValue($return1));

        $snippet3 = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')->getMock();
        $snippet3->method('render')
            ->with($this->equalTo(['key_3' => $toRender[2]['key_3']]))
            ->will($this->returnValue($return3));

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);
        $manager->set('snippet_3', $snippet3);
        $manager->set('snippet_1', $snippet);

        $this->assertSame(
            $return1 . $return3,
            $manager->render($toRender)
        );
    }

    public function testRenderSearchable()
    {
        $toRender = [
            ['type' => 'snippet_1', 'key' => 'value_' . mt_rand()],
            ['type' => 'snippet_2', 'key_2' => 'value_2_' . mt_rand()],
            ['type' => 'snippet_3', 'key_3' => 'value_3_' . mt_rand()],
        ];
        $return1 = 'return_' . mt_rand();
        $return3 = 'return_3_' . mt_rand();

        $snippet = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')->getMock();
        $snippet->method('render')
            ->with($this->equalTo(['key' => $toRender[0]['key']]))
            ->will($this->returnValue($return1));
        $snippet->method('isSearchable')
            ->will($this->returnValue(false));

        $snippet3 = $this->getMockBuilder('\marvin255\bxcontent\snippets\SnippetInterface')->getMock();
        $snippet3->method('render')
            ->with($this->equalTo(['key_3' => $toRender[2]['key_3']]))
            ->will($this->returnValue($return3));
        $snippet3->method('isSearchable')
            ->will($this->returnValue(true));

        $manager = \marvin255\bxcontent\SnippetManager::getInstance(true);
        $manager->set('snippet_3', $snippet3);
        $manager->set('snippet_1', $snippet);

        $this->assertSame($return3, $manager->render($toRender, true));
        $this->assertSame($return1, $manager->render($toRender, false));
        $this->assertSame($return1 . $return3, $manager->render($toRender));
    }
}
