/**
 * 2019 inAzerty
 * module separatepackages
 *
 * @author     inAzerty  <contact@inazerty.com>
 * @copyright  2019 inAzerty
 * @license  commercial
 * version 1.0.1 from 2020/04/10
 */

$(document).on('click', '#allow_seperated_package', function () {
    $.ajax({
        type: 'POST',
        headers: {
            "cache-control": "no-cache"
        },
        url: prestashop.urls.pages.cart + '?rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType: 'json',
        // data: 'controller=cart&ajax=true'
        data: '&ajax=true' +
            '&summary=true' +
            '&allowSeperatedPackage=true' +
            '&value=' +
            ($(this).prop('checked') ? '1' : '0') +
            '&token=' + prestashop.static_token +
            '&allow_refresh=1',
        success: function (jsonData) {
            if (jsonData.hasError) {
                var errors = '';
                for (var error in jsonData.errors)
                    //IE6 bug fix
                    if (error !== 'indexOf')
                        errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
                if (!!$.prototype.fancybox)
                    $.fancybox.open([{
                        type: 'inline',
                        autoScale: true,
                        minHeight: 30,
                        content: '<p class="fancybox-error">' + errors + '</p>'
                    }], {
                        padding: 0
                    });
                else
                    alert(errors);
                $('input[name=quantity_' + id + ']').val($('input[name=quantity_' + id + '_hidden]').val());
            } else {

                prestashop.emit('updateCart', {
                    reason: "separate packages module"
                  });
                 
                /*
                if (jsonData.refresh)
                    window.location.href = window.location.href;
                //updateCartSummary(jsonData.summary);
                if (window.ajaxCart != undefined)
                    ajaxCart.updateCart(jsonData);
               // updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
                //updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
                if (typeof (getCarrierListAndUpdate) !== 'undefined')
                    getCarrierListAndUpdate();
                if (typeof (updatePaymentMethodsDisplay) !== 'undefined')
                    updatePaymentMethodsDisplay();
                    */
            }
        }
    });
});


// reload alert with checkbox in case we add/remove some products that can trigger changement  
prestashop.on(
    'updateCart',
    function (event) {
      
        if($('#separatepackages-cart-alert').length > 0){
            $('#separatepackages-cart-alert').load(prestashop.urls.pages.cart + '?action=show #separatepackages-cart-alert > *')
        }
        if($('#block-reassurance').length > 0){
            $('#block-reassurance').load(prestashop.urls.pages.cart + '?action=show #block-reassurance > *')
        }
    }
);
