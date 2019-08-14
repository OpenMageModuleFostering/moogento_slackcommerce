var SecurityConfig = function(data) {
    var self = this;

    self.send_type_immediate = ko.observable(data.send_type_immediate);
    self.immediate_custom_channel = ko.observable(data.immediate_custom_channel);
    self.colorize_immediate = ko.observable(data.colorize_immediate*1);
    self.color_immediate = ko.observable(data.color_immediate);

    self.send_type = ko.observable(data.send_type);
    self.custom_channel = ko.observable(data.custom_channel);
    self.colorize = ko.observable(data.colorize*1);
    self.color = ko.observable(data.color);

    self.hour = ko.observable(data.hour);
	
    self.total_number_fails = ko.observable(data.total_number_fails*1);
    self.count_ip_fails = ko.observable(data.count_ip_fails*1);
    self.count_target_fails = ko.observable(data.count_target_fails*1);
    self.not_sent_if_no_fails = ko.observable(data.not_sent_if_no_fails*1);
    self.have_line_fails = ko.observable(data.have_line_fails*1);
    self.line_fails = ko.observable(data.line_fails);

    self.buildSecurityName = function(field, multi) {
        return 'groups[security][fields][' + field + '][value]' + (multi ? '[]' : '');
    };
};