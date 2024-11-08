define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'ko'
], function (_, uiRegistry, select, ko) {
    'use strict';

    self.typeColumn = null;
    self.shippingColumn = null;

    return select.extend({
        initialize: function () {
            const shippingValue = '1';
            this._super();
            let typeColumn = this.index === 'type' ? this : false;
            if (typeColumn) {
                self.typeColumn = typeColumn;
            }
            let shippingColumn = this.index === 'shipping_options' ? this : false;
            if (shippingColumn) {
                self.shippingColumn = shippingColumn;
            }
            if (self.typeColumn && self.typeColumn.value() === shippingValue && self.shippingColumn) {
                this.showColumn(shippingColumn)
            }
        },
        /**
         * On value change handler.
         * Used to show/hide shipping method column an assign value to entity_identifier field
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            const shippingValue = '1';
            let shippingColumn = uiRegistry.get('index = shipping_options');
            let entityRewardColumn = uiRegistry.get('index = entity_identifier');
            let typeColumn = uiRegistry.get('index = type');
            if (typeColumn.value() === shippingValue) {
                this.showColumn(shippingColumn)
                shippingColumn.value() ? this.updateShippingMethod(entityRewardColumn, this.getShippingLabel(shippingColumn)) : this.updateShippingMethod(entityRewardColumn, '')
            } else {
                this.showColumn(shippingColumn, false)
                entityRewardColumn.enable()
                entityRewardColumn.value('')
            }

            return this._super();
        },
        showColumn: function (element, show = true) {
            if (show) {
                element.visible(true)
                element.enable()
            } else {
                element.visible(false)
                element.disabled()
            }
        },
        updateShippingMethod: function (column, shippingMethod) {
            column.disabled(true)
            column.value(shippingMethod)
        },
        getShippingLabel: function (shipping) {
            let shippingValue = shipping.value();
            let selectedOption = shipping.initialOptions.filter((option) => option.value === shippingValue);
            let shippingLabel = selectedOption[0].label;
            if (shippingLabel.includes('Envio')) {
                shippingLabel = shippingLabel.replace('Envio', 'Envío');
            }
            return shippingLabel;
        },
        setData: function (key, value) {
            this.key = value;
        }
    });
});
