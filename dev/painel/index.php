<?php

session_start();

include('../config.php');
include('../funcoes.php');


$logado = isset($_SESSION['logado']) ? (bool) $_SESSION['logado'] : false;

if (isset($_GET['sair'])) {
    $_SESSION['logado'] = false;

    header("Location: ". SITE_URL ."painel");
    exit;
}

if(isset($_POST['registrarLogin'])){

    $registrarLogin = isset($_POST['registrarLogin']) ? $_POST['registrarLogin'] : '';
    $registrarSenha = isset($_POST['registrarSenha']) ? $_POST['registrarSenha'] : '';
    $registrarPhone = isset($_POST['registrarPhone']) ? $_POST['registrarPhone'] : '';

    $query = mysqli_query($con, "INSERT INTO admins
        (adminLogin,
		adminSenha,
		adminStatus,
		adminRole,
		adminPhone)
		VALUES
		('{$registrarLogin}',
		'{$registrarSenha}',
		0,
		'vendor',
		'{$registrarPhone}')") or die(mysqli_error($con));

	if ($query) {
		$mensagem = 'Você foi registrado com sucesso! Em breve ativaremos sua conta.';
	} else {
		$mensagem = 'Erro ao efetuar o registro';
	}
}

if (isset($_POST['login'])) {
	$post = method_post();
	$login = isset($post['login']) ? $post['login'] : '';
	$senha = isset($post['senha']) ? $post['senha'] : '';

	if (empty($login)) {
		$mensagem = 'Preencha corretamente o campo Login';
	} else if (!is_valid_email($login)) {
		$mensagem = 'Preencha corretamente o campo Login';
	} else if (empty($senha)) {
		$mensagem = 'Preencha corretamente o campo Senha';
	} else {
		$query = mysqli_query($con, "SELECT *
			FROM admins
			WHERE
				adminLogin  = '$login' AND
				adminSenha  = '$senha'
			LIMIT 1");

		$lista = mysqli_fetch_array($query);
		if (isset($lista['adminID']) && $lista["adminStatus"] == 1) {
			$_SESSION['logado'] = true;
			// setar o id do usuario na sessão para filtrar os pedidos e produtos
			$_SESSION['adminID'] = $lista["adminID"];
			// setar a role do usuario - admin ou vendedor
			$_SESSION['adminRole'] = $lista["adminRole"];

			header("Location: ". SITE_URL ."painel");
			exit;
		} else if (isset($lista['adminID']) && $lista["adminStatus"] == 0){
			$mensagem = "Conta desativada";
		} else {
			$mensagem = 'Login ou Senha Inválida';
		}
	}
}

if (isset($_GET['status'])) {
	require_once('../lib/mercadopago.php');
	$query2 = mysqli_query($con, "SELECT * FROM pedidos WHERE pedidoStatus = 1 AND status_key IS NOT NULL AND usuarioID = {$_SESSION["adminID"]} ORDER BY pedidoID DESC");
	while($row = mysqli_fetch_array($query2)){
		$value = $row['pedidoIDMP'];
		if ($value != null) {
			$mp = new MP ($row['status_key']);
			$payment_info = $mp->get("/v1/payments/". $value, false);
			$statusSwitch = $payment_info["response"]["status"];
			switch ($statusSwitch) {
					case "approved":
						$status = "2";
						break;
					case "pending":
						$status = "1";
						break;
					case "in_process":
						$status = "1";
						break;
					case "rejected":
						$status = "3";
						break;
					case "refunded":
						$status = "3";
						break;
					case "cancelled":
						$status = "3";
						break;
					case "in_mediation":
						$status = "1";
						break;
					default:
						$status = "1";
			};
			$query = mysqli_query($con, "UPDATE pedidos SET pedidoStatus='$status' WHERE pedidoIDMP='$value' LIMIT 1;") or die(mysqli_error($con));
		}
	};
	header("Location: ". SITE_URL ."painel/?pg=pedidos");
}

if (isset($_GET['csv']))  {

	$filtroProduto =  $_GET['produto_filter']; 
	$filtroStatus =  $_GET['status_filter'];
	$filtroData =  $_GET['data_filter'];
	$filtroData2 =  $_GET['data_filter2'];   
	$sql_csv = "SELECT * FROM pedidos 
		WHERE
			usuarioID = {$_SESSION["adminID"]} AND
			pedidoProduto = '$filtroProduto' AND
			pedidoStatus = $filtroStatus AND
			pedidoData BETWEEN '$filtroData' AND '$filtroData2'
		ORDER BY 
			pedidoID DESC";
	if  ($filtroProduto == 'ALL') {
		$sql_csv = "SELECT * FROM pedidos 
		WHERE
			usuarioID = {$_SESSION["adminID"]} AND
			pedidoStatus = $filtroStatus AND
			pedidoData BETWEEN '$filtroData' AND '$filtroData2'
		ORDER BY 
			pedidoID DESC LIMIT $start_from, $limit";
	}
	if  ($filtroStatus == 'ALL') {
		$sql_csv = "SELECT * FROM pedidos 
		WHERE
			usuarioID = {$_SESSION["adminID"]} AND
			pedidoProduto = '$filtroProduto' AND
			pedidoData BETWEEN '$filtroData' AND '$filtroData2'
		ORDER BY 
			pedidoID DESC LIMIT $start_from, $limit";
	}
	if  ($filtroStatus == 'ALL' and $filtroProduto == 'ALL') {
		$sql_csv = "SELECT * FROM pedidos 
		WHERE
			usuarioID = {$_SESSION["adminID"]} AND
			pedidoData BETWEEN '$filtroData' AND '$filtroData2'
		ORDER BY 
			pedidoID DESC LIMIT $start_from, $limit";
	}
	if  ($filtroData == $filtroData2) {
		$sql_csv = "SELECT * FROM pedidos 
		WHERE
			usuarioID = {$_SESSION["adminID"]} AND
			pedidoProduto = '$filtroProduto' AND
			pedidoStatus = $filtroStatus AND
			pedidoData >= '$filtroData'
		ORDER BY 
			pedidoID DESC";
	}
	if  ($filtroStatus == 'ALL' and $filtroData == $filtroData2) {
		$sql_csv = "SELECT * FROM pedidos 
		WHERE
			usuarioID = {$_SESSION["adminID"]} AND
			pedidoProduto = '$filtroProduto' AND
			pedidoData >= '$filtroData'
		ORDER BY 
			pedidoID DESC";
	}
	if  ($filtroProduto == 'ALL' and $filtroData == $filtroData2) {
		$sql_csv = "SELECT * FROM pedidos 
		WHERE
			usuarioID = {$_SESSION["adminID"]} AND
			pedidoStatus = $filtroStatus AND
			pedidoData >= '$filtroData'
		ORDER BY 
			pedidoID DESC";
	}
	if  ($filtroStatus == 'ALL' and $filtroProduto == 'ALL' and $filtroData == $filtroData2) {
		$sql_csv = "SELECT * FROM pedidos 
		WHERE
			usuarioID = {$_SESSION["adminID"]} AND
			pedidoData >= '$filtroData'
		ORDER BY 
			pedidoID DESC";
	}
	$query_csv = mysqli_query($con, $sql_csv);
	$total = mysqli_num_rows($query_csv);
	if($total <= 0) {
		header("Location: ". SITE_URL ."painel/?csvnot=true");
	} else {
		$delimiter = ",";
		$filename = "pedidos_" . date('Y-m-d') . ".csv";
		$f = fopen('php://memory', 'w');

		$fields = array('pedidoNome', 'pedidoEmail', 'pedidoTelefone', 'pedidoCPF', 'pedidoCEP', 'pedidoEnderecoRua', 'pedidoEnderecoNumero', 'pedidoEnderecoBairro', 'pedidoEnderecoCidade', 'pedidoEnderecoEstado', 'pedidoEnderecoComplemento', 'pedidoProduto', 'pedidoValor');
		fputcsv($f, $fields, $delimiter);
		while($row = $query_csv->fetch_assoc()){
			$lineData = array($row['pedidoNome'], $row['pedidoEmail'], $row['pedidoTelefone'], $row['pedidoCPF'], $row['pedidoCEP'], $row['pedidoEnderecoRua'], $row['pedidoEnderecoNumero'], $row['pedidoEnderecoBairro'], $row['pedidoEnderecoCidade'], $row['pedidoEnderecoEstado'], $row['pedidoEnderecoComplemento'], $row['pedidoProduto'], $row['pedidoValor']);
			fputcsv($f, $lineData, $delimiter);
		};
		fseek($f, 0);
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '";');
		fpassthru($f);
		
	}
	exit();
}
?>

<html>
<head>
	<title>Administração</title>

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">

	<!-- Google web fonts -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,700i&display=swap" rel="stylesheet">

	<!-- CSS -->
	<link href="<?php echo SITE_URL .'css/bootstrap.min.css'; ?>" rel="stylesheet">
	<link href="<?php echo SITE_URL .'css/bootstrap-datetimepicker.min.css'; ?>" rel="stylesheet">
	<link href="<?php echo SITE_URL .'css/bootstrap-toggle.min.css'; ?>" rel="stylesheet">
	<link href="<?php echo SITE_URL .'css/font-awesome.css'; ?>" rel="stylesheet">
	<link href="<?php echo SITE_URL .'css/painel.css?v='. VERSAO; ?>" rel="stylesheet">

</head>
<body>
	<div class="container">
		<?php
		$mensagem = isset($mensagem) ? $mensagem : '';

		if ($logado) {
			$pagina = isset($_GET['pg']) ? anti_injection($_GET['pg']) : 'pedidos'; ?>

			<div class="painel">
				<div class="row">
					<div class="col-md-3">
						<ul class="list-group">
						  	<li class="list-group-item">
						  		<a href="<?php echo SITE_URL .'painel/?pg=pedidos'; ?>"></i> Pedidos</a>
						  	</li>
						  	<li class="list-group-item">
						  		<a href="<?php echo SITE_URL .'painel/?pg=produtos'; ?>"></i> Produtos</a>
							  </li>
							<?php if($_SESSION["adminRole"] === "admin"){ ?>
						  	<li class="list-group-item">
						  		<a href="<?php echo SITE_URL .'painel/?pg=usuarios'; ?>"></i> Usuários</a>
							  </li>
							<?php } ?>
							<li class="list-group-item">
						  		<a href="<?php echo SITE_URL .'painel/?pg=credenciais'; ?>"></i> Credenciais Mercado Pago</a>
						  	</li>
						  	<li class="list-group-item">
						  		<a href="<?php echo SITE_URL .'painel/?sair'; ?>"></i> Sair</a>
						  	</li>
						</ul>
					</div>
					<div class="col-md-9">
						<?php
						if (is_file($pagina . '.php')) {
							include($pagina . '.php');
						} else {
							include('produtos.php');
						} ?>
					</div>
				</div>
			</div>

			<?php
		} else { ?>
		<?php
				$pagina = isset($_GET['pg']) ? anti_injection($_GET['pg']) : "";
				if($pagina == "registrar"){
			?>
				<div class="login-form">
					<form action="" method="post">
						<h2 class="text-center">Registrar-se</h2>
							<div class="form-group">
								<input name="registrarLogin" type="email" class="form-control" placeholder="E-mail" required="required">
							</div>
							<div class="form-group">
								<input name="registrarPhone"   placeholder="(88) 88888-8888" pattern="[0-9]+$" title="Permitido apenas números" type="tel" class="form-control" required="required">
							</div>
							<div class="form-group">
								<input name="registrarSenha" type="password" class="form-control" placeholder="Senha" required="required">
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-block">Registrar</button>
							</div>
						<a href="<?php echo SITE_URL . "painel/" ?>">Login</a>
					</form>
				</div>
			<?php } else { ?>
			<div class="login-form">
	    		<form action="" method="post">
	        		<h2 class="text-center">Acesso</h2>
	        			<div class="form-group">
	            			<input name="login" type="text" class="form-control" placeholder="Login" required="required">
	        			</div>
	        			<div class="form-group">
	            			<input name="senha" type="password" class="form-control" placeholder="Senha" required="required">
	        			</div>
	        			<div class="form-group">
	            			<button type="submit" class="btn btn-primary btn-block">Acessar</button>
						</div>
						<a href="<?php echo SITE_URL . "painel/?pg=registrar" ?>">Registre-se</a>
	    		</form>
			</div>
			<?php } ?>
			<?php
		} ?>

	</div>
	<script src="<?php echo SITE_URL .'js/jquery.js'; ?>"></script>
	<script src="<?php echo SITE_URL .'js/bootstrap.min.js'; ?>"></script>
	<script src="<?php echo SITE_URL .'js/moment-with-locales.min.js'; ?>"></script>
	<script src="<?php echo SITE_URL .'js/bootstrap-datetimepicker.min.js'; ?>"></script>
	<script src="<?php echo SITE_URL .'js/bootstrap-toggle.min.js'; ?>"></script>
	<script>
		<?php echo empty($mensagem) ? '' : "alert('$mensagem')"; ?>
	</script>
	<script>
			$(function () {
                $('#dataFilter').datetimepicker({
					locale: 'pt-br',
					format: 'YYYY-MM-DD'
				});
				$('#dataFilter2').datetimepicker({
					locale: 'pt-br',
					format: 'YYYY-MM-DD'
				});
            });
	</script>
	<script>
		function filtrar(){
			$("#inputaction").html("<input type='hidden' name='filtrar' value='true' id='inputFilter' />"); 
		}
		function exportar(){
			$("#inputaction").html("<input type='hidden' name='csv' value='true' id='inputExpor' />"); 
		}		
	</script>
	<script>setTimeout(function(){ $('.alertcsv').css('display','none') }, 3000);</script>
	<script>
		$(function() {
		$('#toggle-event').change(function() {
			$(this).val($(this).prop('checked'))
		})
		})
	</script>
	<script>
		$(function() {
		$('#toggle-event2').change(function() {
			$(this).val($(this).prop('checked'))
		})
		})
	</script>
	<script>
		$(function() {
		$('#toggle-event3').change(function() {
			$(this).val($(this).prop('checked'))
		})
		})
	</script>
	<script>
		$(function() {
		$('#toggle-event4').change(function() {
			$(this).val($(this).prop('checked'))
		})
		})
	</script>
</body>
</html>
