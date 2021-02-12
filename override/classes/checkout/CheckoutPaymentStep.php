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
class CheckoutPaymentStep extends CheckoutPaymentStepCore
{
    /*
    * module: separatepackages
    * date: 2021-02-07 14:10:30
    * version: 1.0.1
    */
    private $selected_payment_option;
    /*
    * module: separatepackages
    * date: 2021-02-07 14:10:30
    * version: 1.0.1
    */
    public function handleRequest(array $requestParams = array())
    {
        $allProductsInStock = $this->getCheckoutSession()->getCart()->isAllProductsInStock();
        if ($allProductsInStock !== true) {
            $cartShowUrl = $this->context->link->getPageLink(
                'cart',
                null,
                $this->context->language->id,
                array(
                    'action' => 'show',
                ),
                false,
                null,
                false
            );
        }
        if (isset($requestParams['select_payment_option'])) {
            $this->selected_payment_option = $requestParams['select_payment_option'];
        }
        $this->setTitle(
            $this->getTranslator()->trans(
                'Payment',
                array(),
                'Shop.Theme.Checkout'
            )
        );
    }
    /*
    * module: separatepackages
    * date: 2021-02-07 14:10:30
    * version: 1.0.1
    */
    public function render(array $extraParams = array())
    {
        $isFree = 0 == (float) $this->getCheckoutSession()->getCart()->getOrderTotal(true, Cart::BOTH);
        $paymentOptions = $this->paymentOptionsFinder->present($isFree);
        $conditionsToApprove = $this->conditionsToApproveFinder->getConditionsToApproveForTemplate();
        $deliveryOptions = $this->getCheckoutSession()->getDeliveryOptions();
        $deliveryOptionKey = $this->getCheckoutSession()->getSelectedDeliveryOption();
        if (isset($deliveryOptions[$deliveryOptionKey])) {
            $selectedDeliveryOption = $deliveryOptions[$deliveryOptionKey];
        } else {
            $selectedDeliveryOption = 0;
        }
        unset($selectedDeliveryOption['product_list']);
        $assignedVars = array(
            'is_free' => $isFree,
            'payment_options' => $paymentOptions,
            'conditions_to_approve' => $conditionsToApprove,
            'selected_payment_option' => $this->selected_payment_option,
            'selected_delivery_option' => $selectedDeliveryOption,
            'show_final_summary' => Configuration::get('PS_FINAL_SUMMARY_ENABLED'),
        );
        return $this->renderTemplate($this->getTemplate(), $extraParams, $assignedVars);
    }
}
