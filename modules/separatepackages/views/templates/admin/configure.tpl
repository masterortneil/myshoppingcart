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
	<h4><i class="icon icon-credit-card"></i> {$display_name|escape:'htmlall':'UTF-8'}</h4>
	<p>
		{$description|escape:'htmlall':'UTF-8'}
	</p>

	{if $ps_ship_when_available_configuration}
		<article class="module_confirmation conf confirm alert alert-success" role="alert" data-alert="success">
			{l s='"Delayed shipping" option is set up in Shop Parameters > Order Settings.'  mod='separatepackages'} 
        </article>
	{else}
		<article class="module_confirmation conf confirm alert alert-danger" role="alert" data-alert="danger">
			{l s='"Delayed shipping" option is not set up in Shop Parameters > Order Settings, please turn it on.'  mod='separatepackages'} 
        </article>
 
	{/if}
</div>

{* <div class="panel">
	<h4><i class="icon icon-tags"></i> {l s='Documentation' mod='separatepackages'}</h4>
	<p>
		&raquo; {l s='You can get a PDF documentation to configure this module' mod='separatepackages'} :
		<ul>
			<li><a href="#" target="_blank">{l s='English' mod='separatepackages'}</a></li>
			<li><a href="#" target="_blank">{l s='French' mod='separatepackages'}</a></li>
		</ul>
	</p>
</div> *}
