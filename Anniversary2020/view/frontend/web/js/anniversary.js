define(['jquery', 'jquery/validate'], function ($) {

    $(document).ready(function () {

        //Evento que se ejecuta al registrarse un customer
        $(document).on('customerRegistered', function (e) {
            var isAnniversary2020 = $('[name="Anniversary2020"]');
            if (isAnniversary2020.val()) {
                onPromoClick({ 'name': 'Registro cliente con Aniversario2020' });
            }
        });

        $('button[name=rascaButton]').attr("disabled", false);

        $('body').on('submit', '#rasca-form', function (ev) {
            if ($(this).hasClass('loggedIn')) {
                ev.preventDefault();
                let form = $(this);
                $(form).validate({
                    rules: {
                        rascaCode: {
                            required: true,
                            isAlphanumeric: true
                        },
                    },
                    messages: {
                        rascaCode: {
                            isAlphanumeric: "Formato no válido"
                        }
                    },
                    ignore: '.select2-input, .select2-focusser',
                    errorClass: "has-error",
                    highlight: function (element, errorClass) {
                        if ($(element).is("select")) {
                            $(element).parent().find("span.select2-selection").addClass(errorClass);
                        } else {
                            $(element).closest("div.input").addClass(errorClass);
                        }
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        if ($(element).is("select")) {
                            $(element).parent().find("span.select2-selection").removeClass(errorClass);
                        } else {
                            $(element).closest("div.input").removeClass(errorClass);
                        }
                    },
                    errorPlacement: function (error, element) {
                        if ($(element).is("select")) {
                            $(element).parent().find("span.selection").after(error);
                        } else if (!$(element).is("input[type=checkbox]")) {
                            $(element).after(error);
                        }
                    }
                });
                if ($(form).valid()) {
                    disableSubmitButton(form);
                    hideRascasError();
                    $.ajax({
                        cache: false,
                        type: 'post',
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (response) {
                            let successOk = response.success;
                            if (successOk) {
                                if (typeof response.isError !== 'undefined') {
                                    $registerRasca = { 'name': 'Registro código Aniversario2020 OK' };
                                    if (response.isError) {
                                        $registerRasca = { 'name': 'Registro código Aniversario2020 KO' };
                                    }
                                    onPromoClick($registerRasca);
                                }
                                window.location.href = response.redirectUrl;
                            } else {
                                onPromoClick({ 'name': 'Registro código Aniversario2020 KO' });
                                showRascasError(response.message);
                                enableSubmitButton(form);
                            }
                        }
                    });
                }
            } else {
                ev.preventDefault();
                let rascaCode = $('[name=rascaCode]').val();
                let formLogin = $('form.form-login');
                let formRegister = $('form.form-register');
                let hiddenInput = "<input type='hidden' name='Anniversary2020' value='" + rascaCode + "' />";
                formLogin.append(hiddenInput);
                formRegister.append(hiddenInput);
                $('.dialogBox-modal-login').toggleModal();
            }
        });

        $('body').on('submit', '#rasca-customer-edit-form', function (ev) {
            ev.preventDefault();
            let form = $(this).closest('form');

            $(form).validate({
                rules: {
                    customer_dni: {
                        required: true,
                        minlength: 9,
                        maxlength: 9,
                        dnivalidator: true
                    },
                    customer_telephone: {
                        required: true,
                        phonevalidator: true
                    },
                    customer_island: "required",
                    terms_and_conditions: "required"
                },
                messages: {
                    customer_dni: {
                        minlength: "Formato no válido",
                        maxlength: "Formato no válido",
                        dnivalidator: "Formato no válido"
                    }
                },
                ignore: '.select2-input, .select2-focusser',
                errorClass: "has-error",
                highlight: function (element, errorClass) {
                    if ($(element).is("select")) {
                        $(element).parent().find("span.select2-selection").addClass(errorClass);
                    } else {
                        $(element).closest("div.input").addClass(errorClass);
                    }
                },
                unhighlight: function (element, errorClass, validClass) {
                    if ($(element).is("select")) {
                        $(element).parent().find("span.select2-selection").removeClass(errorClass);
                    } else {
                        $(element).closest("div.input").removeClass(errorClass);
                    }
                },
                errorPlacement: function (error, element) {
                    if ($(element).is("select")) {
                        $(element).parent().find("span.selection").after(error);
                    } else if (!$(element).is("input[type=checkbox]")) {
                        $(element).after(error);
                    }
                }
            });

            if ($(form).valid()) {
                disableSubmitButton(form);
                hideRascasError();
                $.ajax({
                    cache: false,
                    type: 'post',
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function (response) {
                        let successOk = response.success;
                        if (successOk) {
                            if (typeof response.isError !== 'undefined') {
                                $registerRasca = { 'name': 'Registro código Aniversario2020 OK' };
                                if (response.isError) {
                                    $registerRasca = { 'name': 'Registro código Aniversario2020 KO' };
                                }
                                onPromoClick($registerRasca);
                            }
                            window.location.href = response.redirectUrl;
                        } else {
                            onPromoClick({ 'name': 'Registro código Aniversario2020 KO' });
                            showRascasError(response.message);
                            enableSubmitButton(form);
                        }
                    }
                });
            }
        });

    });

    function showRascasError(message) {
        let errorDiv = $('div.rascas-error');
        errorDiv.html(message);
        errorDiv.show();
    }

    function hideRascasError() {
        let errorDiv = $('div.rascas-error');
        errorDiv.html('');
        errorDiv.hide();
    }

    function onPromoClick(promoObj) {
        if (typeof dataLayer !== 'undefined') {
            dataLayer.push({
                'event': 'promotionClick', 'ecommerce': {
                    'promoClick': {
                        'promotions': [promoObj]
                    }
                }
            });
        }
    }

    function disableSubmitButton(form) {
        form.find('button[type=submit]').attr('disabled', true).addClass('button__complex-checkout checked');
    }

    function enableSubmitButton(form) {
        form.find('button[type=submit]').attr('disabled', false).removeClass('button__complex-checkout checked');
    }


    $.validator.addMethod("isAlphanumeric", function (value, element) {
        return value.match(/^[a-zA-Z0-9ñÑ]+$/) !== null;
    }, "Debe introducir un código válido");


});
