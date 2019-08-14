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
        ['options', 'selectedOptions', 'value'].forEach(function(propName){
            if (allBindings.has(propName)){
                var prop = allBindings.get(propName);
                if (ko.isObservable(prop)){
                    prop.subscribe(function(){
                        $element.trigger('chosen:updated');
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
    init: function(element, valueAccessor) {
        jQuery(element).switchButton({
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
    }
};

ko.bindingHandlers.color = {
    init: function(element, valueAccessor) {
        var myColor = new jscolor.color(element);
    }
};

var NotificationConfig = (function() {

    function Notification(data)
    {
        var self = this;

        self.name = ko.observable(data.name);
        self.key = ko.observable(data.key);
        self.send_type = ko.observable(data.send_type);
        self.custom_channel = ko.observable(data.custom_channel);
        self.colorize = ko.observable(data.colorize*1);
        self.color = ko.observable(data.color);

        self.buildName = function(field, multi) {
            return 'groups[notifications][fields][' + self.key() + '_' + field + '][value]' + (multi ? '[]' : '');
        };
    }

    function NotificationsList(data) {
        var self = this;

        self.notificationsData = ko.observableArray();
        ko.utils.arrayForEach(data, function(d){
            self.notificationsData.push(new Notification(d));
        })
    }

    return {
        list: NotificationsList
    };
})();