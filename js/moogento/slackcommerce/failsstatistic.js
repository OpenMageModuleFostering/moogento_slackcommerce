var FailsStatisticConfig = function(data) {
    var self = this;

    self.send_type = ko.observable(data.send_type);
    self.custom_channel = ko.observable(data.custom_channel);
    self.colorize = ko.observable(data.colorize*1);
    self.color = ko.observable(data.color);
    self.hour = ko.observable(data.hour);

    self.total_number_fails = ko.observable(1*data.total_number_fails);
    self.count_ip_fails = ko.observable(1*data.count_ip_fails);
    self.count_target_fails = ko.observable(1*data.count_target_fails);
    self.not_sent_if_no_fails = ko.observable(1*data.not_sent_if_no_fails);
    self.have_line_fails = ko.observable(1*data.have_line_fails);
    self.line_fails = ko.observable(data.line_fails);

    self.buildFailsStatisticName = function(field, multi) {
        return 'groups[fails_statistic][fields][' + field + '][value]' + (multi ? '[]' : '');
    };
};