(function ($, document, window) {

    /**
     * Счетчик для уникального идентификатора
     */
    var _uniqueId = 0;
    /**
     * Возвращает уникальный идентификатор
     */
    var getUniqueId = function () {
        _uniqueId++;
        return 'control_unique_' + _uniqueId;
    };

    /**
     * Массив скриптов, которыми инициируются поля битрикса
     */
    var BitrixScripts = [];
    $(document).on( "updateBitrixScripts", function( event ) {
        BitrixScripts.forEach(function(script) {
            eval(script);
        });
    })


    /**
     * Базовый класс поля со всем необходимым для поля функционалом
     */
    var BaseControlClass = function (settings, controlsFactory) {
        this.settings = settings;
        this.controlsFactory = controlsFactory;
        this.parent = null;
        this.value = null;
        this.uniqueCounter = 0;
    };

    BaseControlClass.prototype.getLabel = function () {
        return this.settings.label || null;
    };

    BaseControlClass.prototype.isMultiple = function () {
        return this.settings.multiple ? true : false;
    };

    BaseControlClass.prototype.setParent = function (parent) {
        this.parent = parent;
        return this;
    };

    BaseControlClass.prototype.getParent = function () {
        return this.parent || null;
    };

    BaseControlClass.prototype.setValue = function (value) {
        this.value = value;
        return this;
    };

    BaseControlClass.prototype.getValue = function () {
        return this.value || null;
    };

    BaseControlClass.prototype.getName = function (parent) {
        var settingsName = this.settings.name || null;
        if (settingsName) {
            var parent = this.getParent();
            if (parent && parent.getName) {
                settingsName = parent.getName() + '[' + settingsName + ']';
            }
        }
        return settingsName;
    };

    BaseControlClass.prototype.render = function ($block) {
        $block.empty();
        if (this.isMultiple()) {

            var snippet = this;

            var $items = $block.find('marvin255bxcontent-snippets-snippet-multiple-items');
            if (!$items.length) {
                $items = $('<div class="marvin255bxcontent-snippets-snippet-multiple-items" />').appendTo($block);
                var value = this.getValue();
                if (value) {
                    $.each(value, function (key, item) {
                        snippet._renderMultiple($items, item);
                    });
                }
            }

            var $addButton = $block.find('marvin255bxcontent-snippets-snippet-multiple-add');
            if (!$addButton.length) {
                $addButton = $('<button class="marvin255bxcontent-snippets-snippet-multiple-add" />').text('Добавить новый элемент').appendTo($block);
            }
            $addButton.off('click').on('click', function () {
                snippet._renderMultiple($items);
                return false;
            });

        } else {
            this._renderInternal($block, this.getName(), this.getValue());
        }
    };

    BaseControlClass.prototype._renderMultiple = function ($controlBlock, value) {
        var $block = $('<div class="marvin255bxcontent-snippets-snippet-multiple-items-item" />').appendTo($controlBlock);
        var name = this.getName() + '[' + this.uniqueCounter++ + ']';

        var $del = $('<button class="marvin255bxcontent-snippets-snippet-multiple-items-delete" />').text('Удалить').appendTo($block);
        $del.off('click').on('click', function () {
            $block.empty().remove();
        });

        this._renderInternal($block, name, value);
    }

    BaseControlClass.prototype._renderInternal = function ($block, name, value) {
        var $input = $('<input type="text" />').appendTo($block);
        $input.attr('name', name);
        if (typeof value !== 'undefined') {
            $input.val(value);
        }
    };



    /**
     * Определение текстового поля
     */
    var InputClass = function (settings, controlsFactory) {
        BaseControlClass.apply(this, [settings, controlsFactory]);
    };
    InputClass.prototype = Object.create(BaseControlClass.prototype);
    InputClass.prototype.constructor = InputClass;

    //регистрируем поле
    $.fn.marvin255bxcontent('registerControl', 'input', InputClass);


    /**
     * Определение поля с textarea
     */
    var TextareaClass = function (settings, controlsFactory) {
        BaseControlClass.apply(this, [settings, controlsFactory]);
    };
    TextareaClass.prototype = Object.create(BaseControlClass.prototype);
    TextareaClass.prototype.constructor = TextareaClass;
    TextareaClass.prototype._renderInternal = function ($block, name, value) {
        var $input = $('<textarea />').appendTo($block);
        $input.attr('name', name);
        if (typeof value !== 'undefined') {
            $input.val(value);
        }
    };

    //регистрируем поле
    $.fn.marvin255bxcontent('registerControl', 'textarea', TextareaClass);


    /**
     * Определение поля с файлом
     */
    var FileClass = function (settings, controlsFactory) {
        BaseControlClass.apply(this, [settings, controlsFactory]);
    };
    FileClass.prototype = Object.create(BaseControlClass.prototype);
    FileClass.prototype.constructor = FileClass;
    FileClass.prototype.getBxFileJs = function (id) {
        var script = '';
        if (this.settings.template) {
            var tpl = this.settings.template;
            script = tpl.replace(/_____elementId_____/g, id).replace(/_____clickEvent_____/g, id + 'Click');
        }
        return script;
    };
    FileClass.prototype._renderInternal = function ($block, name, value) {
        $block.addClass('marvin255bxcontent-control-file-bitrix');
        var id = getUniqueId();
        var $input = $('<input type="text" id="' + id + '" name="' + name + '" />').appendTo($block);
        var $button = $('<button type="button" onclick="' + id + 'Click(); return false;" />').text('Загрузить файл').appendTo($block);
        if (typeof value !== 'undefined') {
            $input.val(value);
        }
        var bxJs = this.getBxFileJs(id);
        $(bxJs).filter('script').each(function (key) {
            eval(this.innerHTML);
        });
    };

    //регистрируем поле
    $.fn.marvin255bxcontent('registerControl', 'file', FileClass);


    /**
     * Определение поля с изображением
     */
    var ImageClass = function (settings, controlsFactory) {
        BaseControlClass.apply(this, [settings, controlsFactory]);
    };
    ImageClass.prototype = Object.create(BaseControlClass.prototype);
    ImageClass.prototype.constructor = ImageClass;
    ImageClass.prototype.getBxFileJs = FileClass.prototype.getBxFileJs;
    ImageClass.prototype._renderInternal = function ($block, name, value) {
        $block.addClass('marvin255bxcontent-control-file-bitrix');
        var self = this;
        var id = getUniqueId();
        var $input = $('<input type="text" id="' + id + '" name="' + name + '" />').appendTo($block);
        var $button = $('<button type="button" onclick="' + id + 'Click(); return false;" />').text('Загрузить изображение').appendTo($block);
        $input.on('change', function () {
            var $this = $(this);
            var $image = $this.parent().find('img');
            if ($this.val()) {
                if (!$image.length) {
                    $image = $('<img />').insertAfter($button);
                    if (self.settings.width) {
                        $image.css('max-width', self.settings.width);
                    }
                    if (self.settings.height) {
                        $image.css('max-height', self.settings.height);
                    }
                }
                $image.attr('src', $this.val());
            } else if ($image.length) {
                $image.remove();
            }
        });
        if (typeof value !== 'undefined') {
            $input.val(value);
            $input.trigger('change');
        }
        var bxJs = this.getBxFileJs(id);
        $(bxJs).filter('script').each(function (key) {
            eval(this.innerHTML);
        });
    };

    //регистрируем поле
    $.fn.marvin255bxcontent('registerControl', 'image', ImageClass);


    /**
     * Определение поля с редактором
     */
    var EditorClass = function (settings, controlsFactory) {
        BaseControlClass.apply(this, [settings, controlsFactory]);
    };
    EditorClass.prototype = Object.create(BaseControlClass.prototype);
    EditorClass.prototype.constructor = EditorClass;
    EditorClass.prototype._renderInternal = function ($block, name, value) {
        var $input = $('<textarea />').appendTo($block);
        $input.attr('name', name);
        if (typeof value !== 'undefined') {
            $input.val(value);
        }
    };
    EditorClass.prototype.getBxFileJs = function (id, name) {
        var script = '';
        if (this.settings.template) {
            var tpl = this.settings.template;
            script = tpl.replace(/(<textarea.+name=\")_____name_____\"/g, '$1' + name + '"');
            script = script.replace(/("inputName":")_____name_____\"/g, '$1' + name + '"');
            script = script.replace(/_____name_____/g, id).replace(/_____type_____/g, id + '_value_type');
        }
        return script;
    };
    EditorClass.prototype._renderInternal = function ($block, name, value) {
        $block.addClass('marvin255bxcontent-control-file-bitrix');
        var id = getUniqueId();
        var $input = $('<div />').appendTo($block);
        var bxJs = this.getBxFileJs(id, name);
        $(bxJs).appendTo($input);
        $input.find('textarea').val(value);
        BitrixScripts.push($(bxJs).filter('script').last().get(0).innerHTML);
        $(bxJs).filter('script').each(function (key) {
            eval(this.innerHTML);
        });
    };

    //регистрируем поле
    $.fn.marvin255bxcontent('registerControl', 'editor', EditorClass);


    /**
     * Определение поля со списком
     */
    var SelectClass = function (settings, controlsFactory) {
        BaseControlClass.apply(this, [settings, controlsFactory]);
    };
    SelectClass.prototype = Object.create(BaseControlClass.prototype);
    SelectClass.prototype.constructor = SelectClass;
    SelectClass.prototype._renderMultiple = function ($controlBlock, value) {
        var name = this.getName() + '[]';
        this._renderInternal($controlBlock, name, value);
    };
    SelectClass.prototype._renderInternal = function ($block, name, value) {
        var html = '';
        if (this.settings.list) {
            if (this.settings.prompt) {
                html += '<option value="">' + this.settings.prompt + '</option>';
            }
            $.each(this.settings.list, function (key, value) {
                html += '<option value="' + key + '">' + value + '</option>';
            });
        }
        var $input = $('<select />').html(html).appendTo($block);
        $input.attr('name', name);
        if (typeof value !== 'undefined') {
            $input.val(value);
        }
    };

    //регистрируем поле
    $.fn.marvin255bxcontent('registerControl', 'select', SelectClass);


    /**
     * Определение составного поля
     */
    var CombineClass = function (settings, controlsFactory) {
        BaseControlClass.apply(this, [settings, controlsFactory]);
    };
    CombineClass.prototype = Object.create(BaseControlClass.prototype);
    CombineClass.prototype.constructor = CombineClass;
    CombineClass.prototype._renderInternal = function ($block, name, value) {
        if (!this.settings.elements) return;
        var controlsFactory = this.controlsFactory;
        var controls = [];
        $.each(this.settings.elements, function (key, element) {
            if (!element.type) return;
            var $cobineBlock = $('<div class="marvin255bxcontent-snippets-snippet-combine-item" />').appendTo($block);
            var $cobineLabel = $('<div class="marvin255bxcontent-snippets-snippet-combine-item-label" />').appendTo($cobineBlock);
            var $cobineInput = $('<div class="marvin255bxcontent-snippets-snippet-combine-item-input" />').appendTo($cobineBlock);
            var settings = $.extend({}, element);
            settings.name = name + '[' + element.name + ']';
            var control = controlsFactory.createInstance(settings.type, settings);
            if (value && value[element.name]) {
                control.setValue(value[element.name]);
            }
            $cobineLabel.text(control.getLabel());
            control.render($cobineInput);
        });
    };

    //регистрируем поле
    $.fn.marvin255bxcontent('registerControl', 'combine', CombineClass);


})(jQuery, document, window);
