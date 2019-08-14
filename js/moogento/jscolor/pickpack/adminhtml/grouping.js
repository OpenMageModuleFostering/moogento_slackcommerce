/** 
* Moogento
* 
* SOFTWARE LICENSE
* 
* This source file is covered by the Moogento End User License Agreement
* that is bundled with this extension in the file License.html
* It is also available online here:
* http://www.moogento.com/License.html
* 
* NOTICE
* 
* If you customize this file please remember that it will be overwrtitten
* with any future upgrade installs. 
* If you'd like to add a feature which is not in this software, get in touch
* at www.moogento.com for a quote.
* 
* ID          pe+sMEDTrtCzNq3pehW9DJ0lnYtgqva4i4Z=
* File        grouping.js
* @category   Moogento
* @package    pickPack
* @copyright  Copyright (c) 2014 Moogento <info@moogento.com> / All rights reserved.
* @license    http://www.moogento.com/License.html
*/
var moogenthoShippingMethodGroup = Class.create();
moogenthoShippingMethodGroup.prototype = {
    initialize: function (containerId, statusesHtml,countryGroup, baseName) {

        this.template = false;
        this.templateSyntax = /(^|.|\r|\n)({{(\w+)}})/;

        this.rowsCount = 0;
        if(countryGroup =='')
        {
            var ship_easy_message = 'This needs the <b><a href="http://moogento.com/magento-order-shipping-processing.html" target="_blank">shipEasy</a></b> extension to work';
            this.templateText = '<tr id="shipping_grouping_{{id}}" class="shipping_background">' +
                '<td><input  name="' + baseName + '[row_{{id}}][name]" class="row_{{id}}_name  input-text" type="text" value="{{name}}" /></td>' +
                '<td><select  id="row_{{id}}" style="width: 125px !important;" name="' + baseName + '[row_{{id}}][type][]" class="select_type row_{{id}}_type">'+statusesHtml+'</select></td>' +
                '<td style="width: 80px !important;"><textarea style="width: 19em !important;height: 9.5em !important;" name="' + baseName + '[row_{{id}}][pattern]" class=" input-text row_{{id}}_pattern" type="text-area" value="{{pattern}}" >{{pattern}}</textarea>' +
                '<div style="width: 80px !important;"><label  class="row_{{id}}_country_group">'+ship_easy_message+'</label></div></td>' +
                '<td><input name="' + baseName + '[row_{{id}}][xnudge]" class="input-text row_{{id}}_xnudge" type="text" value="{{xnudge}}" /></td>' +
                '<td><input name="' + baseName + '[row_{{id}}][ynudge]" class="input-text row_{{id}}_" type="text" value="{{ynudge}}" /></td>' +
                '<td><input style="width: 30px !important;" name="' + baseName + '[row_{{id}}][priority]" class="input-textynudge row_{{id}}_priority" type="text" value="{{priority}}"/></td>' +
                '<td>{{image}}<input name="' + baseName + '[row_{{id}}][file]" type="file" class="input-text" value="{{image}}" /></td>' +
                '<td><button class="scalable delete delete-select-row icon-btn" type="button"><span><span><span>Delete</span></span></span></button></td>' +
                '</tr>';
        }
        else
        {
            this.templateText = '<tr id="shipping_grouping_{{id}}" class="shipping_background">' +
                '<td><input  name="' + baseName + '[row_{{id}}][name]" class="row_{{id}}_name  input-text" type="text" value="{{name}}" /></td>' +
                '<td><select  id="row_{{id}}" style="width: 125px !important;" name="' + baseName + '[row_{{id}}][type][]" class="select_type row_{{id}}_type">'+statusesHtml+'</select></td>' +
                '<td><textarea style="width: 19em !important;height: 9.5em !important;" name="' + baseName + '[row_{{id}}][pattern]" class="input-text row_{{id}}_pattern" type="text-area" value="{{pattern}}" >{{pattern}}</textarea>' +
                    '<select id="change_country_group_row_{{id}}" style="width: 80px !important;" name="' + baseName + '[row_{{id}}][country_group][]" class="select_country_group row_{{id}}_country_group">'+countryGroup+'</select></td>' +
                '<td><input name="' + baseName + '[row_{{id}}][xnudge]" class="input-text row_{{id}}_xnudge" type="text" value="{{xnudge}}" /></td>' +
                '<td><input name="' + baseName + '[row_{{id}}][ynudge]" class="input-text row_{{id}}_" type="text" value="{{ynudge}}" /></td>' +
                '<td><input style="width: 30px !important;" name="' + baseName + '[row_{{id}}][priority]" class="input-textynudge row_{{id}}_priority" type="text" value="{{priority}}"/></td>' +
                '<td>{{image}}<input name="' + baseName + '[row_{{id}}][file]" type="file" class="input-text" value="{{image}}" /></td>' +
                '<td><button class="scalable delete delete-select-row icon-btn" type="button"><span><span><span>Delete</span></span></span></button></td>' +
                '</tr>';
        }

        this.container = $(containerId);
        this.tableBody = $(this.container.select('tbody')[0]);
        this.addBtn = $(this.container.select('#add-new-shipping-method-group')[0]);

        this.addBtn.observe('click', this.addBackgroundRow.bind(this));
    },


    addBackgroundRow: function() {
        this.rowsCount++;
        this.template = new Template(this.templateText, this.templateSyntax);
        Element.insert(this.tableBody, {'bottom':this.template.evaluate({id: this.rowsCount, value: ''})});
        this.bindRemoveBtns();
        
        var tr = $('shipping_grouping_'+this.rowsCount);
        var type = $(tr.select('select')[0]);
        if(type.getValue() == 'shipping_method')
        {
            var hidden_country_select = $(tr.select('select')[1]);
            if(hidden_country_select != undefined)
                hidden_country_select.hide();
            else
            {
                label = $(tr.select('label')[0]);
                if (label != undefined) {
                    label.hide();
                }
            }
        }
        else if(type.getValue() == 'country_group')
        {
            var hidden_partern_area = $(tr.select('textarea')[0]);
            if(hidden_partern_area != undefined)
                hidden_partern_area.hide();

        }

        var select = $('row_'+this.rowsCount);
        select.observe("change",function(event){
            if(select.getValue() == 'shipping_method' )
            {
               $$('.'+select.getAttribute('id')+'_country_group')[0].hide();
               $$('.'+select.getAttribute('id')+'_pattern')[0].show();
            }else
            {
                $$('.'+select.getAttribute('id')+'_pattern')[0].hide();
                $$('.'+select.getAttribute('id')+'_country_group')[0].show();
            }
        });

    
    },

    bindRemoveBtns: function() {
        this.tableBody.select('.delete-select-row').each(function(elm){
            elm = $(elm);
            if (!elm.binded) {
                elm.binded = true;
                Event.observe(elm, 'click', this.removeBackgroundRow.bind(this));
            }
        }.bind(this));
    },

    removeBackgroundRow: function(event) {
        var element = $(Event.findElement(event, 'tr'));
        if (element) {
            element = $(element);
            element.remove();
        }
    },

    //Insert value to table.
    initValues: function(values) {
//        alert(JSON.stringify(values, null, 4));
        for(key in values) {
            this.rowsCount++;
            this.template = new Template(this.templateText, this.templateSyntax);
            Element.insert(
                this.tableBody,
                {
                    'bottom':this.template.evaluate({
                        id: this.rowsCount,
                        name: values[key]['name'],
                        type: values[key]['type'],
                        pattern: values[key]['pattern'],
                        country_group: values[key]['country_group'],
                        xnudge: values[key]['xnudge'],
                        ynudge: values[key]['ynudge'],
                        priority: values[key]['priority'],
                        image: values[key]['image']
                    })
                }
            );

            typeSelect = $($('shipping_grouping_' + this.rowsCount).select('select')[0]);
            typeSelect.setValue(values[key]['type']);
            countryGroupSelect = $($('shipping_grouping_' + this.rowsCount).select('select')[1]);
            if (countryGroupSelect != undefined) {
                countryGroupSelect.setValue(values[key]['country_group']);
            }
        }
        this.bindRemoveBtns();
    }


};




