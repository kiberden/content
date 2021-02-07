# Bxcontent

[![Latest Stable Version](https://poser.pugx.org/marvin255/bxcontent/v/stable.png)](https://packagist.org/packages/marvin255/bxcontent)
[![License](https://poser.pugx.org/marvin255/bxcontent/license.svg)](https://packagist.org/packages/marvin255/bxcontent)
[![Build Status](https://travis-ci.org/marvin255/bxcontent.svg?branch=master)](https://travis-ci.org/marvin255/bxcontent)

Дополнительное поле для элементов инфоблока с возможностью создания сложных сниппетов со сложным html для контент менеджеров.



## Оглавление

* [Установка](#Установка).
* [Использование](#Использование).
* [Регистрация сниппетов в системе](#Регистрация-сниппетов-в-системе).
* [Доступные типы элементов управления](#Доступные-типы-элементов-управления).
* [Представление результата](#Представление-результата).
* [Добавление своих js и стилей](#Добавление-своих-js-и-стилей).
* [Сборки сниппетов](#Сборки-сниппетов).



## Установка

**С помощью [Composer](https://getcomposer.org/doc/00-intro.md)**

1. Добавьте в ваш composer.json в раздел `require`:

    ```javascript
    "require": {
        "marvin255/bxcontent": "~0.4"
    }
    ```

2. Если требуется автоматическое обновление библиотеки через composer, то добавьте в раздел `scripts`:

    ```javascript
    "scripts": [
        {
            "post-install-cmd": "\\marvin255\\bxcontent\\installer\\Composer::injectModule",
            "post-update-cmd": "\\marvin255\\bxcontent\\installer\\Composer::injectModule",
        }
    ]
    ```

3. Выполните в консоли внутри вашего проекта:

    ```
    composer update
    ```

4. Если пункт 2 не выполнен, то скопируйте папку `vendors/marvin255/bxcontent/marvin255.bxcontent` в папку `local/modules` вашего проекта.

5. Установите модуль в административном разделе 1С-Битрикс "Управление сайтом".

**Обычная**

1. Скачайте архив с репозиторием.
2. Скопируйте папку `marvin255.bxcontent` из архива репозитория в папку `local/modules` вашего проекта.
3. Установите модуль в административном разделе 1С-Битрикс "Управление сайтом".



## Использование

В административной части для управления инфоблоками появится новое свойство `Конструктор html`. Соответственно, чтобы использовать конструктор нужно создать для элементов нужного инфоблока пользовательское свойство типа `Конструктор html`. После этого на странице редактирования элемента появится конструктор.

Основная единица конструктора - сниппет. Каждый сниппет должен быть задан как объект, реализующий интерфейс `\marvin255\bxcontent\snippets\SnippetInterface`. Сниппет имеет два основных поля: название сниппета и массив с элементами управления.

В качестве элементов управления должны быть заданы объекты, реализующие интерфейс `\marvin255\bxcontent\controls\ControlInterface`. Для каждого элемента управления в обязательном порядке должны быть заданы: тип (передается в js для правильного отображения), имя (значение элемента управления будет создано и передано в базу данных под этим именем) и название (отобразится в интерфейсе). Кроме того, каждый элемент управления можно сделать множественным, если указать параметр `multiple` в таком случае элемент вернет в качестве значения массив и в конструкторе будет отображено несколько полей для данного элемента.

Один сниппет может содержать любое количество разных элементов управления. Соответственно, сниппет - это, по сути, коллекция элементов управления.

Для управления типами сниппетов в системе используется класс `\marvin255\bxcontent\SnippetManager`, с помощью которого можно получить все зарегистрированные сниппеты в системе и задать новые типы сниппетов. `\marvin255\bxcontent\SnippetManager` реализует шаблон singleton и доступен по вызову `\marvin255\bxcontent\SnippetManager::getInstance()`.



## Регистрация сниппетов в системе

Для того, чтобы сниппеты появились в системе, они должны быть зарегистрированы в `\marvin255\bxcontent\SnippetManager`. Для этого существует событие `collectSnippets`, в которое единственным параметром передается ссылка на объект `\marvin255\bxcontent\SnippetManager`. Для регистрации сниппета не обязательно создавать отдельный класс, можно воспользоваться общим классом `\marvin255\bxcontent\snippets\Base`, который поставляется вместе с модулем.

Пример регистрации сниппетов:

```php
AddEventHandler('marvin255.bxcontent', 'collectSnippets', 'collectSnippetsHandler');
function collectSnippetsHandler($manager)
{
    //сниппет с текстом и выпадающим списком
    $manager->set('text_select', new \marvin255\bxcontent\snippets\Base([
        'label' => 'Текст и выпадающий список',
        'controls' => [
            new \marvin255\bxcontent\controls\Editor([
                'name' => 'description',
                'label' => 'Текстовый редактор',
            ]),
            new \marvin255\bxcontent\controls\Select([
                'name' => 'class',
                'label' => 'Список',
                'prompt' => '-',
                'list' => [
                    'item1' => 'Опция 1',
                    'item2' => 'Опция 2',
                ],
            ]),
        ],
    ]));

    //сниппет со слайдером
    $manager->set('slider', new \marvin255\bxcontent\snippets\Base([
        'label' => 'Слайдер',
        'controls' => [
            new \marvin255\bxcontent\controls\Input([
                'name' => 'title',
                'label' => 'Заголовок слайдера',
            ]),
            new \marvin255\bxcontent\controls\Combine([
                'name' => 'slides',
                'label' => 'Слайды',
                'multiple' => true,
                'elements' => [
                    new \marvin255\bxcontent\controls\File([
                        'name' => 'image',
                        'label' => 'Файл с изображением',
                    ]),
                    new \marvin255\bxcontent\controls\Input([
                        'name' => 'sign',
                        'label' => 'Подпись',
                    ]),
                ],
            ]),
        ],
    ]));
}
```



## Доступные типы элементов управления

Поскольку все элементы управления обрабатываются на стороне js, то они должны быть описаны и в php и в js. Соответственно, их количество на данный момент ограничено:

1. `\marvin255\bxcontent\controls\Input` - обычная строка для ввода текста,

2. `\marvin255\bxcontent\controls\Editor` - wysiwyg-редактор,

3. `\marvin255\bxcontent\controls\Textarea` - поле для ввода нескольких строк текста (textarea),

4. `\marvin255\bxcontent\controls\File` - поле с возможностью выбрать или загрузить файл в файловую систему Битрикса,

5. `\marvin255\bxcontent\controls\Image` - поле с возможностью выбрать или загрузить изображение в файловую систему Битрикса,

6. `\marvin255\bxcontent\controls\Select` - поле с ограниченным количеством вариантов для выбора (select),

7. `\marvin255\bxcontent\controls\Combine` - поле с помощью которого можно скомбинировать несколько других полей.



## Представление результата

Результат может представляться в открытой части как с помощью обработки полученного json в каждом шаблоне, в котором выводится элемент с полями-конструкторами, так и более централизованно - с помощью представлений, указанных для сниппета.

При создании каждого сниппета можно указать объект, реализующий интерфейс `\marvin255\bxcontent\views\ViewInterface`, который будет отвечать за отображение данного конкретного сниппета.

```php
AddEventHandler('marvin255.bxcontent', 'collectSnippets', 'collectSnippetsHandler');
function collectSnippetsHandler($manager)
{
    //получаем объект приложения битрикса для отображения компонентов
    global $APPLICATION;
    //сниппет с текстом и выпадающим списком
    $manager->set('text_select', new \marvin255\bxcontent\snippets\Base([
        'view' => new \marvin255\bxcontent\views\Component($APPLICATION, 'custom:slider'),
        'label' => 'Текст и выпадающий список',
        'controls' => [
            new \marvin255\bxcontent\controls\Editor([
                'name' => 'description',
                'label' => 'Текстовый редактор',
            ]),
            new \marvin255\bxcontent\controls\Select([
                'name' => 'class',
                'label' => 'Список',
                'prompt' => '-',
                'list' => [
                    'item1' => 'Опция 1',
                    'item2' => 'Опция 2',
                ],
            ]),
        ],
    ]));
}
```

Соотвественно, для вывода сформированного для всех сниппетов html, достаточно будет вызвать метод `render` менеджера сниппетов. Например, для компонента `bitrix:news.detail` это будет выглядеть так:

```php
//template.php
echo \marvin255\bxcontent\SnippetManager::getInstance()->render($arResult['PROPERTIES']['html_constructor_property']['VALUE']);
```

либо, при условии, что свойство указано для отображения:

```php
//template.php
echo $arResult['DISPLAY_PROPERTIES']['html_constructor_property']['DISPLAY_VALUE'];
```



## Добавление своих js и стилей

Менеджер сниппетов обладает функционалом для добавления js и css.

Например, в событии можно указать не только специфический сниппет, но так же и скрипты и стили для него:

```php
AddEventHandler('marvin255.bxcontent', 'collectSnippets', 'collectSnippetsHandler');
function collectSnippetsHandler($manager)
{
    $manager->addJs('/bitrix/js/marvin255.bxcontent/controls.js');
    $manager->addCss('/bitrix/css/marvin255.bxcontent/plugin.css');
}
```



## Сборки сниппетов

Часть сниппетов заранее определена и добавлена в состав библиотеки. Сниппет из сборки должен наследовать классу `\marvin255\bxcontent\packs\Pack`. Такой сниппет может быть  добавлен к менеджеру с помощью функции `\marvin255\bxcontent\packs\Pack::setTo`, которая первым параметром принимает объект менеджера, а вторым - массив настроек для сниппета. Кроме того, сниппет из сборки имеет встроенные методы для отображения.

```php
use marvin255\bxcontent\packs\bootstrap;

AddEventHandler('marvin255.bxcontent', 'collectSnippets', 'collectSnippetsHandler');
function collectSnippetsHandler($manager)
{
    bootstrap\Carousel::setTo($manager, ['label' => 'кастомное название сниппета перепишет то, что задано в сниппете']);
    bootstrap\Collapse::setTo($manager);
    bootstrap\Media::setTo($manager);
    bootstrap\Tabs::setTo($manager);
}
```

**Сборка сниппетов для bootstrap**

`\marvin255\bxcontent\packs\bootstrap\Carousel` - [слайдер из bootsrap](https://getbootstrap.com/docs/3.3/javascript/#carousel).

`\marvin255\bxcontent\packs\bootstrap\Collapse` - [аккордеон из bootsrap](https://getbootstrap.com/docs/3.3/javascript/#collapse).

`\marvin255\bxcontent\packs\bootstrap\Tabs` - [закладки из bootsrap](https://getbootstrap.com/docs/3.3/javascript/#tabs).

`\marvin255\bxcontent\packs\bootstrap\Media` - [медиа объекты из bootsrap](https://getbootstrap.com/docs/3.3/components/#media).
