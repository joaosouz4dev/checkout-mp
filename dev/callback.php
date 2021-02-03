<?php
session_start();

require_once('config.php');
require_once('funcoes.php');
require_once('lib/mercadopago.php');

$var1434e5e = DEBUG;
if ($var1434e5e == true) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

$post     = method_post();
$vardba308f = '';

$vare2d602f = NOTIFICA_URL;

$codigo = isset($post['codigo']) ? $post['codigo'] : '';

$query = mysqli_query($con, "SELECT * 
	FROM produtos
	WHERE 
		produtoCodigo = '$codigo'
	LIMIT 1;");

$total = mysqli_num_rows($query);
if ($total == 0) {
	echo json_encode(array(
	    'msg'  => 'Código de Produto Inválido',
	    'tipo' => 'error'
	));

	exit;
}

$lista = mysqli_fetch_array($query);

$var8d05030 = $lista['produtoID'];
$varaa4730c = $lista['produtoNome'];
$varbb1b202 = $lista['produtoFrete'];
$varbb1b202 = number_format($varbb1b202, 2);

$var8c7a816 = $lista['produtoValor'];
$var8c7a816 = number_format($var8c7a816, 2);

$produto_dono = $lista["usuarioID"];

$userQuery = mysqli_query($con, "SELECT * FROM admins WHERE adminID = $produto_dono limit 1");
$user = mysqli_fetch_array($userQuery);

$userMPKEY = $user["MP_KEY"];
$userToken = $user["MP_TOKEN"];
$userSandbox = $user["useSandbox"];

$var0f6352c = isset($post['total_parcelas'])   ? $post['total_parcelas'] : '0.00';

$varc5105d2 = number_format($var8c7a816 + $varbb1b202, 2);

$var2700a3f = isset($post['tipo']) ? $post['tipo'] : '';

$var954d4fb  = isset($_SESSION['cliente_email'])   ? anti_injection($_SESSION['cliente_email']) : '';
$clienteNome = isset($_SESSION['cliente_nome'])    ? anti_injection($_SESSION['cliente_nome']) : '';
$var6433367  = isset($_SESSION['celular'])         ? preg_replace('/[^0-9]/', '', $_SESSION['celular']) : '';
$vare71fb4a  = isset($_SESSION['cliente_cpf'])     ? preg_replace('/[^0-9]/', '', $_SESSION['cliente_cpf']) : '';
$vareb22fe8  = isset($_SESSION['endereco_cep'])    ? preg_replace('/[^0-9]/', '', $_SESSION['endereco_cep']) : '';
$var46b32b6  = strlen($vare71fb4a);
$rua         = isset($_SESSION['endereco_rua'])    ? anti_injection($_SESSION['endereco_rua']) : '';
$numero      = isset($_SESSION['endereco_n'])      ? anti_injection($_SESSION['endereco_n']) : '';
$bairro      = isset($_SESSION['endereco_bairro']) ? anti_injection($_SESSION['endereco_bairro']) : '';
$cidade      = isset($_SESSION['endereco_cidade']) ? anti_injection($_SESSION['endereco_cidade']) : '';
$estado      = isset($_SESSION['endereco_uf'])     ? anti_injection($_SESSION['endereco_uf']) : '';
$complemento = isset($_SESSION['complemento'])     ? anti_injection($_SESSION['complemento']) : '';

if (empty($clienteNome)) {
    $vardba308f = 'Preencha corretamente o campo Nome';
} else if (!is_valid_email($var954d4fb)) {
    $vardba308f = 'Preencha corretamente o campo Email';   
} else if (empty($var6433367)) {
    $vardba308f = 'Preencha corretamente o campo Telefone';     
} else if ($var46b32b6 < 11 || !validaCPF($vare71fb4a)) {
    $vardba308f = 'Preencha corretamente o campo CPF';    
} else if (strlen($vareb22fe8) <> 8) {
    $vardba308f = 'Preencha corretamente o campo CEP';    
} else if (empty($rua)) {
    $vardba308f = 'Preencha corretamente o campo Endereço';
} else if (empty($numero )) {
    $vardba308f = 'Preencha corretamente o campo Número';    
} else if (empty($bairro)) {
    $vardba308f = 'Preencha corretamente o campo Bairro';     
} else if (empty($cidade)) {
    $vardba308f = 'Preencha corretamente o campo cidade';     
} else if (empty($estado)) {
    $vardba308f = 'Selecione corretamente o Estado';    
} else if (empty($var2700a3f)) {
    $vardba308f = 'Selecione uma forma de pagamento';    
} else {

	$var9af2705 = 1;
	$varf8f690f = '';
	$varacd24a9 = '';
	$vard7003d4 = strtoupper(substr(md5(uniqid(rand(), true)), 0, 15));

	$var0865eb2 = 'Cartão de Crédito';
	if ($var2700a3f == 'boleto')
		$var0865eb2 = 'Boleto bancário';
		
	if ($var2700a3f == 'cartao')
		$varc5105d2 = $var0f6352c;

	$query = mysqli_query($con, "INSERT INTO pedidos
		(pedidoCodigo,
		pedidoNome,
		pedidoEmail,
		pedidoTelefone,
		pedidoCPF,
		pedidoCEP,
		pedidoEnderecoRua,
		pedidoEnderecoNumero,
		pedidoEnderecoBairro,
		pedidoEnderecoCidade,
		pedidoEnderecoEstado,
		pedidoPagamento,
		pedidoStatus,
		pedidoData,
		pedidoFrete,
		pedidoValor,
		pedidoEnderecoComplemento,
		pedidoProduto,
		status_key,
		usuarioID) 
			VALUES
		('". $vard7003d4 ."',
		'". $clienteNome ."',
		'". $var954d4fb ."',
		'". $var6433367 ."',
		'". $vare71fb4a ."',
		'". $vareb22fe8 ."',
		'". $rua ."',
		'". $numero ."',
		'". $bairro ."',
		'". $cidade ."',
		'". $estado ."',
		'". $var0865eb2 ."',
		'1',
		'". date('Y-m-d H:i:s') ."',
		'". $varbb1b202 ."',
		'". $var8c7a816 ."',
		'". $complemento ."',
		'". $varaa4730c ."',
		'". $userToken ."',
		$produto_dono)") or die(mysqli_error($con));

	if ($query) {
		$pedidoID = mysqli_insert_id($con);

		$var2e9eca5 = explode(' ', $clienteNome);
		$varc260d22 = isset($var2e9eca5[0]) ? $var2e9eca5[0] : '';
		array_shift($var2e9eca5);
		$var5b66f5c = implode(' ', $var2e9eca5);
			
		if ($var2700a3f == 'boleto') {

			$varb325cc1 = new MP($userToken);
			$varb325cc1->sandbox_mode($userSandbox);
			
			$var14fa49b = array(
				"date_of_expiration" => date("Y-m-d\TH:i:s.000\Z", strtotime('+5 days')),
				"transaction_amount" => round((float) $varc5105d2, 2),
				"external_reference" => $vard7003d4,
				"description"        => "Boleto - " . $varaa4730c . " - " . $varc260d22 ,
				"notification_url"   => $vare2d602f . "?ucod={$produto_dono}",
				"payment_method_id"  => "bolbradesco",
				"payer" => array(
					"email"          => $var954d4fb,
					"first_name"     => $varc260d22,
					"last_name"      => $var5b66f5c,
					"identification" => array(
						"type"   => "CPF",
						"number" => $vare71fb4a
					),
					"address"=>  array(
						"zip_code"      => $vareb22fe8,
						"street_name"   => $rua,
						"street_number" => $numero,
						"neighborhood"  => $bairro,
						"city"          => $cidade,
						"federal_unit"  => $estado
					)
				),
				"additional_info"=>  array(
					"items" => array(
						array(
							"id"          => $pedidoID,
							"title"       => $varaa4730c,
							"description" => "",
							"picture_url" => "",
							"category_id" => "others", // LISTAGEM DISPONÍVEL EM: https://api.mercadopago.com/item_categories
							"quantity"    => $var9af2705,
							"unit_price"  => str_replace(',', '.', $var8c7a816)
						)
					)
				)
			);

			$var9031fef = $varb325cc1->post("/v1/payments/", $var14fa49b);

			$barcode    = $var9031fef['response']['barcode']['content'];
			$varf8f690f = $var9031fef['response']['transaction_details']['external_resource_url'];
			$pedidoIDMP    = $var9031fef['response']['id'];

			$query = mysqli_query($con, "UPDATE pedidos SET
				pedidoLinkBoleto   = '". $varf8f690f ."',
				pedidoBarcode      = '". $barcode ."',
				pedidoIDMP   = '". $pedidoIDMP ."'
			WHERE
				pedidoID = $pedidoID
			LIMIT 1;") or die(mysqli_error($con));

			$vara60bda2  = SITE_URL .'?p='. $codigo .'&id='. $vard7003d4;
			
		} else {

			$varb325cc1 = new MP($userToken);
			$varb325cc1->sandbox_mode($userSandbox);
			
			$var1dddb74 = (int) $_REQUEST['paymentMethodId'];
			$varc5f386c = rand(1000, 9999);
			
			$var897e67c = (int) $_REQUEST['installmentsOption'];

			$var14fa49b = array(
				"token"                => $_REQUEST['token'],
				"installments"         => $var897e67c,
				"transaction_amount"   => round((float) $var0f6352c, 2),
				"external_reference"   => $vard7003d4,
				"binary_mode"          => false,
				"description"          => "Cartão - " . $varaa4730c . " - " . $varc260d22 ,
				"payment_method_id"    => $_REQUEST['paymentMethodId'],
				"statement_descriptor" => "",
				"notification_url"     => $vare2d602f . "?ucod={$produto_dono}",
				"payer" => array(
					"email"          => $var954d4fb,
					"first_name"     => $varc260d22,
					"last_name"      => $var5b66f5c,
					"identification" => array(
						"type"   => "CPF",
						"number" => $vare71fb4a
					),
					"address"=>  array(
						"zip_code"      => $vareb22fe8,
						"street_name"   => $rua,
						"street_number" => $numero,
						"neighborhood"  => $bairro,
						"city"          => $cidade,
						"federal_unit"  => $estado
					)
				),
				"additional_info"=>  array(
					"ip_address" => $_SERVER['REMOTE_ADDR'],
					"items" => array(
						array(
							"id"          => $pedidoID,
							"title"       => $varaa4730c,
							"description" => "",
							"picture_url" => "",
							"category_id" => "others", // LISTAGEM DISPONÍVEL EM: https://api.mercadopago.com/item_categories
							"quantity"    => $var9af2705,
							"unit_price"  => $var8c7a816
						)
					)
				)
			);
			
			$var9031fef = $varb325cc1->post("/v1/payments/", $var14fa49b);
			$var077f471           = $var9031fef['response']['status'];
			$pedidoIDMP2    = $var9031fef['response']['id'];

			if ($pedidoIDMP2) {
				getStatus($pedidoIDMP2 , $userMPKEY);
			}

			$query = mysqli_query($con, "UPDATE pedidos SET
				pedidoIDMP   = '". $pedidoIDMP2 ."'
			WHERE
				pedidoID = $pedidoID
			LIMIT 1;") or die(mysqli_error($con));
						
			if ($var077f471 == 'in_process')
				$vardba308f = 'Processando';

			if ($var077f471 == 'approved') {
				$var9583432 = true;
			}
			if ($var077f471 == 'cancelled') {
				$query = mysqli_query($con, "UPDATE pedidos SET 
					pedidoStatus   = '3',
				WHERE
					pedidoID = $pedidoID
				LIMIT 1;") or die(mysqli_error($con));
				if($query){
					return true;
				}
			}

			if (($var077f471 == 'in_process') || ($var077f471 == 'approved')) {
				$vara60bda2  = SITE_URL .'?p='. $codigo .'&id='. $vard7003d4;
			} else {
				echo json_encode(array(
					'msg'  => 'Transação não autorizada no Cartão de Crédito',
					'tipo' => 'error'
				));

				exit;
			}
		}
		
		echo json_encode(array(
			'redirect' => $vara60bda2
		));

		exit;
	} else {
		$vardba308f = 'Erro ao cadastrar Pedido. Entre em contato com o Suporte.';
	}
}

echo json_encode(array(
    'msg'  => $vardba308f,
    'tipo' => 'error'
));