define([
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/quote',
    'jquery',
    'ko',
    'mage/url'
], function (Component, quote, $, ko, url) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Diselabs_Wallet/payment/wallet'
        },

        walletBalance: ko.observable(0),

        initialize: function () {
            this._super();
            this.getWalletBalance();
            return this;
        },

        getWalletBalance: function () {
            var self = this;
            $.ajax({
                url: url.build('wallet/payment/balance'),
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    self.walletBalance(response.balance);
                }
            });
        },

        getBalance: function () {
            return this.walletBalance();
        },

        hasBalance: function () {
            return parseFloat(this.getBalance()) >= parseFloat(quote.totals().grand_total);
        },

        getMessage: function () {
            if (!this.hasBalance()) {
                return 'Insufficient wallet balance. Current balance: ' + this.getBalance();
            }
            return '';
        }
    });
});
