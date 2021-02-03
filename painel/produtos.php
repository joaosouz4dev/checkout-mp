<?php
if (isset($_GET['add']) || isset($_GET['edit'])) { 

	$action = SITE_URL .'painel/?pg=produtos&add'; 
	$id     = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;

	if (isset($_GET['edit']))
		$action = SITE_URL .'painel/?pg=produtos&edit=' . $id; 

	if (isset($_POST['produtoNome'])) {
		$post = method_post();
		$opcaoCartao   = stringToBool($post['opcaoCartao']);
		$opcaoBoleto   = stringToBool($post['opcaoBoleto']);
		$pixelCartao   = stringToBool($post['pixelCartao']);
		$pixelBoleto   = stringToBool($post['pixelBoleto']);
		if (empty($post['produtoNome'])) {
			$mensagem = 'Preencha corretamente o campo Nome';
		} else if (empty($post['produtoValor'])) {
			$mensagem = 'Preencha corretamente o campo Valor';
		} else {
			if ($id > 0) {
				$query = mysqli_query($con, "UPDATE produtos SET 
						produtoNome   = '". $post['produtoNome'] ."',
						produtoValor  = '". $post['produtoValor'] ."',
						produtoFrete  = '". $post['produtoFrete'] ."',
						produtoPixel  = '". $post['produtoPixel'] ."',
						opcaoCartao   = $opcaoCartao,
						opcaoBoleto   = $opcaoBoleto,
						pixelCartao   = $pixelCartao,
						pixelBoleto   = $pixelBoleto,
						urlAgradecimento  = '". $post['urlAgradecimento'] ."'
					WHERE
						produtoID = $id
					LIMIT 1;") or die(mysqli_error($con));

				if ($query) {
					
					$mensagem = 'Alterações foram salvas';
				} else {
					$mensagem = 'Erro ao alterar';
				}

			} else {

				$codigo = md5(uniqid(rand(), true));
				$query = mysqli_query($con, "INSERT INTO produtos
					(produtoNome,
					produtoValor,
					produtoFrete,
					produtoCodigo,
					produtoPixel,
					opcaoCartao,
					opcaoBoleto,
					pixelCartao,
					pixelBoleto,
					urlAgradecimento,
					usuarioID) 
					VALUES
					('". $post['produtoNome'] ."',
					'". $post['produtoValor'] ."',
					'". $post['produtoFrete'] ."',
					'". $codigo ."',
					'". $post['produtoPixel'] ."',
					$opcaoCartao,
					$opcaoBoleto,
					$pixelCartao,
					$pixelBoleto,
					'". $post['urlAgradecimento'] ."',
					{$_SESSION["adminID"]})") or die(mysqli_error($con));
		
				if ($query) {
					$mensagem = 'Produto foi adicionado';
				} else {
					$mensagem = 'Erro ao adicionar produto';
				}
			}
		}
	} 

	$lista = array();
	if ($id > 0) {
		$query = mysqli_query($con, "SELECT * 
			FROM produtos
			WHERE 
				produtoID = $id AND usuarioID = {$_SESSION["adminID"]}
			ORDER BY produtoID DESC
			LIMIT 1");

		$lista = mysqli_fetch_array($query);
		if(!$lista){
			$redirect_prod = SITE_URL . "painel/?pg=produtos";
			header("Location: $redirect_prod");
		}
	} ?>

	<h2 class="painel-titulo"><?php echo isset($_GET['add']) ? 'Adicionar produto' : 'Editar produto'; ?></h2>

	<form action="<?php echo $action; ?>" method="POST">
		<div class="form-group">
			<label>Nome</label>
			<input type="text" name="produtoNome" value="<?php echo isset($lista['produtoNome']) ? $lista['produtoNome'] : ''; ?>" class="form-control" />
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label>Valor</label>
					<input placeholder="Ex: 123.00" type="text" name="produtoValor" value="<?php echo isset($lista['produtoValor']) ? $lista['produtoValor'] : ''; ?>" class="form-control" />
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label>Valor do Frete</label>
					<input placeholder="Ex: 123.00" type="text" name="produtoFrete" value="<?php echo isset($lista['produtoFrete']) ? $lista['produtoFrete'] : ''; ?>" class="form-control" />
				</div>
			</div>
		</div>
		<style>
		.form-group.toggle {
			display: flex;
			align-items: flex-start;
			justify-content: center;
			flex-direction: column;
		}
		</style>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group toggle">
					<label>Opção Cartão</label>
					<input id="toggle-event" type="checkbox" name="opcaoCartao" <?php if ($lista['opcaoCartao'] == 1) { echo "checked"; } ?> data-toggle="toggle" data-onstyle="success" value="<?php echo isset($lista['opcaoCartao']) ? $lista['opcaoCartao'] : ''; ?>">
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group toggle">
					<label>Opção Boleto</label>
					<input id="toggle-event2" type="checkbox" name="opcaoBoleto" <?php if ($lista['opcaoBoleto'] == 1) { echo "checked"; } ?> data-toggle="toggle" data-onstyle="success" value="<?php echo isset($lista['opcaoBoleto']) ? $lista['opcaoBoleto'] : ''; ?>">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group toggle">
					<label>Pixel Cartão</label>
					<input id="toggle-event3" type="checkbox" name="pixelCartao" <?php if ($lista['pixelCartao'] == 1) { echo "checked"; } ?> data-toggle="toggle" data-onstyle="success" value="<?php echo isset($lista['pixelCartao']) ? $lista['pixelCartao'] : ''; ?>">
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group toggle">
					<label>Pixel Boleto</label>
					<input id="toggle-event4" type="checkbox" name="pixelBoleto" <?php if ($lista['pixelBoleto'] == 1) { echo "checked"; } ?> data-toggle="toggle" data-onstyle="success" value="<?php echo isset($lista['pixelBoleto']) ? $lista['pixelBoleto'] : ''; ?>">
				</div>
			</div>
		</div>
		
		<div class="form-group">
			<label>Pixel Facebook código</label>
			<input type="text" name="produtoPixel" value="<?php echo isset($lista['produtoPixel']) ? $lista['produtoPixel'] : ''; ?>" class="form-control" />
		</div>

		<div class="form-group">
			<label>Url Agradecimento,</label>
			<small>após uma venda no cartão, redirecionará para a seguinte url:</small>
			<input type="url" onblur="checkURL(this)" placeholder="www.seusite.com" pattern="https://.*" name="urlAgradecimento" value="<?php echo isset($lista['urlAgradecimento']) ? $lista['urlAgradecimento'] : ''; ?>" class="form-control" />
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<button type="submit" class="btn btn-success btn-block">Salvar alterações</button>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<button type="button" class="btn btn-primary btn-block" onclick="location.href='<?php echo $action = SITE_URL .'painel/?pg=produtos'; ?>';">Volar</button>
				</div>
			</div>
		</div>
	</form>

	<?php
} else {

	if (isset($_GET['excluir'])) {
		$id = (int) $_GET['excluir'];
		if ($id > 0) {
			$query = mysqli_query($con, "DELETE FROM produtos 
				WHERE
					produtoID = $id
				LIMIT 1;");

			if ($query) {
				$mensagem = 'Produto foi removido';
			} else {
				$mensagem = 'Erro ao remover produto';
			}
		}
	}

	$query = mysqli_query($con, "SELECT * 
		FROM produtos WHERE usuarioID = {$_SESSION["adminID"]}
		ORDER BY produtoID DESC");

	$total = mysqli_num_rows($query); ?>

	<h2 class="painel-titulo"><?php echo $total; ?> - Produtos</h2>

	<div class="painel-botoes">
		<a href="<?php echo SITE_URL .'painel/?pg=produtos&add'; ?>" class="btn btn-primary">+ Produto</a>
	</div>

	<table class="table table-bordered">
	  	<tr>
	    	<th>ID</th>
	    	<th>NOME</th>
	    	<th>VALOR</th>
	    	<th>FRETE</th>
	    	<th>PIXEL</th>
	    	<th></th>
	  	<tr>

	  	<?php
	  	if ($total == 0) { ?>
	  		<tr>
	  			<td class="text-center" colspan="6"><i>Nenhum registro</i></td>
	  		</tr>
	  		<?php
	  	} else { 
	  		while ($lista = mysqli_fetch_array($query)) { ?>
			  	<tr>
			    	<td><?php echo $lista['produtoID']; ?></td>
			    	<td><?php echo $lista['produtoNome']; ?></td>
			    	<td><?php echo $lista['produtoValor']; ?></td>
			    	<td><?php echo $lista['produtoFrete']; ?></td>
			    	<td><?php echo empty($lista['produtoPixel']) ? '-' : $lista['produtoPixel']; ?></td>
			    	<td width="140">
			    		<div class="btn-group">
			    			<a href="<?php echo SITE_URL .'?p='. $lista['produtoCodigo']; ?>" class="btn btn-warning" target="_blank"><i class="fa fa-external-link"></i></a>
			    			<a href="<?php echo SITE_URL .'painel/?pg=produtos&edit='. $lista['produtoID']; ?>" class="btn btn-primary"><i class="fa fa-edit"></i></a>
			    			<a href="<?php echo SITE_URL .'painel/?pg=produtos&excluir='. $lista['produtoID']; ?>" class="btn btn-danger"><i class="fa fa-trash"></i></a>
			    		</a>
			    	</td>
			  	</tr>
			  	<?php
			}
		} ?>
	<table>
	<?php
} 