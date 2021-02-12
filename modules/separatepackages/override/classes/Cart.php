<?php
/**
 * 2019 inAzerty
 * module separatepackages
 *
 * @author     inAzerty  <contact@inazerty.com>
 * @copyright  2019 inAzerty
 * @license  commercial
 * version 1.0.1 from 2020/04/10
 */

class Cart extends CartCore
{
    public function checkQuantities($returnProductOnFailure = false)
    {
        if (Configuration::isCatalogMode() && !defined('_PS_ADMIN_DIR_')) {
            return false;
        }

        foreach ($this->getProducts() as $product) {
            if (!$this->allow_seperated_package &&
                !$product['allow_oosp'] &&
                StockAvailable::dependsOnStock($product['id_product']) &&
                $product['advanced_stock_management'] &&
                (bool) Context::getContext()->customer->isLogged() &&
                ($delivery = $this->getDeliveryOption()) &&
                !empty($delivery)
            ) {
                $product['stock_quantity'] = StockManager::getStockByCarrier(
                    (int) $product['id_product'],
                    (int) $product['id_product_attribute'],
                    $delivery
                );
            }

            // var_dump($product['allow_oosp']);
            // echo '<br>';
            // var_dump($product['available_for_order']);
            // echo '<br>';
            // var_dump($product['stock_quantity']);
            // echo '<br>';
            // var_dump($product['cart_quantity']);
            // echo '<br>';
            // die();

            if (!$product['active'] ||
                !$product['available_for_order'] ||
                (!$product['allow_oosp'] && $product['stock_quantity'] < $product['cart_quantity'])
            ) {
                return $returnProductOnFailure ? $product : false;
            }
            //   die(print_r($product));
            if (!$product['allow_oosp']) {
                $productQuantity = Product::getQuantity(
                    $product['id_product'],
                    $product['id_product_attribute'],
                    null,
                    $this,
                    $product['id_customization']
                );
                if ($productQuantity < 0) {
                    return $returnProductOnFailure ? $product : false;
                }
            }
        }

        return true;
    }

    /**
     * Are all products of the Cart in stock?
     *
     * @param bool $ignore_virtual Ignore virtual products
     * @param bool $exclusive (DEPRECATED) If true, the validation is exclusive : it must be present product in stock and out of stock
     *
     * @since 1.5.0
     *
     * @return bool False if not all products in the cart are in stock
     */
    public function isAllProductsInStock($ignoreVirtual = false, $exclusive = false)
    {
        if (func_num_args() > 1) {
            @trigger_error(
                '$exclusive parameter is deprecated since version 1.7.3.2 and will be removed in the next major version.',
                E_USER_DEPRECATED
            );
        }
        $productOutOfStock = 0;
        $productInStock = 0;

        foreach ($this->getProducts(false, false, null, false) as $product) {
            if ($ignoreVirtual && $product['is_virtual']) {
                continue;
            }
            $idProductAttribute = !empty($product['id_product_attribute']) ? $product['id_product_attribute'] : null;
            $availableOutOfStock = Product::isAvailableWhenOutOfStock($product['out_of_stock']);
            $productQuantity = Product::getQuantity(
                $product['id_product'],
                $idProductAttribute,
                null,
                $this,
                $product['id_customization']
            );

          
            if (!$exclusive
                // && ($productQuantity < 0 && !$availableOutOfStock)
                && ($productQuantity < 0 && $availableOutOfStock)
            ) {
                return false;
            } elseif ($exclusive) {
                if ($productQuantity <= 0) {
                    ++$productOutOfStock;
                } else {
                    ++$productInStock;
                }

                if ($productInStock > 0 && $productOutOfStock > 0) {
                    return false;
                }
            }
        }

        return true;
    }
}
