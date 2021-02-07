(function ($, document, window) {


    /**
     * Объект, который хранит в себе коллекцию других объектов, сохраненных
     * под определенными ключами.
     */
    var CollectionClass = function () {
        this._uniqueCounter = 0;
        this._collection = {};
    };

    CollectionClass.prototype.set = function (key, item) {
        this._collection[key] = item;
        return this;
    };

    CollectionClass.prototype.add = function (item) {
        var key = 'item_' + this._uniqueCounter++;
        this.set(key, item);
    };

    CollectionClass.prototype.get = function (key) {
        return this._collection[key] || null;
    };

    CollectionClass.prototype.remove = function (key) {
        delete this._collection[key];
        return this;
    };

    CollectionClass.prototype.setCollection = function (collection) {
        this._collection = {};
        for (var k in collection) {
            if (!collection.hasOwnProperty(k)) continue;
            this.set(k, collection[k]);
        }
    };

    CollectionClass.prototype.getCollection = function () {
        return this._collection;
    };

    CollectionClass.prototype.map = function (callback) {
        for (var k in this._collection) {
            if (!this._collection.hasOwnProperty(k)) continue;
            var res = callback.call(this, this._collection[k], k);
            if (res === false) break;
        }
        return this;
    };

    CollectionClass.prototype.search = function (item) {
        var key = null;
        this.map(function (collectionItem, collectionKey) {
            if (item === collectionItem) {
                key = collectionKey;
                return false;
            }
        });
        return key;
    };


    /**
     * Класс для сниппета.
     */
    var SnippetClass = function (type, settings, controlsFactory) {
        var self = this;
        var controls = new CollectionClass;

        //создаем контролы для текущего сниппета
        if (settings.controls) {
            for (var k in settings.controls) {
                if (!settings.controls.hasOwnProperty(k) || !settings.controls[k].type) continue;
                var control = controlsFactory.createInstance(settings.controls[k].type, settings.controls[k]);
                if (control) {
                    control.setParent(self);
                    controls.set(settings.controls[k].name, control);
                }
            }
        }

        self.settings = settings;
        self.settings.type = type;
        self.settings.controls = controls;
        self.name = '';

        self.getType = function () {
            return self.settings.type || '';
        };

        self.getLabel = function () {
            return self.settings.label || '';
        };

        self.getControls = function () {
            return self.settings.controls;
        };

        self.getName = function () {
            var parent = self.getParent();
            var name = '';
            if (parent.getName) {
                name = parent.getName();
            }
            if (self.name) {
                name = name ? name + '[' + self.name + ']' : self.name;
            }
            return name;
        };

        self.setName = function (name) {
            self.name = name;
            return self;
        };

        self.setValue = function (value) {
            if (!value) return;
            self.getControls().map(function (control, key) {
                control.setValue(value[key] || null);
            });
        };

        self.setParent = function (parent) {
            self.parent = parent;
        };

        self.getParent = function () {
            return self.parent;
        };
    };


    /**
     * Коллекция из нескольких сниппетов.
     */
    var SnippetCollectionClass = function (name) {
        CollectionClass.apply(this, []);
        this.name = name;
    };
    SnippetCollectionClass.prototype = Object.create(CollectionClass.prototype);
    SnippetCollectionClass.prototype.constructor = SnippetCollectionClass;
    SnippetCollectionClass.prototype.getName = function () {
        return this.name;
    };
    SnippetCollectionClass.prototype.set = function (key, item) {
        item.setParent(this);
        item.setName(key);
        CollectionClass.prototype.set.apply(this, arguments);
        return this;
    };


    /**
     * Фабричный класс для создания новых объектов контролов.
     */
    var ControlFactoryClass = function () {
        CollectionClass.apply(this, []);
    };
    ControlFactoryClass.prototype = Object.create(CollectionClass.prototype);
    ControlFactoryClass.prototype.constructor = ControlFactoryClass;
    ControlFactoryClass.prototype.createInstance = function (type, settings) {
        var item = this.get(type);
        if (!item) return null;
        return new item($.extend({}, settings), this);
    };


    /**
     * Фабричный класс для создания новых объектов сниппетов.
     */
    var SnippetFactoryClass = function (controlsFactory) {
        CollectionClass.apply(this, []);
        this.controlsFactory = controlsFactory;
    };
    SnippetFactoryClass.prototype = Object.create(CollectionClass.prototype);
    SnippetFactoryClass.prototype.constructor = SnippetFactoryClass;
    SnippetFactoryClass.prototype.createInstance = function (type) {
        var item = this.get(type);
        if (!item) return null;
        return new SnippetClass(type, $.extend({}, item), this.controlsFactory);
    };


    /**
     * Представление для блока сниппетов.
     */
    var SnippetCollectionViewClass = function ($domBlock, collection, snippetsFactory, options) {
        var self = this;

        self.domBlock = $($domBlock);
        self.collection = collection;
        self.renderedCollection = new CollectionClass;
        self.snippetsFactory = snippetsFactory;
        self.options = options;

        self.render = function () {
            self.renderSnippets();
            self.renderSelector();
        };

        self.renderSnippets = function () {
            var $snippetsBlock = self.domBlock.find('.marvin255bxcontent-snippets-block');
            if (!$snippetsBlock.length) {
                $snippetsBlock = $('<div class="marvin255bxcontent-snippets-block" />').appendTo(self.domBlock);
            }
            //отображает сниппеты, которые есть в коллекции, но которых нет на форме
            self.collection.map(function (item, key) {
                if (!self.renderedCollection.get(key)) {
                    var $snippetBlock = $('<div class="marvin255bxcontent-snippets-snippet" />').appendTo($snippetsBlock);
                    self.renderedCollection.set(key, $snippetBlock);
                    self.renderSnippet($snippetBlock, item, key);
                }
            });
            //убирает сниппеты, которые отображены на форме, но которых нет в коллекции
            self.renderedCollection.map(function (item, key) {
                if (!self.collection.get(key)) {
                    item.empty().remove();
                }
            });
        };

        self.renderSnippet = function ($block, snippet, key) {
            //скрытое поле с типом данного сниппета
            var $typeHidden = $block.children('input[type=hidden]');
            if (!$typeHidden.length) {
                var typeHiddenName = snippet.getName() + '[type]';
                var $typeHidden = $('<input type="hidden" name="' + typeHiddenName + '" value="' + snippet.getType() + '">').appendTo($block);
            }

            //заголовок сниппета
            var $header = $block.find('.marvin255bxcontent-snippets-snippet-header');
            if (!$header.length) {
                $header = $('<div class="marvin255bxcontent-snippets-snippet-header" />').text(snippet.getLabel).appendTo($block);
            }

            //кнопка удаления сниппета
            var $remove = $block.find('.marvin255bxcontent-snippets-snippet-remove');
            if (!$remove.length) {
                $remove = $('<button class="marvin255bxcontent-snippets-snippet-remove" />').text('Удалить').appendTo($block);
            }
            $remove.off('click').on('click', function () {
                self.collection.remove(key);
                self.renderSnippets();
                return false;
            });

            //кнопки для перемещения сниппета
            var $moveButtons = $block.find('.marvin255bxcontent-snippets-snippet-move');
            if ($moveButtons.length) {
                $moveUp = $moveButtons.find('marvin255bxcontent-snippets-snippet-move-up');
                $moveDown = $moveButtons.find('marvin255bxcontent-snippets-snippet-move-down');
            } else {
                $moveButtons = $('<div class="marvin255bxcontent-snippets-snippet-move" />').appendTo($block);
                $moveUp = $('<button class="marvin255bxcontent-snippets-snippet-move-up" />').text('Выше').appendTo($moveButtons);
                $moveDown = $('<button class="marvin255bxcontent-snippets-snippet-move-down" />').text('Ниже').appendTo($moveButtons);
            }
            $moveUp.off('click').on('click', function () {
                var $upperSibling = $block.prev();
                if ($upperSibling.length) {
                    $block.detach().insertBefore($upperSibling);
                    $(document).trigger( "updateBitrixScripts" );
                }
                return false;
            });
            $moveDown.off('click').on('click', function () {
                var $lowerSibling = $block.next();
                if ($lowerSibling.length) {
                    $block.detach().insertAfter($lowerSibling);
                    $(document).trigger( "updateBitrixScripts" );
                }
                return false;
            });

            //контролы
            var $controls = $block.find('.marvin255bxcontent-snippets-snippet-controls');
            if (!$controls.length) {
                $controls = $('<div class="marvin255bxcontent-snippets-snippet-controls" />').appendTo($block);
            } else {
                $controls.empty();
            }
            snippet.getControls().map(function (item, key) {
                var $controlArea = $('<div class="marvin255bxcontent-snippets-snippet-controls-control" />').appendTo($controls);
                var $label = $('<div class="marvin255bxcontent-snippets-snippet-controls-control-label" />').text(item.getLabel()).appendTo($controlArea);
                var $control = $('<div class="marvin255bxcontent-snippets-snippet-controls-control-container" />').appendTo($controlArea);
                item.render($control);
            });
        };

        self.renderSelector = function () {
            var $selectorBlock = self.domBlock.find('.marvin255bxcontent-selector-block');
            if (!$selectorBlock.length) {
                $selectorBlock = $('<div class="marvin255bxcontent-selector-block" />').appendTo(self.domBlock);
            }

            var typesOptions = '<option value="">Выберите тип блока</option>';
            self.snippetsFactory.map(function (item, key) {
                if (self.options && self.options.allowed_snippets && $.inArray(key, self.options.allowed_snippets) === -1) {
                    return true;
                }
                typesOptions += '<option value="' + key + '">' + item.label + '</option>';
            });
            var $select = $selectorBlock.find('select');
            if (!$select.length) {
                $select = $('<select />').html(typesOptions).appendTo($selectorBlock);
            } else if ($select.html() !== typesOptions) {
                $select.html(typesOptions);
            }

            var $button = $selectorBlock.find('button');
            if (!$button.length) {
                $button = $('<button />').text('Добавить блок').appendTo($selectorBlock);
            }
            $button.off('click').on('click', function () {
                var type = $select.val();
                var snippet = type ? self.snippetsFactory.createInstance(type) : null;
                if (snippet) {
                    self.collection.add(snippet);
                    self.renderSnippets();
                }
                $select.val('');
                return false;
            });
        };

        self.destroy = function () {
            self.domBlock.empty().remove();
            delete self.domBlock;
            delete self.collection;
            delete self.renderedCollection;
            delete self.snippetsFactory;
        };
    };


    /**
     * Инициируем объекты фабрики для сниппетов и контролов.
     */
    var controlsFactory = new ControlFactoryClass(),
        snippetsFactory = new SnippetFactoryClass(controlsFactory);


    /**
     * Методы jquery плагина.
     */
    var methods = {
        //добавляет новый тип контрола в фабричный объект контролов
        'registerControl': function (type, construct) {
            return controlsFactory.set(type, construct);
        },
        //добавляет новый тип сниппета в фабричный объект сниппетов
        'registerSnippet': function (type, settings) {
            return snippetsFactory.set(type, settings);
        },
        //добавляет новые типы сниппетов из объекта
        'registerSnippets': function (snippets) {
            return snippetsFactory.setCollection(snippets);
        },
        //инициирует плагин на выбранных элементах
        'init': function (options) {
            return this.filter('textarea').each(function (i) {
                var $textarea = $(this);
                var $snippetsBlock = $('<div />').insertAfter($textarea);
                var collection = new SnippetCollectionClass($textarea.attr('name'));
                var view = new SnippetCollectionViewClass($snippetsBlock, collection, snippetsFactory, options);
                var defaultValues = $textarea.val() ? JSON.parse($textarea.val()) : null;
                if (defaultValues) {
                    $.each(defaultValues, function (key, value) {
                        var snippetValue = $.extend({}, value);
                        var snippet = snippetValue.type ? snippetsFactory.createInstance(snippetValue.type) : null;
                        if (!snippet) return;
                        snippet.setValue(snippetValue);
                        collection.add(snippet);
                    });
                    $textarea.val('');
                }
                view.render();
                $textarea.data('marvin255bxcontent_viewer', view);
            });
        },
    };


    /**
     * Регистрация jquery плагина.
     */
    $.fn.marvin255bxcontent = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if(typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        }
        return false;
    };


    $(document).on('ready', function () {
        $('.marvin255bxcontent-init').marvin255bxcontent();
    });

})(jQuery, document, window);
