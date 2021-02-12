<?php
// MOD TMP
class Ba_importerAjaxModuleFrontController extends ModuleFrontController {
		public function initContent() {
			$this->ajax = true;

			parent::initContent();
		}

		public function displayAjax() {
			// echo Tools::jsonEncode( "You do not have permission to access it." ); die();
			// $remote_ip = Tools::getRemoteAddr();
			//
			// if ( !( int )Configuration::get( 'PS_SHOP_ENABLE' ) ) {
			// 	if ( !in_array($remote_ip, explode( ',', Configuration::get( 'PS_MAINTENANCE_IP' ) ) ) ) {
			// 		if ( !Configuration::get( 'PS_MAINTENANCE_IP' ) ) {
			// 			Configuration::updateValue( 'PS_MAINTENANCE_IP', $remote_ip );
			// 		} else {
			// 			Configuration::updateValue('PS_MAINTENANCE_IP', Configuration::get( 'PS_MAINTENANCE_IP' ) . ',' . $remote_ip );
			// 		}
			// 	}
			// }
			//
			// error_reporting( E_ALL );
			//
			// if (Tools::getValue('baimporter_token') != sha1(_COOKIE_KEY_ . 'baimporter'))
			// 	$this->ajaxDie( Tools::jsonEncode( $this->module->l( "You do not have permission to access it." ) ) );
			//
			// set_time_limit( 0 );
			// require_once( '../../classes/ajaximport.php' );
			//
			// $cookiekey = $this->module->cookiekeymodule();
			// $batoken = Tools::getValue( "batoken" );
			//
			// $cookie = new Cookie( 'psAdmin' );
			// $id_employee = $cookie->id_employee;
			//
			// if (
			// 	   $batoken == $cookiekey
			// 	&& !empty( $id_employee )
			// ) {
			// 	$a = new AjaxImport();
			// 	$a->submitAddDb();
			// } else
			// 	$this->ajaxDie( Tools::jsonEncode( $this->module->l( "You do not have permission to access it." ) ) );
		}
}
// MOD TMP END
