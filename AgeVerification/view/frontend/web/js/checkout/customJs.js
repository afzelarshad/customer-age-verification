define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'mage/url',
        'jquery/ui',
        'mage/validation'
    ],
    function (ko, $, Component, url,validation) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Customer_AgeVerification/checkout/dob-fields'
            },

            initObservable: function () {
                this.CheckVals = ko.observable('');

                this.SaveOP = function (data, event) {
                    var linkUrls = url.build('psc/checkout/saveinquote');
                    var age = validate();
                    $.ajax({
                        showLoader: true,
                        url: linkUrls,
                        data: {dob: this.CheckVals},
                        type: "POST",
                        dataType: 'json'
                    }).done(function (data) {
                        console.log('success');
                    });
                }
                return this;
            },
            /**
             * @return {jQuery}
             */
            /**
             * Validate the custom field
             * @returns {Boolean}
             */
            validate: function () {
                var field = '#lipampesanumber';
                var errorMessage = 'This field is required.';

                // Perform custom validation logic here
                var isValid = $(field).val() !== ''; // Example validation: Check if the field is not empty

                if (!isValid) {
                    $(field).addClass('mage-error');
                    $(field).attr('aria-invalid', 'true');
                    $(field).siblings('.mage-error').text(errorMessage).show();
                } else {
                    $(field).removeClass('mage-error');
                    $(field).removeAttr('aria-invalid');
                    $(field).siblings('.mage-error').hide();
                }

                return isValid;
            }
        });
    }
);
