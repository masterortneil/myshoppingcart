{** 
 * 2019 inAzerty
 * module separatepackages
 * 
 * @author     inAzerty  <contact@inazerty.com>
 * @copyright  2019 inAzerty
 * @license  commercial
 * @version 1.0.1 from 2020/04/10
 *}

{if isset($allow_seperated_package) && $allow_seperated_package}
    <div class="alert alert-success">
        {$separatepackages_checkout_message|escape:'htmlall':'UTF-8'}
    </div>
{/if}