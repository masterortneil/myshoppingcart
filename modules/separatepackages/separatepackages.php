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

if (!defined('_PS_VERSION_')) {
    exit;
}
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;

class Separatepackages extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'separatepackages';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.1';
        $this->author = 'inAzerty';
        $this->need_instance = 0;
        $this->module_key = '4502a37a4316225da21b5ca68802c843';

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('On backorder: send available product first');
        $this->description = $this->l('Extends “send available product first“ core functionality from PS 1.6 to PS 1.7  It allows customer to split order in two when some products are in stock (shipped first) and some other products are not in stock (shipped when available). Two orders will be created in backoffice with two different statuses depending on payment method.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        $languages = Language::getLanguages(false);

        $values = array();


        foreach ($languages as $lang) {
            $values['SEPARATEPACKAGES_CHECKBOX_LABEL'][(int)$lang['id_lang']] = $this->l('Send available products first', false, 'fr') ;
            $values['SEPARATEPACKAGES_CHECKBOX_LABEL_TOOLTIP'][(int)$lang['id_lang']] = $this->l('Some products are not available immediately. Your order will be shipped when all products are available (+/-10 days). Check the box below if you want available products to be shipped first (shipping cost may be applied twice).', false, 'fr');
            $values['SEPARATEPACKAGES_CHECKOUT_MESSAGE'][(int)$lang['id_lang']] = $this->l('Your order will be shipped in 2 packages according to your choice', false, 'fr');


            Configuration::updateValue('SEPARATEPACKAGES_CHECKBOX_LABEL', $values['SEPARATEPACKAGES_CHECKBOX_LABEL']);
            Configuration::updateValue('SEPARATEPACKAGES_CHECKBOX_LABEL_TOOLTIP', $values['SEPARATEPACKAGES_CHECKBOX_LABEL_TOOLTIP']);
            Configuration::updateValue('SEPARATEPACKAGES_CHECKOUT_MESSAGE', $values['SEPARATEPACKAGES_CHECKOUT_MESSAGE']);
        }

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
						// MOD 067.6: Move hook position from cart-footer to checkout block.
						// $this->registerHook('displayShoppingCartFooter') &&
						$this->registerHook('displayExpressCheckoutBefore') &&
						// MOD 067.6 END
            $this->registerHook('displayBeforeCarrier');
    }

    public function uninstall()
    {
        Configuration::deleteByName('SEPARATEPACKAGES_CHECKBOX_LABEL');
        Configuration::deleteByName('SEPARATEPACKAGES_CHECKBOX_LABEL_TOOLTIP');
        Configuration::deleteByName('SEPARATEPACKAGES_CHECKOUT_MESSAGE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';

        if (((bool)Tools::isSubmit('submitSeparatepackagesModule')) == true) {
            $this->postProcess();
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        $this->context->smarty->assign(
            array(
                'module_dir' => $this->_path,
                'name' => $this->name,
                'display_name' => $this->displayName,
                'description' => $this->description,
                'ps_ship_when_available_configuration' => Configuration::get('PS_SHIP_WHEN_AVAILABLE')
            )
        );

        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        $output_footer = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure-footer.tpl');

        if (defined('_PS_ADMIN_DIR_')) {
            $this->admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
            $this->admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $this->admin_webpath);
        }

        $this->context->controller->addCSS(__PS_BASE_URI__.$this->admin_webpath.'/themes/new-theme/public/theme.css', 'all', 1);
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/back.js', 1);

        return $output.$this->renderForm().$output_footer;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $lang->id;
        // $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSeparatepackagesModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Cart checkbox label'),
                        'name' => 'SEPARATEPACKAGES_CHECKBOX_LABEL',
                        'desc' => $this->l('This text will label the checkbox below cart table on cart page.'),
                        'lang' => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Cart checkbox label precisions'),
                        'name' => 'SEPARATEPACKAGES_CHECKBOX_LABEL_TOOLTIP',
                        'desc' => $this->l('You can give here more informations to customer about separate package. This text will be displayed above the checkbox.'),
                        'lang' => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Message on checkout page displayed before carriers'),
                        'name' => 'SEPARATEPACKAGES_CHECKOUT_MESSAGE',
                        'desc' => $this->l('This message will be displayed right before carrier choice on checkout page to remind customer his choice in case he choosed separate packages. Displayed as "alert-success" message box.'),
                        'lang' => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false);
        $fields = array();

        foreach ($languages as $lang) {
            $fields['SEPARATEPACKAGES_CHECKBOX_LABEL'][$lang['id_lang']] = Tools::getValue('SEPARATEPACKAGES_CHECKBOX_LABEL_'.$lang['id_lang'], Configuration::get('SEPARATEPACKAGES_CHECKBOX_LABEL', $lang['id_lang']));

            $fields['SEPARATEPACKAGES_CHECKBOX_LABEL_TOOLTIP'][$lang['id_lang']] = Tools::getValue('SEPARATEPACKAGES_CHECKBOX_LABEL_TOOLTIP_'.$lang['id_lang'], Configuration::get('SEPARATEPACKAGES_CHECKBOX_LABEL_TOOLTIP', $lang['id_lang']));

            $fields['SEPARATEPACKAGES_CHECKOUT_MESSAGE'][$lang['id_lang']] = Tools::getValue('SEPARATEPACKAGES_CHECKOUT_MESSAGE_'.$lang['id_lang'], Configuration::get('SEPARATEPACKAGES_CHECKOUT_MESSAGE', $lang['id_lang']));
        }


        return $fields;

        // return array(
        //     'SEPARATEPACKAGES_CHECKBOX_LABEL' => Configuration::get('SEPARATEPACKAGES_CHECKBOX_LABEL', true),
        //     'SEPARATEPACKAGES_CHECKBOX_LABEL_TOOLTIP' => Configuration::get('SEPARATEPACKAGES_CHECKBOX_LABEL_TOOLTIP', true),
        //     'SEPARATEPACKAGES_CHECKOUT_MESSAGE' => Configuration::get('SEPARATEPACKAGES_CHECKOUT_MESSAGE', true),
        // );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        $languages = Language::getLanguages(false);
        $values = array();


        foreach (array_keys($form_values) as $key) {
            foreach ($languages as $lang) {
                // var_dump(Tools::getValue($key.'_'.$lang['id_lang']));
                // var_dump($key.'_'.$lang['id_lang']);
                // die();
                // Configuration::updateValue($key, Tools::getValue($key));
                // Configuration::updateValue($key, Tools::getValue($key.'_'.$lang['id_lang']));
                $values[$key][$lang['id_lang']] = Tools::getValue($key.'_'.$lang['id_lang']);
                //$values['BANNER_DESC'][$lang['id_lang']] = Tools::getValue('BANNER_DESC_'.$lang['id_lang']);
            }
        }

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, $values[$key]);
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/front.js');
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
    }

    public function hookDisplayShoppingCartFooter()
    {
        $this->context->smarty->assign(
            array(
                'separatepackages_checkbox_label' => Configuration::get('SEPARATEPACKAGES_CHECKBOX_LABEL', $this->context->language->id),
                'separatepackages_checkbox_label_tooltip' => Configuration::get('SEPARATEPACKAGES_CHECKBOX_LABEL_TOOLTIP', $this->context->language->id)
            )
        );
        return $this->context->smarty->fetch($this->local_path.'views/templates/hook/cart-footer.tpl');
    }
		// MOD 067.7: Move hook position from cart-footer to checkout block.
		public function hookDisplayExpressCheckoutBefore() {
			return $this->hookDisplayShoppingCartFooter();
		}
		// MOD 067.7 END
    public function hookDisplayBeforeCarrier()
    {
        $this->context->smarty->assign(
            array(
                'allow_seperated_package' => $this->context->cart->allow_seperated_package,
                'separatepackages_checkout_message' => Configuration::get('SEPARATEPACKAGES_CHECKOUT_MESSAGE', $this->context->language->id)

            )
        );
        // MOD 059: Add the cart footer.
				return $this->hookDisplayShoppingCartFooter() . $this->context->smarty->fetch($this->local_path.'views/templates/hook/before-carrier.tpl');
				// MOD 059 END
    }
}
