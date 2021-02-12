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
class OrderDetail extends OrderDetailCore
{
    /**
     * Create an order detail liable to an id_order.
     *
     * @param object $order
     * @param object $cart
     * @param array $product
     * @param int $id_order_status
     * @param int $id_order_invoice
     * @param bool $use_taxes set to false if you don't want to use taxes
     */
    /*
    * module: separatepackages
    * date: 2021-02-07 14:10:30
    * version: 1.0.1
    */
    protected function create(Order $order, Cart $cart, $product, $id_order_state, $id_order_invoice, $use_taxes = true, $id_warehouse = 0)
    {
        if ($use_taxes) {
            $this->tax_calculator = new TaxCalculator();
        }
        $this->id = null;
        $this->product_id = (int) $product['id_product'];
        $this->product_attribute_id = $product['id_product_attribute'] ? (int) $product['id_product_attribute'] : 0;
        $this->id_customization = $product['id_customization'] ? (int) $product['id_customization'] : 0;
        $this->product_name = $product['name'] .
            ((isset($product['attributes']) && $product['attributes'] != null) ?
                ' - ' . $product['attributes'] : '');
        $this->product_quantity = (int) $product['cart_quantity'];
        $this->product_ean13 = empty($product['ean13']) ? null : pSQL($product['ean13']);
        $this->product_isbn = empty($product['isbn']) ? null : pSQL($product['isbn']);
        $this->product_upc = empty($product['upc']) ? null : pSQL($product['upc']);
        $this->product_reference = empty($product['reference']) ? null : pSQL($product['reference']);
        $this->product_supplier_reference = empty($product['supplier_reference']) ? null : pSQL($product['supplier_reference']);
        $this->product_weight = $product['id_product_attribute'] ? (float) $product['weight_attribute'] : (float) $product['weight'];
        $this->id_warehouse = $id_warehouse;
        $product_quantity = (int) Product::getQuantity($this->product_id, $this->product_attribute_id, null, $cart);
        $this->product_quantity_in_stock = ($product_quantity - (int) $product['cart_quantity'] < 0) ? $product_quantity : (int) $product['cart_quantity'];
        $han = $cart->getProductQuantity(
            $this->product_id,
            $this->product_attribute_id,
            $this->id_customization,
            $cart->id_address_delivery
        );
        if ($cart->allow_seperated_package && $product_quantity < 0) {
            $lol = $product_quantity * -1 ;
            if ($product['cart_quantity'] + $lol == $han['quantity']) {
                $this->product_quantity_in_stock = 0 ;
            }
        }
        $this->setVirtualProductInformation($product);
        $this->checkProductStock($product, $id_order_state);
        if ($use_taxes) {
            $this->setProductTax($order, $product);
        }
        $this->setShippingCost($order, $product);
        $this->setDetailProductPrice($order, $cart, $product);
        $this->id_order_invoice = (int) $id_order_invoice;
        $this->id_shop = (int) $product['id_shop'];
        $this->save();
        if ($use_taxes) {
            $this->saveTaxCalculator($order);
        }
        unset($this->tax_calculator);
    }
}
