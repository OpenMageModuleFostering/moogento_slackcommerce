ko.bindingHandlers.chosen = {
    init: function(element, valueAccessor, allBindings) {
        var $element = jQuery(element);
        var options = ko.unwrap(valueAccessor());
        setTimeout(function(){
            if (typeof options === 'object') {
                $element.chosen(options);
            } else {
                $element.chosen();
            }
        }, 0);
        ['options', 'selectedOptions', 'value', 'disable'].forEach(function(propName){
            if (allBindings.has(propName)){
                var prop = allBindings.get(propName);
                if (ko.isObservable(prop)){
                    prop.subscribe(function(){
                        setTimeout(function() {
                            $element.trigger('chosen:updated');
                        }, 0);
                    });
                }
            }
        });
    },
    update: function(element) {
        var $element = jQuery(element);
        $element.trigger('chosen:updated');
    }
};

ko.bindingHandlers.switch = {
    init: function(element, valueAccessor, allBindings) {
        var $element = jQuery(element);
        $element.switchButton({
            show_labels: false,
            on_callback: function() {
                var value = valueAccessor();
                value(1);
            },
            off_callback: function() {
                var value = valueAccessor();
                value(0);
            }
        });
        ['disable'].forEach(function(propName){
            if (allBindings.has(propName)){
                var prop = allBindings.get(propName);
                if (ko.isObservable(prop)){
                    prop.subscribe(function(newVal){
                        setTimeout(function() {
                            console.log(newVal);
                            if (newVal) {
                                $element.switchButton('instance').disable();
                            } else {
                                $element.switchButton('instance').enable();
                            }
                        }, 0);
                    });
                }
            }
        });

    }
};

ko.bindingHandlers.datepicker = {
    init: function(element, valueAccessor, allBindingsAccessor) {

        var options, value, subscription, origOnSelect;

        options = valueAccessor();
        value = ko.utils.unwrapObservable(options.value);

        jQuery(element).datepicker(options);

        if (value) {
            jQuery(element).datepicker('setDate', value);
        }

        if (ko.isObservable(options.value)) {
            subscription = options.value.subscribe(function (newValue) {
                jQuery(element).datepicker('setDate', newValue);
            });

            ko.utils.domNodeDisposal.addDisposeCallback(element, function () {
                subscription.dispose();
            });
        }

        if (ko.isWriteableObservable(options.value)) {
            origOnSelect = jQuery(element).datepicker('option', 'onSelect');
            jQuery(element).datepicker('option', 'onSelect', function (selectedText) {
                var format, date;

                format = jQuery(element).datepicker('option', 'dateFormat');
                date = jQuery.datepicker.parseDate(format, selectedText);
                options.value(date);

                if (typeof origOnSelect === 'function') {
                    origOnSelect.apply(this, Array.prototype.slice.call(arguments));
                }
            });
        }
    }
};

ko.bindingHandlers.color = {
    init: function(element, valueAccessor) {
        var myColor = new jscolor.color(element);
    }
};