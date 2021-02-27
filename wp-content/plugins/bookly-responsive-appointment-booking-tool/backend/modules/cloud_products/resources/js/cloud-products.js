jQuery(function ($) {
    'use strict';

    const $prices = $('.bookly-js-product-price-selector'),
        component = {
            items:    '[data-product-price-id]',
            dropdown: '.bookly-js-product-price-dropdown',
            enable:   '.bookly-js-product-enable',
            disable:  '.bookly-js-product-disable',
        },
        $onOffButtons = $('.bookly-js-product-enable,.bookly-js-product-disable'),
        $infoButtons = $('.bookly-js-product-info-button'),
        $revertCancelButtons = $('.bookly-js-product-revert-cancel'),
        $updateRequiredButtons = $('.bookly-js-bookly-update-required'),
        $infoModal = $('#bookly-product-info-modal'),
        infoModal = {
            $loading: $('.bookly-js-loading', $infoModal),
            $content: $('#bookly-info-content', $infoModal),
            $title: $('.modal-title',$infoModal)
        },
        $activationModal = $('#bookly-product-activation-modal'),
        activationModal = {
            $title: $('.modal-title', $activationModal),
            $success: $('.bookly-js-success', $activationModal),
            $fail: $('.bookly-js-fail', $activationModal),
            $content: $('.bookly-js-content', $activationModal),
            $button: $('.bookly-js-action-btn', $activationModal)
        },
        $unsubscribeModal = $('#bookly-product-unsubscribe-modal'),
        $cancelSubscriptionButton = $('#bookly-cancel-subscription'),
        $cancelSubscriptionMethod = $('#bookly_cancel_subscription_method'),
        hash = window.location.href.split('#');

    $prices
        .on('click', component.items, function () {
            const $selector = $(this).parents('.bookly-js-product-price-selector'),
                productPriceId = $(this).data('product-price-id');
            $selector.data('pp-id', productPriceId);
            $('.bookly-js-product-price', $selector).html($(this).html());
        });

    for (var product in BooklyL10n.products) {
        let productActive = BooklyL10n.products[product].active,
            $card = $('[data-product="' + product + '"]')
        ;
        if (productActive) {
            $card.removeClass('bg-light').addClass('bg-white');
            $(component.items, $card).each(function () {
                const productPriceId = $(this).data('product-price-id');
                let selected = false;
                BooklyL10n.subscriptions.forEach(function (item) {
                    if (selected === false && item.product_price_id == productPriceId) {
                        selected = true;
                    }
                });
                if (selected) {
                    $(this).trigger('click');
                }
            });
        }

        $(component.enable, $card).toggle(!productActive);
        $(component.disable, $card).toggle(productActive);
        $(component.dropdown, $card).prop('disabled', productActive).toggleClass('disabled', productActive);

        if (!productActive) {
            if ($('.bookly-js-best-offer', $card).length > 0) {
                $('.bookly-js-best-offer', $card).trigger('click');
            } else if ($('.bookly-js-users-choice', $card).length > 0) {
                $('.bookly-js-users-choice', $card).trigger('click')
            } else {
                $(component.items + ':first', $card).first().trigger('click');
            }
        }
    }

    $infoButtons.on('click', function () {
        const ladda = Ladda.create(this);
        const product =  $(this).closest('.bookly-js-cloud-product').data('product');
        ladda.start();
        infoModal.$loading.show();
        infoModal.$title.html(BooklyL10n.products[product].info_title).show();
        infoModal.$content.hide();
        $infoModal.booklyModal('show');
        $.ajax({
            type: 'POST',
            url : ajaxurl,
            data: {
                action: 'bookly_cloud_get_product_info',
                product: product,
                csrf_token: BooklyL10n.csrfToken,
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    infoModal.$loading.hide();
                    infoModal.$content.html(response.data.html).show();
                } else {
                    booklyAlert({error: [response.data.message]});
                }
            }
        }).always(ladda.stop);
    });

    $('.bookly-js-product-login-button').on('click', function (e) {
        e.preventDefault();
        $(document.body).trigger('bookly.cloud.auth.form', ['login']);
        $('#bookly-cloud-auth-modal').booklyModal('show');
    });

    $onOffButtons.on('click', function () {
        const $button = $(this);
        const product = $(this).closest('.bookly-js-cloud-product').data('product');
        const status = $button.hasClass('bookly-js-product-enable') ? 1 : 0;
        let product_price;
        if (status) {
            product_price = $(this).parents('.bookly-js-product-price-selector').data('pp-id');
        }

        if (!status && BooklyL10n.products[product].has_subscription) {
            $unsubscribeModal.data('product', product);
            $unsubscribeModal.booklyModal('show');
        } else {
            changeProductStatus(product, status, product_price, $button)
        }
    });

    $revertCancelButtons.on('click', function () {
        const $button = $(this);
        const product = $(this).closest('.bookly-js-cloud-product').data('product');
        const ladda = Ladda.create($button.get(0));
        let action;
        switch (product) {
            case 'zapier':
                action = 'bookly_cloud_zapier_revert_cancel';
                break;
            default:
                return;
        }

        ladda.start();
        $.ajax({
            type: 'POST',
            url : ajaxurl,
            data: {
                action: action,
                csrf_token: BooklyL10n.csrfToken,
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    booklyAlert({error: [response.data.message]});
                    ladda.stop();
                }
            }
        });
    });

    $cancelSubscriptionButton.on('click', function () {
        changeProductStatus($unsubscribeModal.data('product'), $cancelSubscriptionMethod.find("input:checked").val(), 0, $(this));
    });

    function changeProductStatus(product, status, product_price, $button) {
        const ladda = Ladda.create($button.get(0));
        let action;
        switch (product) {
            case 'stripe':
                action = 'bookly_cloud_stripe_change_status';
                break;
            case 'sms':
                action = 'bookly_cloud_sms_change_status';
                break;
            case 'zapier':
                action = 'bookly_cloud_zapier_change_status';
                break;
            default:
                return;
        }

        ladda.start();
        $.ajax({
            type: 'POST',
            url : ajaxurl,
            data: {
                action: action,
                status: status,
                product_price: product_price,
                csrf_token: BooklyL10n.csrfToken,
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    if (status == '1') {
                        window.location.href = response.data.redirect_url;
                        if (product !== 'stripe') {
                            window.location.reload();
                        }
                    } else {
                        window.location.reload();
                    }
                } else {
                    booklyAlert({error: [response.data.message]});
                    ladda.stop();
                }
            }
        });
    }
    function showProductActivationMessage(product, status) {
        switch (product) {
            case 'stripe':
            case 'sms':
            case 'zapier':
                $activationModal.booklyModal('show');
                activationModal.$title.html(BooklyL10n.products[product].title);
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {
                            action: 'bookly_cloud_get_product_activation_message',
                            product: product,
                            status: status,
                            csrf_token: BooklyL10n.csrfToken,
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                activationModal.$success.show();
                                activationModal.$content.html(response.data.content);
                                if (response.data.button) {
                                    activationModal.$button
                                        .find('span').html(response.data.button.caption).end().off()
                                        .on('click', function () {
                                            window.location.href = response.data.button.url;
                                        })
                                        .show();
                                }
                            } else {
                                activationModal.$fail.show();
                                activationModal.$content.html(response.data.content);
                            }
                        }
                    });
                    break;
        }
    }

    $updateRequiredButtons.on('click', function (e) {
        $('#bookly-product-update-required-modal').booklyModal('show');
    });

    $activationModal
        .on('show.bs.modal', function () {
            activationModal.$success.hide();
            activationModal.$fail.hide();
            activationModal.$content.html('<div class="bookly-loading"></div>');
            activationModal.$button.hide();
        });

    if (hash.length > 1) {
        let hashObj = {};
        hash[1].split('&').forEach(function (part) {
            var params = part.split('=');
            hashObj[params[0]] = params[1];
        });

        if (hashObj.hasOwnProperty('cloud-product')) {
            if (hashObj.hasOwnProperty('status')) {
                showProductActivationMessage(hashObj['cloud-product'], hashObj['status']);
                if ('pushState' in history) {
                    history.pushState('', document.title, window.location.pathname + window.location.search);
                } else {
                    window.location.href = '#';
                }
            }
        }
    }

});