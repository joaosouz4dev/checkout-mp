<?php
session_start();

require_once('config.php');
require_once('funcoes.php');
require_once('lib/mercadopago.php');

$debug = DEBUG;
if ($debug == true) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

$post     = method_post();
$mensagem = '';

$notifica = NOTIFICA_URL;

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

$produtoID = $lista['produtoID'];
$produtoNome = $lista['produtoNome'];
$produtoFrete = $lista['produtoFrete'];
$produtoFrete = number_format($produtoFrete, 2);

$produtoValor = $lista['produtoValor'];
$produtoValor = number_format($produtoValor, 2);

$produto_dono = $lista["usuarioID"];

$userQuery = mysqli_query($con, "SELECT * FROM admins WHERE adminID = $produto_dono limit 1");
$user = mysqli_fetch_array($userQuery);

$userMPKEY = $user["MP_KEY"];
$userToken = $user["MP_TOKEN"];
$userSandbox = $user["useSandbox"];

$valorParcela = isset($post['total_parcelas'])   ? $post['total_parcelas'] : '0.00';

$valorTotal = number_format($produtoValor + $produtoFrete, 2);

$tipo = isset($post['tipo']) ? $post['tipo'] : '';

$email  = isset($_SESSION['cliente_email'])   ? anti_injection($_SESSION['cliente_email']) : '';
$clienteNome = isset($_SESSION['cliente_nome'])    ? anti_injection($_SESSION['cliente_nome']) : '';
$celular  = isset($_SESSION['celular'])         ? preg_replace('/[^0-9]/', '', $_SESSION['celular']) : '';
$cpf  = isset($_SESSION['cliente_cpf'])     ? preg_replace('/[^0-9]/', '', $_SESSION['cliente_cpf']) : '';
$cep  = isset($_SESSION['endereco_cep'])    ? preg_replace('/[^0-9]/', '', $_SESSION['endereco_cep']) : '';
$cpfTam  = strlen($cpf);
$rua         = isset($_SESSION['endereco_rua'])    ? anti_injection($_SESSION['endereco_rua']) : '';
$numero      = isset($_SESSION['endereco_n'])      ? anti_injection($_SESSION['endereco_n']) : '';
$bairro      = isset($_SESSION['endereco_bairro']) ? anti_injection($_SESSION['endereco_bairro']) : '';
$cidade      = isset($_SESSION['endereco_cidade']) ? anti_injection($_SESSION['endereco_cidade']) : '';
$estado      = isset($_SESSION['endereco_uf'])     ? anti_injection($_SESSION['endereco_uf']) : '';
$complemento = isset($_SESSION['complemento'])     ? anti_injection($_SESSION['complemento']) : '';

