<?php
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
class CartController extends CartControllerCore
{
    
    /*
    * module: separatepackages
    * date: 2021-02-07 14:10:30
    * version: 1.0.1
    */
    public function initContent()
    {
        if (Configuration::isCatalogMode() && Tools::getValue('action') === 'show') {
            Tools::redirect('index.php');
        }
        $presenter = new CartPresenter();
        $presented_cart = $presenter->present($this->context->cart, $shouldSeparateGifts = true);
        $show_option_allow_separate_package = (!$this->context->cart->isAllProductsInStock(true) && Configuration::get('PS_SHIP_WHEN_AVAILABLE'));
        $cart_products = $this->context->cart->getProducts() ;
        $cart_products_quantity_available = 0 ;
        foreach ($cart_products as $cart_product) {
            $cart_products_quantity_available +=  ($cart_product['quantity_available'] <=0) ? 0 : $cart_product['quantity_available'] ;
        }
        if ($cart_products_quantity_available <= 0) {
            $show_option_allow_separate_package = false ;
        }
        $this->context->smarty->assign([
            'cart' => $presented_cart,
            'static_token' => Tools::getToken(false),
            'show_option_allow_separate_package' => $show_option_allow_separate_package,
        ]);
        if (count($presented_cart['products']) > 0) {
            $this->setTemplate('checkout/cart');
        } else {
            $this->context->smarty->assign([
                'allProductsLink' => $this->context->link->getCategoryLink(Configuration::get('PS_HOME_CATEGORY')),
            ]);
            $this->setTemplate('checkout/cart-empty');
        }
        parent::initContent();
    }
    
    /*
    * module: separatepackages
    * date: 2021-02-07 14:10:30
    * version: 1.0.1
    */
    protected function processAllowSeperatedPackage()
    {
        if (!Configuration::get('PS_SHIP_WHEN_AVAILABLE')) {
            return;
        }
        if (Tools::getValue('value') === false) {
            $this->ajaxDie('{"error":true, "error_message": "No value setted"}');
        }
        $this->context->cart->allow_seperated_package = (bool)Tools::getValue('value');
        $this->context->cart->update();
        $this->ajaxDie('{"error":false}');
    }
    /*
    * module: separatepackages
    * date: 2021-02-07 14:10:30
    * version: 1.0.1
    */
    protected function updateCart()
    {
        if ($this->context->cookie->exists()
            && !$this->errors
            && !($this->context->customer->isLogged() && !$this->isTokenValid())
        ) {
            if (Tools::getIsset('add') || Tools::getIsset('update')) {
                $this->processChangeProductInCart();
            } elseif (Tools::getIsset('delete')) {
                $this->processDeleteProductInCart();
            } elseif (Tools::getIsset('allowSeperatedPackage')) {
                $this->processAllowSeperatedPackage(); // wallie
            } elseif (CartRule::isFeatureActive()) {
                if (Tools::getIsset('addDiscount')) {
                    if (!($code = trim(Tools::getValue('discount_name')))) {
                        $this->errors[] = $this->trans(
                            'You must enter a voucher code.',
                            array(),
                            'Shop.Notifications.Error'
                        );
                    } elseif (!Validate::isCleanHtml($code)) {
                        $this->errors[] = $this->trans(
                            'The voucher code is invalid.',
                            array(),
                            'Shop.Notifications.Error'
                        );
                    } else {
                        if (($cartRule = new CartRule(CartRule::getIdByCode($code)))
                            && Validate::isLoadedObject($cartRule)
                        ) {
                            if ($error = $cartRule->checkValidity($this->context, false, true)) {
                                $this->errors[] = $error;
                            } else {
                                $this->context->cart->addCartRule($cartRule->id);
                            }
                        } else {
                            $this->errors[] = $this->trans(
                                'This voucher does not exist.',
                                array(),
                                'Shop.Notifications.Error'
                            );
                        }
                    }
                } elseif (($id_cart_rule = (int) Tools::getValue('deleteDiscount'))
                    && Validate::isUnsignedId($id_cart_rule)
                ) {
                    $this->context->cart->removeCartRule($id_cart_rule);
                    CartRule::autoAddToCart($this->context);
                }
            }
        } elseif (!$this->isTokenValid() && Tools::getValue('action') !== 'show' && !Tools::getValue('ajax')) {
            Tools::redirect('index.php');
        }
    }
}
