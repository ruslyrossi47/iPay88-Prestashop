{if isset($status) && $status} <br class="clear" />
	<p class="alert alert-error">Sorry, there is an error processing your order. <strong><u>Error: {$status}</u></strong></p>
	<p>Please report this to our <a href="{$link->getPagelink('contact')}">customer support</a></p>
{elseif isset($unsuccessful) && $unsuccessful}<br class="clear" />
	<p class="alert alert-error">{$unsuccessful}</p>
{elseif isset($mismatched) && $mismatched}<br class="clear" />
	<p class="alert alert-error">{$mismatched}</p>
{/if}