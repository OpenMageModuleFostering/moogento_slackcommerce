

var NotificationConfig = (function() {

    function Notification(data)
    {
        var self = this;

        self.inherit = ko.observable(data.inherit || 0);
        self.name = ko.observable(data.name);
        self.key = ko.observable(data.key);
        self.send_type = ko.observable(data.send_type);
        self.custom_channel = ko.observable(data.custom_channel);
        self.colorize = ko.observable(data.colorize*1);
        self.color = ko.observable(data.color);

        self.buildName = function(field, multi) {
            return 'groups[notifications][fields][' + self.key() + '_' + field + '][value]' + (multi ? '[]' : '');
        };

        self.buildInheritName = function(field) {
            return 'groups[notifications][fields][' + self.key() + '_' + field + '][inherit]';
        }
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