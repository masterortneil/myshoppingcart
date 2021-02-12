{** 
 * 2019 inAzerty
 * module separatepackages
 * 
 * @author     inAzerty  <contact@inazerty.com>
 * @copyright  2019 inAzerty
 * @license  commercial
 * @version 1.0.1 from 2020/04/10
 *}

<div class="panel">
	<h4><i class="icon icon-bug"></i> {l s='Troubleshooting and theme improvement' mod='separatepackages'}</h4>
		<dl>
            <dt>{l s='I want to display "in stock" / "out of stock" on cart detailed product line' mod='separatepackages'}</dt>
            <dd>
                &raquo;  
                {l s='Locate the file' mod='separatepackages'} <code>/themes/YOUR_THEME_OR_CHILD_THEME/templates/checkout/_partials/cart-detailed-product-line.tpl</code> {l s='in your theme or child theme and add this piece of code on line 71: ' mod='separatepackages'}
                <br><br>
<pre>&lt;div class=&quot;product-line-info&quot;&gt; 
    &lt;div class=&quot;label&quot;&gt; 
        {ldelim}if $product.stock_quantity &gt;= $product.quantity{rdelim}
            &lt;i data-toggle=&quot;tooltip&quot; title=&quot;{ldelim}l s='In stock' d='Shop.Theme.Catalog'{rdelim}&quot; class=&quot;material-icons text-success&quot;&gt;&lt;/i&gt; 
            &lt;span class=&quot;badge d-inline-block badge-success&quot;&gt;
                {ldelim}l s='In stock' d='Shop.Theme.Catalog'{rdelim}
            &lt;/span&gt; 
        {ldelim}/if{rdelim} 
        {ldelim}if $product.stock_quantity &lt; $product.quantity{rdelim} 
            &lt;i data-toggle=&quot;tooltip&quot; title=&quot;{ldelim}l s='Out of stock' d='Shop.Theme.Catalog'{rdelim}&quot; class=&quot;d-none d-inline align-baseline material-icons text-warning&quot;&gt;&lt;/i&gt;
            &lt;span class=&quot;badge d-inline-block badge-warning&quot;&gt;{ldelim}l s='Out of stock'}&lt;br&gt;({ldelim}($product.stock_quantity &gt; 0)?$product.stock_quantity:'0'} {ldelim}l s='In stock' d='Shop.Theme.Catalog'{rdelim})&lt;/span&gt; 
        {ldelim}/if{rdelim} 
    &lt;/div&gt;
&lt;/div&gt;</pre>
                <br>
                {l s='Line number may be different depending on theme. This line number is for Classic default theme.' mod='separatepackages'}
                <br>
                {l s='Expected result on Classic theme cart page:' mod='separatepackages'}
                <br>
                <img src="https://demo.walliecreation.com/prestashop/img/separatepackages_cart.png" class="img-responsive" alt="Expected result">
            </dd>

             <dt>{l s='After doing the improvement above, I want to translate the "Out of stock" label.' mod='separatepackages'}</dt>
            <dd>
                &raquo;  
                {l s='Go to International > Translations > Themes translations > Your theme or child theme > Your language, click "modify", then find "Messages" section as shown below:' mod='separatepackages'}
                <br>
                 <img src="https://demo.walliecreation.com/prestashop/img/separatepackages_translation.png" class="img-responsive" alt="Expected screen">
            </dd>

            <dt>{l s='Markers not showing up while using So Flexibilité 3.1.12 when separate packages option is checked' mod='separatepackages'}</dt>
            <dd>
                &raquo;  
                {l s='Change line 354 in file' mod='separatepackages'}<br>
                <code>/modules/soflexibilite/views/js/front_flexibilite.js</code><br>
                {l s='from:' mod='separatepackages'}
                <code>switch ($(this).val().split(',').join(''))</code><br>
                {l s='to:' mod='separatepackages'}
                <code>switch ($(this).val().split(',')[0])</code>
                <br><br>
                {l s='Do not change module core file directly, use template inheritance instead.' mod='separatepackages'}
            </dd>
        </dl>
</div>

<div class="panel clearfix">
	<h4 id="other_modules" class="panel-heading">{l s='Take a look to our other modules' mod='separatepackages'}</h4>
	
    <div id="modules-list-container-other" data-current-module="{$name|escape:'htmlall':'UTF-8'}" class="row modules-list" data-name="other"></div>

</div>
