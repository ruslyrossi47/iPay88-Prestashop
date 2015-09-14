<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @copyright  2007-2013 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*
* Author : ruslyrossi47@gmail.com
*/

if (!defined('_PS_VERSION_'))
	exit;

class iPay88 extends PaymentModule
{

	public function __construct()
	{
	$this->name = 'ipay88';
	$this->tab = 'payments_gateways';
	$this->version = '1.0';
	$this->author = 'Rusly';
	$this->need_instance = 0;
	$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');

	$this->currencies = true;
	$this->currencies_mode = 'checkbox';

	parent::__construct();

	$this->displayName = $this->l('iPay88');
	$this->description = $this->l('iPay88 configuration module');

	$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

	if (!Configuration::get('iPay88'))      
	  $this->warning = $this->l('No name provided');
	}

	public function install()
	{
	  return parent::install() &&
	  	Configuration::updateValue('iPay88', 'iPay88 MODULE') &&
	  	$this->registerHook('payment') &&
	  	Configuration::updateValue('PS_OS_IPAY88', $this->_create_order_state('iPay88 Payment', null, 'orange') );
	}	

	public function uninstall()
	{
	  return parent::uninstall() && 
	  	Configuration::deleteByName('iPay88');
	}

	public function getContent()
	{
	    $output = null;
	 
	    if (Tools::isSubmit('submit'.$this->name))
	    {
            Configuration::updateValue('MCODE', Tools::getValue('MCODE'));
            Configuration::updateValue('MKEY', Tools::getValue('MKEY'));
            Configuration::updateValue('PURL', Tools::getValue('PURL'));
            $output .= $this->displayConfirmation($this->l('Settings updated'));
	    }
	    return $output.$this->displayForm();
	}

	public function displayForm()
	{
	    // Get default Language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('Settings'),
	        ),
	        'input' => array(
	            array(
	                'type' => 'text',
	                'label' => $this->l('Merchant Code'),
	                'name' => 'MCODE',
	                'size' => 20,
	                'required' => true
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Merchant Key'),
	                'name' => 'MKEY',
	                'size' => 20,
	                'required' => true
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Post URL'),
	                'name' => 'PURL',
	                'size' => 20,
	                'required' => true
	            )
	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'button'
	        )
	    );
	     
	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar
	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;        // false -> remove toolbar
	    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
	    $helper->submit_action = 'submit'.$this->name;
	    $helper->toolbar_btn = array(
	        'save' =>
	        array(
	            'desc' => $this->l('Save'),
	            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	            '&token='.Tools::getAdminTokenLite('AdminModules'),
	        ),
	        'back' => array(
	            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	            'desc' => $this->l('Back to list')
	        )
	    );
	     
	    // Load current value
	    $helper->fields_value['MCODE'] 	= Configuration::get('MCODE');
	    $helper->fields_value['MKEY'] 	= Configuration::get('MKEY');
	    $helper->fields_value['PURL'] 	= Configuration::get('PURL');
	     
	    return $helper->generateForm($fields_form);
	}

	public function hookPayment($params)
	{

		$this->smarty->assign(array(
			'purl' 			=> Configuration::get('PURL'),
			'mcode' 		=> Configuration::get('MCODE'),
			'refNo' 		=> $this->context->cart->id,
			'amount'		=> $this->context->cart->getOrderTotal(true,Cart::BOTH),
			'currency'		=> $this->context->currency->iso_code,
			'proddesc'      => $this->getProductDesc($params),
			'customer'		=> $this->context->cookie->customer_firstname,
			'email'			=> $this->context->cookie->email,
			'tel'			=> $this->getPhoneNumber($this->context->customer->id),
			'signature'		=> $this->iPay88_signature(Configuration::get('MKEY') . Configuration::get('MCODE') . $this->context->cart->id . number_format(str_replace(".", "", $this->context->cart->getOrderTotal(true,Cart::BOTH)), 2, '', '') . $this->context->currency->iso_code ),
			'logoURL' 		=> Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/images/logo.jpg', 
			'logoIpay88' 	=> Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/images/ipay88.gif',
			'responseURL' 	=> $this->context->link->getModuleLink('ipay88', 'receive'),
			'backendPostURL'=> $this->context->link->getModuleLink('ipay88', 'backendposturl'),
			'this_path' 	=> $this->_path,
			'this_path_bw' 	=> $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));

		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return;

		$state = $params['objOrder']->getCurrentState();
		if ($state == Configuration::get('PS_OS_IPAY88') || $state == Configuration::get('PS_OS_OUTOFSTOCK'))
		{
			$this->context->smarty->assign(array(
				'orderHistory' => $this->context->link->getPageLink('history'),
			));
		}
		else
			$this->smarty->assign('error', 'Sorry, we have failed to process your order. Please try again.');
		return $this->display(__FILE__, 'payment_return.tpl');
	}	
	
	public function getPhoneNumber($id_customer)
	{
		$sql = '
			SELECT a.phone
			FROM '._DB_PREFIX_.'address AS a
			WHERE id_customer = '. $id_customer .'
			AND a.phone <> ""
			GROUP BY a.id_customer
			ORDER BY a.id_address
		';

		$results = Db::getInstance()->executeS($sql);

		$tel = 0;
		foreach ($results as $result) :
			$tel = $result['phone'];
		endforeach;

		return $tel;
	}

	public function getProductDesc($params) 
	{ 
		$products = $params['cart']->getProducts(true);

		return $products[0]['name'];
	}

	public function checkCurrency($cart)
	{
		$currency_order = new Currency($cart->id_currency);
		$currencies_module = $this->getCurrency($cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}


    private function _create_order_state($label, $template = null, $color = 'DarkOrange')
    {
        //Create the new status
        $os = new OrderState();
        $os->name = array(
            '1' => $label,
            '2' => '',
            '3' => ''
        );

        $os->invoice = true;
        $os->unremovable = true;
        $os->color = $color;
        $os->template = $template;
        $os->send_email = false;

        $os->save();
        
        return $os->id;
    }

	function iPay88_signature($source)
	{
	  return base64_encode($this->hex2bin(sha1($source)));
	}

	function hex2bin($hexSource)
	{
		$bin = '';
	    for ($i=0;$i<strlen($hexSource);$i=$i+2)
	    {
	      $bin .= chr(hexdec(substr($hexSource,$i,2)));
	    }
	  return $bin;
	}
}