if (empty($clienteNome)) {
    $mensagem = 'Preencha corretamente o campo Nome';
} else if (!is_valid_email($email)) {
    $mensagem = 'Preencha corretamente o campo Email';   
} else if (empty($celular)) {
    $mensagem = 'Preencha corretamente o campo Telefone';     
} else if ($cpfTam < 11 || !validaCPF($cpf)) {
    $mensagem = 'Preencha corretamente o campo CPF';    
} else if (strlen($cep) <> 8) {
    $mensagem = 'Preencha corretamente o campo CEP';    
} else if (empty($rua)) {
    $mensagem = 'Preencha corretamente o campo Endereço';
} else if (empty($numero )) {
    $mensagem = 'Preencha corretamente o campo Número';    
} else if (empty($bairro)) {
    $mensagem = 'Preencha corretamente o campo Bairro';     
} else if (empty($cidade)) {
    $mensagem = 'Preencha corretamente o campo cidade';     
} else if (empty($estado)) {
    $mensagem = 'Selecione corretamente o Estado';    
} else if (empty($tipo)) {
    $mensagem = 'Selecione uma forma de pagamento';    
} else {
	$quantidade = 1;
	$boletoLink = '';
	$pedidoCodigo = strtoupper(substr(md5(uniqid(rand(), true)), 0, 15));

	$pagamentoNome = 'Cartão de Crédito';
	if ($tipo == 'boleto')
		$pagamentoNome = 'Boleto bancário';
		
	if ($tipo == 'cartao')
		$valorTotal = $valorParcela;

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
		('". $pedidoCodigo ."',
		'". $clienteNome ."',
		'". $email ."',
		'". $celular ."',
		'". $cpf ."',
		'". $cep ."',
		'". $rua ."',
		'". $numero ."',
		'". $bairro ."',
		'". $cidade ."',
		'". $estado ."',
		'". $pagamentoNome ."',
		'1',
		'". date('Y-m-d H:i:s') ."',
		'". $produtoFrete ."',
		'". $produtoValor ."',
		'". $complemento ."',
		'". $produtoNome ."',
		'". $userToken ."',
		$produto_dono)") or die(mysqli_error($con));

	if ($query) {
		$pedidoID = mysqli_insert_id($con);

		$arrNome = explode(' ', $clienteNome);
		$primeiroNome = isset($arrNome[0]) ? $arrNome[0] : '';
		array_shift($arrNome);
		$nomeRest = implode(' ', $arrNome);
			
		if ($tipo == 'boleto') {

			$mp = new MP($userToken);
			$mp->sandbox_mode($userSandbox);
			
			$dados = array(
				"date_of_expiration" => date("Y-m-d\TH:i:s.000\Z", strtotime('+5 days')),
				"transaction_amount" => round((float) $valorTotal, 2),
				"external_reference" => $pedidoCodigo,
				"description"        => $produtoNome . " - " . $primeiroNome ,
				"notification_url"   => $notifica . "?ucod={$produto_dono}",
				"payment_method_id"  => "bolbradesco",
				"payer" => array(
					"email"          => $email,
					"first_name"     => $primeiroNome,
					"last_name"      => $nomeRest,
					"identification" => array(
						"type"   => "CPF",
						"number" => $cpf
					),
					"address"=>  array(
						"zip_code"      => $cep,
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
							"title"       => $produtoNome,
							"description" => "",
							"picture_url" => "",
							"category_id" => "others", // LISTAGEM DISPONÍVEL EM: https://api.mercadopago.com/item_categories
							"quantity"    => $quantidade,
							"unit_price"  => str_replace(',', '.', $produtoValor)
						)
					)
				)
			);

			$response = $mp->post("/v1/payments/", $dados);

			$barcode    = $response['response']['barcode']['content'];
			$boletoLink = $response['response']['transaction_details']['external_resource_url'];
			$pedidoIDMP = $response['response']['id'];

			$query = mysqli_query($con, "UPDATE pedidos SET
				pedidoLinkBoleto   = '". $boletoLink ."',
				pedidoBarcode      = '". $barcode ."',
				pedidoIDMP   = '". $pedidoIDMP ."'
			WHERE
				pedidoID = $pedidoID
			LIMIT 1;") or die(mysqli_error($con));

			$redirect  = SITE_URL .'?p='. $codigo .'&id='. $pedidoCodigo;
			
		} else {

			$mp = new MP($userToken);
			$mp->sandbox_mode($userSandbox);
			
			$installments = (int) $_REQUEST['installmentsOption'];

			$dados = array(
				"token"                => $_REQUEST['token'],
				"installments"         => $installments,
				"transaction_amount"   => round((float) $valorParcela, 2),
				"external_reference"   => $pedidoCodigo,
				"binary_mode"          => false,
				"description"          => $produtoNome . " - " . $primeiroNome ,
				"payment_method_id"    => $_REQUEST['paymentMethodId'],
				"statement_descriptor" => "",
				"notification_url"     => $notifica . "?ucod={$produto_dono}",
				"payer" => array(
					"email"          => $email,
					"first_name"     => $primeiroNome,
					"last_name"      => $nomeRest,
					"identification" => array(
						"type"   => "CPF",
						"number" => $cpf
					),
					"address"=>  array(
						"zip_code"      => $cep,
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
							"title"       => $produtoNome,
							"description" => "",
							"picture_url" => "",
							"category_id" => "others", // LISTAGEM DISPONÍVEL EM: https://api.mercadopago.com/item_categories
							"quantity"    => $quantidade,
							"unit_price"  => $produtoValor
						)
					)
				)
			);
			
			$response    = $mp->post("/v1/payments/", $dados);
			$status      = $response['response']['status'];
			$pedidoIDMP2 = $response['response']['id'];

			if ($pedidoIDMP2) {
				getStatus($pedidoIDMP2 , $userToken);
			}

			$query = mysqli_query($con, "UPDATE pedidos SET
				pedidoIDMP   = '". $pedidoIDMP2 ."'
			WHERE
				pedidoID = $pedidoID
			LIMIT 1;") or die(mysqli_error($con));
						
			if ($status == 'in_process')
				$mensagem = 'Processando';
			if ($status == 'cancelled') {
				$query = mysqli_query($con, "UPDATE pedidos SET 
					pedidoStatus   = '3',
				WHERE
					pedidoID = $pedidoID
				LIMIT 1;") or die(mysqli_error($con));
				if($query){
					return true;
				}
			}
			if (($status == 'in_process') || ($status == 'approved')) {
				$redirect  = SITE_URL .'?p='. $codigo .'&id='. $pedidoCodigo;
			} else {
				echo json_encode(array(
					'msg'  => 'Transação não autorizada no Cartão de Crédito',
					'tipo' => 'error'
				));

				exit;
			}
		}
		
		echo json_encode(array(
			'redirect' => $redirect
		));

		exit;
	} else {
		$mensagem = 'Erro ao cadastrar Pedido. Entre em contato com o Suporte.';
	}
}

echo json_encode(array(
    'msg'  => $mensagem,
    'tipo' => 'error'
));