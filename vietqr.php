<?php
/**
Tính năng thanh toán bằng mã QR Code
Hỗ trợ tất cả các ngân hàng tại Việt Nam
Mã nguồn này thuộc sở hữu của Hao Quang Viet Software
MuaSSL.com Certificate Authority
Tác giả: Nguyễn Quốc Việt
Email: viet@haoquangviet.com
**/
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
function vietqr_MetaData()
{
    return array(
        'DisplayName' => 'HQV Viet QR code',
        'APIVersion' => '1.0', // Use API Version 1.1
        'DisableLocalCreditCardInput' => true,
        'TokenisedStorage' => false,
    );
}
function vietqr_config($params){
	$getbank = file_get_contents('https://api.vietqr.io/v2/banks');
	$getbank = json_decode($getbank,true);
	$banklist = array();
	foreach($getbank['data'] as $bank){
		$banklist[$bank['bin']] = $bank['short_name'].' - '.$bank['name'];
	}
	
    $configarray = array(
		"FriendlyName" => array(
			"Type" => "System",
			"Value" => "Thanh toán bằng QR Code"
        ),
		'accountID' => array(
            'FriendlyName' => 'Số tài khoản',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Nhập số tài khoản',
		),
		'accountName' => array(
            'FriendlyName' => 'Tên tài khoản',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Nhập Tên tài khoản',
		),
		'accountBank' => array(
            'FriendlyName' => 'Ngân hàng',
            'Type' => 'dropdown',
            'Options' => $banklist,
            'Default' => '',
            'Description' => 'Chọn',
		),
		'accountBankBranch' => array(
            'FriendlyName' => 'Chi nhánh Ngân hàng',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Nhập chi nhánh ngân hàng nếu có',
		),
		'qrType' => array(
            'FriendlyName' => 'Kiểu Mã QR',
            'Type' => 'dropdown',
            'Options' => ['compact2'=>'Mã QR, các logo , thông tin chuyển khoản','compact'=>'QR kèm logo VietQR, Napas, ngân hàng','qr_only'=>'QR đơn giản','print'=>'Mã QR, các logo và đầy đủ thông tin chuyển khoản'],
            'Default' => '',
            'Description' => 'Chi tiết xem ở <a href="https://vietqr.io/" target="_blank">ở đây</a>',
		),
		'payCode' => array(
            'FriendlyName' => 'Mã thanh toán',
            'Type' => 'text',
            'Size' => '25',
            'Default' => 'HOST',
            'Description' => 'Nhập mã để xác định thanh toán, ví dụ: HOST',
		),
		'paySecretKey' => array(
            'FriendlyName' => 'Mã bảo mật Callback',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Nhập mã bảo mật cho tính năng callback từ bên ngoài.',
		),
		'callbackURL' => array(
            'FriendlyName' => 'Địa chỉ Callback',
            'Type' => 'readonly',
            'Size' => '60',
            'Default' => $params['systemurl'].'modules/gateways/vietqr/vietqr.php',
            'Description' => $params['systemurl'].'modules/gateways/vietqr/vietqr.php',
		),
		'payAPIKey' => array(
            'FriendlyName' => 'Mã API',
            'Type' => 'textarea',
            'Rows' => '3',
            'Value' => '',
            'Description' => 'Mã này để kết nối kiểm tra thanh toán với',
		),
		"instructions" => array(
			"FriendlyName" => "Hướng dẫn",
			"Type" => "textarea",
			"Rows" => "5",
			"Value" => "",
			"Description" => "Nhập hướng dẫn để khách hàng thực hiện theo",
        ),
    );

    return $configarray;

}

function vietqr_link($params){
	if($params['currency']=='VND'){
    $code = '';
	$height='250';
	if($params['qrType']=='qr_only'){
		$height='200';
	}
	$mathanhtoan = $params['payCode'].' '.$params['invoiceid'];
	$code .= '<img alt="QR Code" height="'.$height.'" src="https://img.vietqr.io/image/'.$params['accountBank'].'-'.$params['accountID'].'-'.$params['qrType'].'.png?amount='.$params['amount'].'&addInfo='.($mathanhtoan).'&accountName='.$params['accountName'].'">';
	}
	$code .= '<p>'
        . nl2br($params['instructions'])
        . '<br />'
        . Lang::trans('invoicerefnum')
        . ': '
        . $mathanhtoan
        . '</p>';
	
    return $code;

}