////////////////JS for Packing Sheet/////////////////
var moogenthoShippingMethodGroupPack = Class.create();
moogenthoShippingMethodGroupPack.prototype = {
    initialize: function (containerId, statusesHtml,countryGroup, baseName) {
        this.template = false;
        this.templateSyntax = /(^|.|\r|\n)({{(\w+)}})/;
        this.rowsCount = 0;
            if(countryGroup =='')
            {
                var ship_easy_message = 'This needs the <b><a href="https://moogento.com/magento-order-shipping-processing.html" target="_blank">shipEasy</a></b> extension to work';
                this.templateText = '<tr id="pack_shipping_grouping_{{id}}" class="shipping_background">' +
                    '<td><input  name="' + baseName + '[pack_row_{{id}}][name]" class="pack_row_{{id}}_name  input-text" type="text" value="{{name}}" /></td>' +
                    '<td><select  id="pack_row_{{id}}" style="width: 125px !important;" name="' + baseName + '[pack_row_{{id}}][type][]" class="select_type pack_row_{{id}}_type">'+statusesHtml+'</select></td>' +
                    '<td style="width: 80px !important;"><textarea style="width: 19em !important;height: 9.5em !important;" name="' + baseName + '[pack_row_{{id}}][pattern]" class=" input-text pack_row_{{id}}_pattern" type="text-area" value="{{pattern}}" >{{pattern}}</textarea>' +
                    '<div style="width: 80px !important;"><label  class="pack_row_{{id}}_country_group">'+ship_easy_message+'</label></div></td>' +
                    '<td><input name="' + baseName + '[pack_row_{{id}}][xnudge]" class="input-text pack_row_{{id}}_xnudge" type="text" value="{{xnudge}}" /></td>' +
                    '<td><input name="' + baseName + '[pack_row_{{id}}][ynudge]" class="input-text pack_row_{{id}}_" type="text" value="{{ynudge}}" /></td>' +
                    '<td><input style="width: 30px !important;" name="' + baseName + '[pack_row_{{id}}][priority]" class="input-textynudge pack_row_{{id}}_priority" type="text" value="{{priority}}"/></td>' +
                    '<td>{{image}}<input name="' + baseName + '[pack_row_{{id}}][file]" type="file" class="input-text" value="{{image}}" /></td>' +
                    '<td><button class="scalable delete delete-select-pack-row icon-btn" type="button"><span><span><span>Delete</span></span></span></button></td>' +
                    '</tr>';
            }
            else
            {
                this.templateText = '<tr id="pack_shipping_grouping_{{id}}" class="shipping_background">' +
                    '<td><input  name="' + baseName + '[pack_row_{{id}}][name]" class="pack_row_{{id}}_name  input-text" type="text" value="{{name}}" /></td>' +
                    '<td><select  id="pack_row_{{id}}" style="width: 125px !important;" name="' + baseName + '[pack_row_{{id}}][type][]" class="select_type pack_row_{{id}}_type">'+statusesHtml+'</select></td>' +
                    '<td><textarea style="width: 19em !important;height: 9.5em !important;" name="' + baseName + '[pack_row_{{id}}][pattern]" class=" input-text pack_row_{{id}}_pattern" type="text-area" value="{{pattern}}" >{{pattern}}</textarea>' +
                        '<select id="change_country_group_pack_row_{{id}}" style="width: 80px !important;" name="' + baseName + '[pack_row_{{id}}][country_group][]" class="select_country_group pack_row_{{id}}_country_group">'+countryGroup+'</select></td>' +
                    '<td><input name="' + baseName + '[pack_row_{{id}}][xnudge]" class="input-text pack_row_{{id}}_xnudge" type="text" value="{{xnudge}}" /></td>' +
                    '<td><input name="' + baseName + '[pack_row_{{id}}][ynudge]" class="input-text pack_row_{{id}}_" type="text" value="{{ynudge}}" /></td>' +
                    '<td><input style="width: 30px !important;" name="' + baseName + '[pack_row_{{id}}][priority]" class="input-textynudge pack_row_{{id}}_priority" type="text" value="{{priority}}"/></td>' +
                    '<td>{{image}}<input name="' + baseName + '[pack_row_{{id}}][file]" type="file" class="input-text" value="{{image}}" /></td>' +
                    '<td><button class="scalable delete delete-select-pack-row icon-btn" type="button"><span><span><span>Delete</span></span></span></button></td>' +
                    '</tr>';
            }
        this.container = $(containerId);
        this.tableBody = $(this.container.select('tbody')[0]);
        this.addBtn = $(this.container.select('#pack-add-new-shipping-method-group')[0]);
        this.addBtn.observe('click', this.addBackgroundRow.bind(this));
    },

    addBackgroundRow: function() {
        this.rowsCount++;
        this.template = new Template(this.templateText, this.templateSyntax);
        Element.insert(this.tableBody, {'bottom':this.template.evaluate({id: this.rowsCount, value: ''})});
        this.bindRemoveBtns();
        
        var tr = $('pack_shipping_grouping_'+this.rowsCount);
        var type = $(tr.select('select')[0]);
        if(type.getValue() == 'shipping_method')
        {
            var hidden_country_select = $(tr.select('select')[1]);
            if(hidden_country_select != undefined)
                hidden_country_select.hide();
            else
            {
                label = $(tr.select('label')[0]);
                if (label != undefined) {
                    label.hide();
                }
            }
        }
        else if(type.getValue() == 'country_group')
        {
            var hidden_partern_area = $(tr.select('textarea')[0]);
            if(hidden_partern_area != undefined)
                hidden_partern_area.hide();

        }

        var select = $('pack_row_'+this.rowsCount);
        select.observe("change",function(event){
            if(select.getValue() == 'shipping_method' )
            {
               $$('.'+select.getAttribute('id')+'_country_group')[0].hide();
               $$('.'+select.getAttribute('id')+'_pattern')[0].show();
            }else
            {
                $$('.'+select.getAttribute('id')+'_pattern')[0].hide();
                $$('.'+select.getAttribute('id')+'_country_group')[0].show();
            }
        });
        
    },

    bindRemoveBtns: function() {
        this.tableBody.select('.delete-select-pack-row').each(function(elm){
            elm = $(elm);
            if (!elm.binded) {
                elm.binded = true;
                Event.observe(elm, 'click', this.removeBackgroundRow.bind(this));
            }
        }.bind(this));
    },

    removeBackgroundRow: function(event) {
        var element = $(Event.findElement(event, 'tr'));
        if (element) {
            element = $(element);
            element.remove();
        }
    },

    //Insert value to table.
    initValues: function(values) {
        for(key in values) {
            this.rowsCount++;
            this.template = new Template(this.templateText, this.templateSyntax);
            Element.insert(
                this.tableBody,
                {
                    'bottom':this.template.evaluate({
                        id: this.rowsCount,
                        name: values[key]['name'],
                        type: values[key]['type'],
                        pattern: values[key]['pattern'],
                        country_group: values[key]['country_group'],
                        xnudge: values[key]['xnudge'],
                        ynudge: values[key]['ynudge'],
                        priority: values[key]['priority'],
                        image: values[key]['image']
                    })
                }
            );

            typeSelect = $($('pack_shipping_grouping_' + this.rowsCount).select('select')[0]);
            typeSelect.setValue(values[key]['type']);
            countryGroupSelect = $($('pack_shipping_grouping_' + this.rowsCount).select('select')[1]);
            if (countryGroupSelect != undefined) {
                countryGroupSelect.setValue(values[key]['country_group']);
            }
        }
        this.bindRemoveBtns();
    }


};




