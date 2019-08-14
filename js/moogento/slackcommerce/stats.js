var StatsConfig = function(data) {
    var self = this;

    self.send_type = ko.observable(data.send_type);
    self.custom_channel = ko.observable(data.custom_channel);
    self.colorize = ko.observable(data.colorize*1);
    self.color = ko.observable(data.color);

    self.qty_orders = ko.observable(1*data.qty_orders);
    self.total_revenue = ko.observable(1*data.total_revenue);
    self.qty_products = ko.observable(1*data.qty_products);
    self.avg_products_order = ko.observable(1*data.avg_products_order);
    self.avg_revenue_order = ko.observable(1*data.avg_revenue_order);
    self.hour = ko.observable(data.hour);
    self.daily_stats = ko.observable(1*data.daily_stats);
    self.day = ko.observable(data.day);
    self.weekly_stats = ko.observable(1*data.weekly_stats);
    self.total_number_fails = ko.observable(1*data.total_number_fails);
    self.count_ip_fails = ko.observable(1*data.count_ip_fails);
    self.count_target_fails = ko.observable(1*data.count_target_fails);
    self.not_sent_if_no_fails = ko.observable(1*data.not_sent_if_no_fails);
    self.have_line_fails = ko.observable(1*data.have_line_fails);
    self.line_fails = ko.observable(data.line_fails);

    self.buildName = function(field, multi) {
        return 'groups[stats][fields][' + field + '][value]' + (multi ? '[]' : '');
    };
    self.buildSecuriteName = function(field, multi) {
        return 'groups[security][fields][' + field + '][value]' + (multi ? '[]' : '');
    };
};