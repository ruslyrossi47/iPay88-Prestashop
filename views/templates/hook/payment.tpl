<div class="row">
	<div class="col-xs-12 col-md-6 col-lg-12">
        <p class="payment_module ipay88">
			<a title="Pay with iPay88" id="iPay88SubmitBtn" href="#">
				<span class="logo"><img alt="Pay with iPay88" src="{$logoIpay88}"></span><span class="text">Pay with iPay88</span>
				<span class="text_support">iPay88 supports the payment options as below:</span>
				<span class="bank_card"><img alt="Pay with iPay88" src="{$logoURL}"></span>
			</a>
		</p>
    </div>
</div>

<form action="{$purl}" method="post" name="ePayment" id="ipay88form">
	<input type="hidden" name="MerchantCode" value="{$mcode}" />
	<input type="hidden" name="PaymentId" value="" />
	<input type="hidden" name="RefNo" value="{$refNo}" />
	<input type="hidden" name="Amount" value="{$amount}" />
	<input type="hidden" name="Currency" value="{$currency}" />
	<input type="hidden" name="ProdDesc" value="{$proddesc}" />
	<input type="hidden" name="UserName" value="{$customer}" />
	<input type="hidden" name="UserEmail" value="{$email}" />
	<input type="hidden" name="UserContact" value="{$tel}" />
	<input type="hidden" name="Remark" value="" />
	<input type="hidden" name="Lang" value="UTF-8"/>
	<input type="hidden" name="Signature" value="{$signature}" />
	<input type="hidden" name="ResponseURL" value="{$responseURL}" />
	<!-- <input type="hidden" name="BackendURL" value="{$backendPostURL}"/> -->
</form>
<script type="text/javascript">
	{literal}
		$('#iPay88SubmitBtn').on('click',function(e){
			e.preventDefault();
			$('#ipay88form').submit();
			return false;
		});
	{/literal}
</script>	

<style type="text/css">
p.payment_module.ipay88 a {
	padding-left: 17px;
}
p.payment_module.ipay88 a span.logo {
	display: inline-block;
	margin-left: 13px;
	margin-right: 10px;
	text-align: left;
}
p.payment_module.ipay88 a span.logo img {
	width: auto;
	max-width: 95%;
}
p.payment_module.ipay88 a span.text {
	color: #333;
}
p.payment_module.ipay88 a span.text_support {
	display: block;
	font-size: 13px;
	font-style: italic;
	margin-top: 15px;
	padding-left: 14px;
}
p.payment_module.ipay88 a span.bank_card img {
	width: auto;
	max-width: 95%;
	display: block;
}
</style>