document.observe("dom:loaded", function() {
    /*
    //TODO showing sku and sku title
    var pack_sku_X = $('row_pickpack_options_wonder_pricesN_skuX');
    if (typeof(pack_sku_X) != 'undefined' && pack_sku_X != null)
    {
        pack_sku_X.show();
        var pack_sku_X_input = $('pickpack_options_wonder_pricesN_skuX');
        if (pack_sku_X_input.hasAttribute('disabled')) { 
            pack_sku_X_input.removeAttribute('disabled'); 
        }
    }
    
    var pack_sku_title = $('row_pickpack_options_wonder_sku_title');
    if (typeof(pack_sku_title) != 'undefined' && pack_sku_title != null)
    {
        pack_sku_title.show();
        var pack_sku_title_input = $('pickpack_options_wonder_sku_title');
        if (pack_sku_title_input.hasAttribute('disabled')) { 
            pack_sku_title_input.removeAttribute('disabled'); 
        }
    }
    //Invoice sku
    var invoice_sku_X = $('row_pickinvoice_options_wonder_invoice_pricesN_skuX');
    if (typeof(invoice_sku_X) != 'undefined' && invoice_sku_X != null)
    {
        invoice_sku_X.show();
        var invoice_sku_X_input = $('pickinvoice_options_wonder_invoice_pricesN_skuX');
        if (invoice_sku_X_input.hasAttribute('disabled')) { 
            invoice_sku_X_input.removeAttribute('disabled'); 
        }
    }
    
    var invoice_sku_title = $('row_pickinvoice_options_wonder_invoice_sku_title');
    if (typeof(invoice_sku_title) != 'undefined' && invoice_sku_title != null)
    {
        invoice_sku_title.show();
        var invoice_sku_title_input = $('pickinvoice_options_wonder_invoice_sku_title');
        if (invoice_sku_title_input.hasAttribute('disabled')) { 
            invoice_sku_title_input.removeAttribute('disabled'); 
        }
    }
    
    //TODO showing sku barcode
    var pack_sku_barcode_X = $('row_pickpack_options_wonder_pricesN_barcodeX');
    if (typeof(pack_sku_barcode_X) != 'undefined' && pack_sku_barcode_X != null)
    {
        pack_sku_barcode_X.show();
        var pack_sku_barcode_X_input = $('pickpack_options_wonder_pricesN_barcodeX');
        if (pack_sku_barcode_X_input.hasAttribute('disabled')) { 
            pack_sku_barcode_X_input.removeAttribute('disabled'); 
        }
    }
    
    var pack_sku_barcode_title = $('row_pickpack_options_wonder_sku_barcode_title');
    if (typeof(pack_sku_barcode_title) != 'undefined' && pack_sku_barcode_title != null)
    {
        pack_sku_barcode_title.show();
        var pack_sku_barcode_title_input = $('pickpack_options_wonder_sku_barcode_title');
        if (pack_sku_barcode_title_input.hasAttribute('disabled')) { 
            pack_sku_barcode_title_input.removeAttribute('disabled'); 
        }
    }
    
    //TODO showing sku barcode
    var pack_sku_barcode_X = $('row_pickpack_options_wonder_invoice_pricesN_barcodeX');
    if (typeof(pack_sku_barcode_X) != 'undefined' && pack_sku_barcode_X != null)
    {
        pack_sku_barcode_X.show();
        var pack_sku_barcode_X_input = $('pickpack_options_wonder_invoice__pricesN_barcodeX');
        if (pack_sku_barcode_X_input.hasAttribute('disabled')) { 
            pack_sku_barcode_X_input.removeAttribute('disabled'); 
        }
    }
    
    var pack_sku_barcode_title = $('row_pickpack_options_wonder_invoice_sku_barcode_title');
    if (typeof(pack_sku_barcode_title) != 'undefined' && pack_sku_barcode_title != null)
    {
        pack_sku_barcode_title.show();
        var pack_sku_barcode_title_input = $('pickpack_options_wonder_invoice_sku_barcode_title');
        if (pack_sku_barcode_title_input.hasAttribute('disabled')) { 
            pack_sku_barcode_title_input.removeAttribute('disabled'); 
        }
    }
    */
    var shipping_background = $$('.shipping_background');
    shipping_background.each(function(tr){
        var type = $(tr.select('select')[0]);
        if(type.getValue() == 'shipping_method')
        {
            var hidden_country_select = $(tr.select('select')[1]);
            if(hidden_country_select != undefined)
                hidden_country_select.hide();
            else
            {
                label = $(tr.select('label')[0]);
                if (label != undefined) {
                    label.hide();
                }
            }
        }
        else if(type.getValue() == 'country_group')
        {
            var hidden_partern_area = $(tr.select('textarea')[0]);
            if(hidden_partern_area != undefined)
                hidden_partern_area.hide();

        }

    });

    $$('.select_type').each(function(select){
        select.observe("change",function(event){
            if(select.getValue() == 'shipping_method' )
            {
               $$('.'+select.getAttribute('id')+'_country_group')[0].hide();
               $$('.'+select.getAttribute('id')+'_pattern')[0].show();
            }else
            {
                $$('.'+select.getAttribute('id')+'_pattern')[0].hide();
                $$('.'+select.getAttribute('id')+'_country_group')[0].show();
            }
        });
    });
    
    
    //Auto processing config.
     var update_text_arr = new Array(
        'pickpack_options_wonder_enable_auto_processing'
        // 
//         'pickpack_options_wonder_enable_auto_processing',
//         'pickpack_options_wonder_invoice_enable_auto_processing',
//         'pickpack_options_picks_enable_auto_processing',
//         'pickpack_options_messages_enable_auto_processing'
        );
        
    for (var i = 0, l = update_text_arr.length; i < l; i++) {                
        var auto_processing_main_control = $(update_text_arr[i]);
        if(((typeof(auto_processing_main_control)) != 'undefined') && (auto_processing_main_control != null))
        {
            var selected_value = auto_processing_main_control.selectedIndex;
            if(i == 0)
            {
                var auto_description = $('pack_description').down('.moo_config_info');
            }

            if(i == 1)
            {
                var auto_description = $('invoice_description').down('.moo_config_info');
            }
                        
                if(selected_value == 0 )
                {
                    $$('.auto-processing-printing').each(function(fieldset){
                        fieldset.hide();
                     });
                    if(((typeof(auto_description)) != 'undefined') && (auto_description != null))
                        auto_description.hide();
                }
                auto_processing_main_control.observe("change",function(event){
                if(auto_processing_main_control.getValue() == '0' )
                {
                    $$('.auto-processing-printing').each(function(fieldset){
                        fieldset.hide();
                     });
                    if(((typeof(auto_description)) != 'undefined') && (auto_description != null))
                        auto_description.hide();
                }
                else
                {
                    $$('.auto-processing-printing').each(function(fieldset){
                        fieldset.show();
                     });;
                    if(((typeof(auto_description)) != 'undefined') && (auto_description != null))
                        auto_description.show();
                }
            });
        }
    }
    
    
    var auto_processing_tr = $$('.auto-processing');
    auto_processing_tr.each(function(tr){
        var auto_type = $(tr.select('select')[0]);
        //alert(auto_type.getValue());
        if((typeof(auto_type) !='undefined') && (auto_type != null))
        {
            if(auto_type.getValue() == '0')
            {
                var hidden_country_select = $(tr.select('fieldset fieldset'));
                hidden_country_select.each(function(fieldset){
                    fieldset.hide();
                });

            }
        
            auto_type.observe("change",function(event){
                if(auto_type.getValue() == '0' )
                {
                    $(tr.select('fieldset fieldset')).each(function(fieldset){
                        fieldset.hide();
                     });
                }
                else
                {
                    $(tr.select('fieldset fieldset')).each(function(fieldset){
                        fieldset.show();
                     });
                }
            });
            
            auto_type.observe("change",function(event){
            if(auto_type.getValue() == '0' )
            {
                $(tr.select('fieldset fieldset')).each(function(fieldset){
                    fieldset.hide();
                 });
            }
            else
            {
                $(tr.select('fieldset fieldset')).each(function(fieldset){
                    fieldset.show();
                 });
            }
        });
            }        
    });
    
    var pickpack_general_simple_design_yn = $('pickpack_options_general_simple_design_yn');
    if (typeof(pickpack_general_simple_design_yn) != 'undefined' && pickpack_general_simple_design_yn != null)
    {
       pickpack_general_simple_design_yn.observe("change",function(change){
        var text = $('pickpack_options_general_simple_design_yn')[$('pickpack_options_general_simple_design_yn').selectedIndex].text;
            if(text == 'No' )
            {
                $$('.text-format').each(function(fieldset){
                    fieldset.hide();
                 });
            }
            else
            {
                $$('.text-format').each(function(fieldset){
                    fieldset.show();
                 });
            }
        });
    }
    
    
    
    //Packing sheet custom attributes
    var pickpack_options_wonder_shelving_real_yn = $('pickpack_options_wonder_shelving_real_yn');
    if(((typeof(pickpack_options_wonder_shelving_real_yn)) != 'undefined') && (pickpack_options_wonder_shelving_real_yn != null))
    {
        var text_custom_1 = pickpack_options_wonder_shelving_real_yn[pickpack_options_wonder_shelving_real_yn.selectedIndex].text;
            if(text_custom_1 == 'No' )
            {
                $$('.custom_attribute2_text_grouped').each(function(fieldset){
                    fieldset.hide();
                 });
            }
            else
            {
                $$('.custom_attribute2_text_grouped').each(function(fieldset){
                    fieldset.show();
                 });
            }
    }
    var pickpack_options_wonder_shelving_real_yn = $('pickpack_options_wonder_shelving_real_yn');
    if (typeof(pickpack_options_wonder_shelving_real_yn) != 'undefined' && pickpack_options_wonder_shelving_real_yn != null)
    {
       pickpack_options_wonder_shelving_real_yn.observe("change",function(change){
        var text = $('pickpack_options_wonder_shelving_real_yn')[$('pickpack_options_wonder_shelving_real_yn').selectedIndex].text;
            if(text == 'No' )
            {
                $$('.custom_attribute2_text_grouped').each(function(fieldset){
                    fieldset.hide();
                 });
                $$('.custom_attribute3_text_grouped').each(function(fieldset){
                        fieldset.hide();
                });
                $$('.custom_attribute4_text_grouped').each(function(fieldset){
                        fieldset.hide();
                });
            }
            else
            {
                $$('.custom_attribute2_text_grouped').each(function(fieldset){
                    fieldset.show();
                 });
                 var text_custom_2 = $('pickpack_options_wonder_shelving_yn')[$('pickpack_options_wonder_shelving_yn').selectedIndex].text;
                if(text_custom_2 == 'No' )
                {
                    $$('.custom_attribute3_text_grouped').each(function(fieldset){
                        fieldset.hide();
                     });
                }
                else
                {
                    $$('.custom_attribute3_text_grouped').each(function(fieldset){
                        fieldset.show();
                     });
                }
            }
        });
    }
    
    
    var pickpack_options_wonder_shelving_yn = $('pickpack_options_wonder_shelving_yn');
    if(((typeof(pickpack_options_wonder_shelving_yn)) != 'undefined') && (pickpack_options_wonder_shelving_yn != null))
    {
        pickpack_options_wonder_shelving_yn.observe("change",function(change){
            var text_custom_2 = pickpack_options_wonder_shelving_yn[pickpack_options_wonder_shelving_yn.selectedIndex].text;
            if(text_custom_2 == 'No' )

            {
                $$('.custom_attribute3_text_grouped').each(function(fieldset){
                    fieldset.hide();


                 });
            }
            else

            {
                $$('.custom_attribute3_text_grouped').each(function(fieldset){
                    fieldset.show();


                 });
            }
        });
    }

    var pickpack_options_wonder_shelving_2_yn = $('pickpack_options_wonder_shelving_2_yn');
    if (typeof(pickpack_options_wonder_shelving_2_yn) != 'undefined' && pickpack_options_wonder_shelving_2_yn != null)
    {
       pickpack_options_wonder_shelving_2_yn.observe("change",function(change){

        var text = $('pickpack_options_wonder_shelving_2_yn')[$('pickpack_options_wonder_shelving_2_yn').selectedIndex].text;
            if(text == 'No' )
            {
                $$('.custom_attribute4_text_grouped').each(function(fieldset){
                    fieldset.hide();
                 });
            }
            else
            {
                $$('.custom_attribute4_text_grouped').each(function(fieldset){
                    fieldset.show();
                 });
            }
        });
    }
    
    //Invoice custom attributes 
    var pickpack_options_wonder_invoice_shelving_real_yn = $('pickpack_options_wonder_invoice_shelving_real_yn');
    if(((typeof(pickpack_options_wonder_invoice_shelving_real_yn)) != 'undefined') && (pickpack_options_wonder_invoice_shelving_real_yn != null))
    {
        var text_custom_1 = pickpack_options_wonder_invoice_shelving_real_yn[pickpack_options_wonder_invoice_shelving_real_yn.selectedIndex].text;
            if(text_custom_1 == 'No' )
            {
                $$('.custom_attribute2_text_grouped').each(function(fieldset){
                    fieldset.hide();
                 });
            }
            else
            {
                $$('.custom_attribute2_text_grouped').each(function(fieldset){
                    fieldset.show();
                 });
            }
    }
    
    var pickpack_options_wonder_invoice_shelving_real_yn = $('pickpack_options_wonder_invoice_shelving_real_yn');
    if (typeof(pickpack_options_wonder_invoice_shelving_real_yn) != 'undefined' && pickpack_options_wonder_invoice_shelving_real_yn != null)
    {
       pickpack_options_wonder_invoice_shelving_real_yn.observe("change",function(change){
        var text = $('pickpack_options_wonder_invoice_shelving_real_yn')[$('pickpack_options_wonder_invoice_shelving_real_yn').selectedIndex].text;
            if(text == 'No' )
            {
                $$('.custom_attribute2_text_grouped').each(function(fieldset){
                    fieldset.hide();
                 });
                $$('.custom_attribute3_text_grouped').each(function(fieldset){
                        fieldset.hide();
                     });
                $$('.custom_attribute4_text_grouped').each(function(fieldset){
                        fieldset.hide();
                });
            }
            else
            {
                $$('.custom_attribute2_text_grouped').each(function(fieldset){
                    fieldset.show();
                 });
                 var text_custom_2 = $('pickpack_options_wonder_invoice_shelving_yn')[$('pickpack_options_wonder_invoice_shelving_yn').selectedIndex].text;
                if(text_custom_2 == 'No' )
                {
                    $$('.custom_attribute3_text_grouped').each(function(fieldset){
                        fieldset.hide();
                     });
                }
                else
                {
                    $$('.custom_attribute3_text_grouped').each(function(fieldset){
                        fieldset.show();
                     });
                }
            }
        });
    }
    
    
    
    var pickpack_options_wonder_invoice_shelving_yn = $('pickpack_options_wonder_invoice_shelving_yn');
    if(((typeof(pickpack_options_wonder_invoice_shelving_yn)) != 'undefined') && (pickpack_options_wonder_invoice_shelving_yn != null))
    {
        pickpack_options_wonder_invoice_shelving_yn.observe("change", function(change){
            var text_custom_2 = pickpack_options_wonder_invoice_shelving_yn[pickpack_options_wonder_invoice_shelving_yn.selectedIndex].text;
        
            if(text_custom_2 == 'No' )
            {
                $$('.custom_attribute3_text_grouped').each(function(fieldset){
                    fieldset.hide();
                 });
            }
            else
            {
                $$('.custom_attribute3_text_grouped').each(function(fieldset){
                    fieldset.show();
                 });
            }
        });
    }

    var pickpack_options_wonder_invoice_shelving_2_yn = $('pickpack_options_wonder_invoice_shelving_2_yn');
    if (typeof(pickpack_options_wonder_invoice_shelving_2_yn) != 'undefined' && pickpack_options_wonder_invoice_shelving_2_yn != null)
    {
       pickpack_options_wonder_invoice_shelving_2_yn.observe("change",function(change){

        var text = $('pickpack_options_wonder_invoice_shelving_2_yn')[$('pickpack_options_wonder_invoice_shelving_2_yn').selectedIndex].text;
            if(text == 'No' )
            {
                $$('.custom_attribute4_text_grouped').each(function(fieldset){
                    fieldset.hide();
                 });
            }
            else
            {
                $$('.custom_attribute4_text_grouped').each(function(fieldset){
                    fieldset.show();
                 });
            }
        });
    }

    //JS for auto processing
    //Pack
    var pickpack_options_wonder_enable_auto_processing = $('pickpack_options_wonder_enable_auto_processing');
    
    if(((typeof(pickpack_options_wonder_enable_auto_processing)) != 'undefined') && (pickpack_options_wonder_enable_auto_processing != null))
    {
        var text_custom_1 = pickpack_options_wonder_enable_auto_processing[pickpack_options_wonder_enable_auto_processing.selectedIndex].value;
            if(text_custom_1 == 0 )
            {
                $$('#pickpack_options_wonder .auto-processing-printing').each(function(fieldset){
                    fieldset.hide();
                 });
            }
            else
            {
                $$('#pickpack_options_wonder .auto-processing-printing').each(function(fieldset){
                    fieldset.show();
                 });
            }
        pickpack_options_wonder_enable_auto_processing.observe("change",function(change){
            var text_custom_1 = pickpack_options_wonder_enable_auto_processing[pickpack_options_wonder_enable_auto_processing.selectedIndex].value;
            if(text_custom_1 == 0 )
            {
                $$('#pickpack_options_wonder .auto-processing-printing').each(function(fieldset){
                    fieldset.hide();
                });
            }
            else
            {
                $$('#pickpack_options_wonder .auto-processing-printing').each(function(fieldset){
                    fieldset.show();
                });
            }
        });
    }

    
    
    //Invoice
    
    var pickpack_options_wonder_invoice_enable_auto_processing = $('pickpack_options_wonder_invoice_enable_auto_processing');
    
    if(((typeof(pickpack_options_wonder_invoice_enable_auto_processing)) != 'undefined') && (pickpack_options_wonder_invoice_enable_auto_processing != null))
    {
        var text_custom_1 = pickpack_options_wonder_invoice_enable_auto_processing[pickpack_options_wonder_invoice_enable_auto_processing.selectedIndex].value;
            if(text_custom_1 == 0 )
            {
                $$('#pickpack_options_wonder_invoice .auto-processing-printing').each(function(fieldset){
                    fieldset.hide();
                 });
            }
            else
            {
                $$('#pickpack_options_wonder_invoice .auto-processing-printing').each(function(fieldset){
                    fieldset.show();
                 });
            }
            pickpack_options_wonder_invoice_enable_auto_processing.observe("change",function(change){ 
    var text_custom_1 = pickpack_options_wonder_invoice_enable_auto_processing[pickpack_options_wonder_invoice_enable_auto_processing.selectedIndex].value;
                if(text_custom_1 == 0 )
                {
                    $$('#pickpack_options_wonder_invoice .auto-processing-printing').each(function(fieldset){
                        fieldset.hide();
                     });
                }
                else
                {
                    $$('#pickpack_options_wonder_invoice .auto-processing-printing').each(function(fieldset){
                        fieldset.show();
                     });
                }
    });
    }
    
 
    //Messages pickpack_options_messages_enable_auto_processing
    
    var pickpack_options_messages_enable_auto_processing = $('pickpack_options_messages_enable_auto_processing');
    
    if(((typeof(pickpack_options_messages_enable_auto_processing)) != 'undefined') && (pickpack_options_messages_enable_auto_processing != null))
    {
        var text_custom_1 = pickpack_options_messages_enable_auto_processing[pickpack_options_messages_enable_auto_processing.selectedIndex].value;
            if(text_custom_1 == 0 )
            {
                $$('#pickpack_options_messages .auto-processing-printing').each(function(fieldset){
                    fieldset.hide();
                 });
            }
            else
            {
                $$('#pickpack_options_messages .auto-processing-printing').each(function(fieldset){
                    fieldset.show();
                 });
            }
            pickpack_options_messages_enable_auto_processing.observe("change",function(change){ 
    var text_custom_1 = pickpack_options_messages_enable_auto_processing[pickpack_options_messages_enable_auto_processing.selectedIndex].value;
                if(text_custom_1 == 0 )
                {
                    $$('#pickpack_options_messages .auto-processing-printing').each(function(fieldset){
                        fieldset.hide();
                     });
                }
                else
                {
                    $$('#pickpack_options_messages .auto-processing-printing').each(function(fieldset){
                        fieldset.show();
                     });
                }
    });
    }
    
    
    //Picks
    var pickpack_options_picks_enable_auto_processing = $('pickpack_options_picks_enable_auto_processing');
    
    if(((typeof(pickpack_options_picks_enable_auto_processing)) != 'undefined') && (pickpack_options_picks_enable_auto_processing != null))
    {
        var text_custom_1 = pickpack_options_picks_enable_auto_processing[pickpack_options_picks_enable_auto_processing.selectedIndex].value;
            if(text_custom_1 == 0 )
            {
                $$('#pickpack_options_picks .auto-processing-printing').each(function(fieldset){
                    fieldset.hide();
                 });
            }
            else
            {
                $$('#pickpack_options_picks .auto-processing-printing').each(function(fieldset){
                    fieldset.show();
                 });
            }
            pickpack_options_picks_enable_auto_processing.observe("change",function(change){ 
        var text_custom_1 = pickpack_options_picks_enable_auto_processing[pickpack_options_picks_enable_auto_processing.selectedIndex].value;
                    if(text_custom_1 == 0 )
                    {
                        $$('#pickpack_options_picks .auto-processing-printing').each(function(fieldset){
                            fieldset.hide();
                         });
                    }
                    else
                    {
                        $$('#pickpack_options_picks .auto-processing-printing').each(function(fieldset){
                            fieldset.show();
                         });
                    }
        });
    }
    
 
    var update_text = new Array(
        '#row_pickpack_options_general_font_family_header label.inherit',
        '#row_pickpack_options_general_font_color_header label.inherit',
        '#row_pickpack_options_general_font_style_header label.inherit',
        '#row_pickpack_options_general_font_size_header label.inherit',

        '#row_pickpack_options_general_font_family_subtitles label.inherit',  
        '#row_pickpack_options_general_font_color_subtitles label.inherit',
        '#row_pickpack_options_general_font_style_subtitles label.inherit',
        '#row_pickpack_options_general_font_size_subtitles label.inherit',    

        '#row_pickpack_options_general_font_family_company label.inherit',    
        '#row_pickpack_options_general_font_color_company label.inherit',
        '#row_pickpack_options_general_font_style_company label.inherit',
        '#row_pickpack_options_general_font_size_company label.inherit',  

        '#row_pickpack_options_general_font_family_body label.inherit',   
        '#row_pickpack_options_general_font_color_body label.inherit',
        '#row_pickpack_options_general_font_style_body label.inherit',
        '#row_pickpack_options_general_font_size_body label.inherit', 

        '#row_pickpack_options_general_font_family_message label.inherit',    
        '#row_pickpack_options_general_font_color_message label.inherit',
        '#row_pickpack_options_general_font_style_message label.inherit',
        '#row_pickpack_options_general_font_size_message label.inherit',  
        '#row_pickpack_options_general_background_color_message label.inherit',
            
        '#row_pickpack_options_general_font_family_comments label.inherit',   
        '#row_pickpack_options_general_font_color_comments label.inherit',
        '#row_pickpack_options_general_font_style_comments label.inherit',
        '#row_pickpack_options_general_font_size_comments label.inherit', 
        '#row_pickpack_options_general_background_color_comments label.inherit',
        
        '#row_pickpack_options_general_font_family_gift_message label.inherit',   
        '#row_pickpack_options_general_font_color_gift_message label.inherit',
        '#row_pickpack_options_general_font_style_gift_message label.inherit',
        '#row_pickpack_options_general_font_size_gift_message label.inherit', 
        '#row_pickpack_options_general_background_color_gift_message label.inherit',
            
        '#row_pickpack_options_wonder_invoice_product_sku_yn label.inherit',  
        '#row_pickpack_options_wonder_invoice_sku_title label.inherit',
        '#row_pickpack_options_wonder_invoice_pricesN_skuX label.inherit',
        
        '#row_pickpack_options_wonder_invoice_product_sku_barcode_yn label.inherit',  
        '#row_pickpack_options_wonder_invoice_sku_barcode_title label.inherit',
        '#row_pickpack_options_wonder_invoice_pricesN_barcodeX label.inherit', 

        '#row_pickpack_options_wonder_invoice_product_stock_qty_yn label.inherit',  
        '#row_pickpack_options_wonder_invoice_product_stock_qty_title label.inherit',
        '#row_pickpack_options_wonder_invoice_pricesN_stockqtyX label.inherit', 

        '#row_pickpack_options_wonder_invoice_product_qty_backordered_yn label.inherit',  
        '#row_pickpack_options_wonder_invoice_product_qty_backordered_title label.inherit',
        '#row_pickpack_options_wonder_invoice_prices_qtybackorderedX label.inherit', 

        '#row_pickpack_options_wonder_invoice_product_warehouse_yn label.inherit',  
        '#row_pickpack_options_wonder_invoice_product_warehouse_title label.inherit',
        '#row_pickpack_options_wonder_invoice_prices_warehouseX label.inherit', 

        '#row_pickpack_options_wonder_invoice_product_images_yn label.inherit',  
        '#row_pickpack_options_wonder_invoice_images_title label.inherit',
        '#row_pickpack_options_wonder_invoice_pricesN_images_priceX label.inherit', 
        
        '#row_pickpack_options_wonder_invoice_qty_title label.inherit',  
        '#row_pickpack_options_wonder_invoice_pricesN_qty_priceX label.inherit',
        '#row_pickpack_options_wonder_invoice_product_qty_upsize_yn label.inherit',

        '#row_pickpack_options_wonder_invoice_show_product_name label.inherit',  
        '#row_pickpack_options_wonder_invoice_items_title label.inherit',
        '#row_pickpack_options_wonder_invoice_pricesN_productX label.inherit',
        //pdf zebra
        '#row_pickpack_options_label_zebra_font_family_label label.inherit',   
        '#row_pickpack_options_label_zebra_font_style_label label.inherit',
        '#row_pickpack_options_label_zebra_font_size_label label.inherit',
        '#row_pickpack_options_label_zebra_font_color_label label.inherit',
        
        '#row_pickpack_options_label_zebra_font_family_product label.inherit',   
        '#row_pickpack_options_label_zebra_font_style_product label.inherit',
        '#row_pickpack_options_label_zebra_font_size_product label.inherit',
        '#row_pickpack_options_label_zebra_font_color_product label.inherit',
        
        '#row_pickpack_options_label_zebra_font_family_return_label_side label.inherit',   
        '#row_pickpack_options_label_zebra_font_style_return_label_side label.inherit',
        '#row_pickpack_options_label_zebra_font_size_return_label_side label.inherit',
        '#row_pickpack_options_label_zebra_font_color_return_label_side label.inherit',
        '#row_pickpack_options_label_zebra_label_return_address_yn label.inherit',
        '#row_pickpack_options_label_zebra_nudge_return_label label.inherit',
        '#row_pickpack_options_label_zebra_label_return_address_side label.inherit',
        '#row_pickpack_options_label_zebra_rotate_return_label_side label.inherit',
        
        //Pack

        '#row_pickpack_options_wonder_product_sku_yn label.inherit',  
        '#row_pickpack_options_wonder_sku_title label.inherit',
        '#row_pickpack_options_wonder_pricesN_skuX label.inherit',
        
        '#row_pickpack_options_wonder_product_sku_barcode_yn label.inherit',  
        '#row_pickpack_options_wonder_sku_barcode_title label.inherit',
        '#row_pickpack_options_wonder_pricesN_barcodeX label.inherit',

        '#row_pickpack_options_wonder_product_warehouse_yn label.inherit',  
        '#row_pickpack_options_wonder_product_warehouse_title label.inherit',
        '#row_pickpack_options_wonder_prices_warehouseX label.inherit',

        '#row_pickpack_options_wonder_product_stock_qty_yn label.inherit',  
        '#row_pickpack_options_wonder_product_stock_qty_title label.inherit',
        '#row_pickpack_options_wonder_pricesN_stockqtyX label.inherit',

        '#row_pickpack_options_wonder_product_qty_backordered_yn label.inherit',  
        '#row_pickpack_options_wonder_product_qty_backordered_title label.inherit',
        '#row_pickpack_options_wonder_prices_qtybackorderedX label.inherit',

        '#row_pickpack_options_wonder_qty_title label.inherit',  
        '#row_pickpack_options_wonder_pricesN_qty_priceX label.inherit',
        '#row_pickpack_options_wonder_product_qty_upsize_yn label.inherit',

        '#row_pickpack_options_wonder_product_images_yn label.inherit',  
        '#row_pickpack_options_wonder_images_title label.inherit',
        '#row_pickpack_options_wonder_pricesN_images_priceX label.inherit',

        '#row_pickpack_options_wonder_show_product_name label.inherit',  
        '#row_pickpack_options_wonder_items_title label.inherit',
        '#row_pickpack_options_wonder_pricesN_productX label.inherit'
        );

    // $$('#row_pickpack_options_general_font_family_header label.inherit').first().update("Default");  
    // $$('#row_pickpack_options_general_font_color_header label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_style_header label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_size_header label.inherit').first().update("Default");    
    
    // $$('#row_pickpack_options_general_font_family_subtitles label.inherit').first().update("Default");   
    // $$('#row_pickpack_options_general_font_color_subtitles label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_style_subtitles label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_size_subtitles label.inherit').first().update("Default"); 

    // $$('#row_pickpack_options_general_font_family_company label.inherit').first().update("Default"); 
    // $$('#row_pickpack_options_general_font_color_company label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_style_company label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_size_company label.inherit').first().update("Default");   

    // $$('#row_pickpack_options_general_font_family_body label.inherit').first().update("Default");    
    // $$('#row_pickpack_options_general_font_color_body label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_style_body label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_size_body label.inherit').first().update("Default");  

    // $$('#row_pickpack_options_general_font_family_message label.inherit').first().update("Default"); 
    // $$('#row_pickpack_options_general_font_color_message label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_style_message label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_size_message label.inherit').first().update("Default");   
    // $$('#row_pickpack_options_general_background_color_message label.inherit').first().update("Default");
        
    // $$('#row_pickpack_options_general_font_family_comments label.inherit').first().update("Default");    
    // $$('#row_pickpack_options_general_font_color_comments label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_style_comments label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_size_comments label.inherit').first().update("Default");  
    // $$('#row_pickpack_options_general_background_color_comments label.inherit').first().update("Default");
    
    // $$('#row_pickpack_options_general_font_family_gift_message label.inherit').first().update("Default");    
    // $$('#row_pickpack_options_general_font_color_gift_message label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_style_gift_message label.inherit').first().update("Default");
    // $$('#row_pickpack_options_general_font_size_gift_message label.inherit').first().update("Default");  
    // $$('#row_pickpack_options_general_background_color_gift_message label.inherit').first().update("Default");
        
    // $$('#row_pickpack_options_wonder_invoice_product_sku_yn label.inherit').first().update("Default");   
    // $$('#row_pickpack_options_wonder_invoice_sku_title label.inherit').first().update("Default");
    // $$('#row_pickpack_options_wonder_invoice_pricesN_skuX label.inherit').first().update("Default");
    
    // $$('#row_pickpack_options_wonder_invoice_product_sku_barcode_yn label.inherit').first().update("Default");   
    // $$('#row_pickpack_options_wonder_invoice_sku_barcode_title label.inherit').first().update("Default");
    // $$('#row_pickpack_options_wonder_invoice_pricesN_barcodeX label.inherit').first().update("Default"); 

 //    $$('#row_pickpack_options_wonder_invoice_product_stock_qty_yn label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_invoice_product_stock_qty_title label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_invoice_pricesN_stockqtyX label.inherit').first().update("Default"); 

 //    $$('#row_pickpack_options_wonder_invoice_product_qty_backordered_yn label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_invoice_product_qty_backordered_title label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_invoice_prices_qtybackorderedX label.inherit').first().update("Default"); 

 //    $$('#row_pickpack_options_wonder_invoice_product_warehouse_yn label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_invoice_product_warehouse_title label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_invoice_prices_warehouseX label.inherit').first().update("Default"); 

 //    $$('#row_pickpack_options_wonder_invoice_product_images_yn label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_invoice_images_title label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_invoice_pricesN_images_priceX label.inherit').first().update("Default"); 
    
 //    $$('#row_pickpack_options_wonder_invoice_qty_title label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_invoice_pricesN_qty_priceX label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_invoice_product_qty_upsize_yn label.inherit').first().update("Default");

 //    $$('#row_pickpack_options_wonder_invoice_show_product_name label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_invoice_items_title label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_invoice_pricesN_productX label.inherit').first().update("Default");

 //    //Pack

 //    $$('#row_pickpack_options_wonder_product_sku_yn label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_sku_title label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_pricesN_skuX label.inherit').first().update("Default");
    
 //    $$('#row_pickpack_options_wonder_product_sku_barcode_yn label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_sku_barcode_title label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_pricesN_barcodeX label.inherit').first().update("Default");

 //    $$('#row_pickpack_options_wonder_product_warehouse_yn label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_product_warehouse_title label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_prices_warehouseX label.inherit').first().update("Default");

 //    $$('#row_pickpack_options_wonder_product_stock_qty_yn label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_product_stock_qty_title label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_pricesN_stockqtyX label.inherit').first().update("Default");

 //    $$('#row_pickpack_options_wonder_product_qty_backordered_yn label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_product_qty_backordered_title label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_prices_qtybackorderedX label.inherit').first().update("Default");

 //    $$('#row_pickpack_options_wonder_qty_title label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_pricesN_qty_priceX label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_product_qty_upsize_yn label.inherit').first().update("Default");

 //    $$('#row_pickpack_options_wonder_product_images_yn label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_images_title label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_pricesN_images_priceX label.inherit').first().update("Default");

 //    $$('#row_pickpack_options_wonder_show_product_name label.inherit').first().update("Default");  
 //    $$('#row_pickpack_options_wonder_items_title label.inherit').first().update("Default");
 //    $$('#row_pickpack_options_wonder_pricesN_productX label.inherit').first().update("Default");

    
    
});


