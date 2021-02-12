{**
 * 2019 inAzerty
 * module separatepackages
 *
 * @author     inAzerty  <contact@inazerty.com>
 * @copyright  2019 inAzerty
 * @license  commercial
 * @version 1.0.1 from 2020/04/10
 *}

<div id="separatepackages-cart-alert">
{if isset($show_option_allow_separate_package) && $show_option_allow_separate_package}
    <div class="alert alert-danger">
        <p><strong>{l s='Important !' mod='separatepackages'}</strong><br>
          {$separatepackages_checkbox_label_tooltip|escape:'htmlall':'UTF-8'}
       </p>
        <label for="allow_seperated_package" class="checkbox inline">
            <input type="checkbox" name="allow_seperated_package" id="allow_seperated_package" {if Context::getContext()->cart->allow_seperated_package}checked="checked"{/if} autocomplete="off"/>
            {$separatepackages_checkbox_label|escape:'htmlall':'UTF-8'}
          </label>
    </div>
{/if}
</div>
