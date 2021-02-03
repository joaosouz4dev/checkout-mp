<?php

   session_start();

   include('config.php');
   include('funcoes.php');

   $compraID      = 0;
   $compraProduto = '';
   $compraFrete   = '';
   $compraValor   = '';
   $compraPixel   = '';
   $compraCodigo  = '';
   $pagamentoTipo = 'boleto';

   if (isset($_GET['p']))
       $compraCodigo = anti_injection($_GET['p']);

   $query = mysqli_query($con, "SELECT *
       FROM produtos
       WHERE
           produtoCodigo = '$compraCodigo'
       LIMIT 1;");

   $total = mysqli_num_rows($query);
   if ($total > 0) {
       $lista = mysqli_fetch_array($query);

   	$compraID      = $lista['produtoID'];
   	$compraProduto = $lista['produtoNome'];
   	$compraFrete   = $lista['produtoFrete'];
   	$compraValor   = $lista['produtoValor'];
   	$compraPixel   = $lista['produtoPixel'];
   	$compraTotal   = number_format($compraValor + $compraFrete, 2);
   } ?>
<html>
   <head>
      <title>Pagamento produto <?php echo $compraProduto; ?></title>
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta charset="UTF-8">
      <!-- Google web fonts -->
      <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet'>
      <!-- CSS -->
      <link href="<?php echo SITE_URL .'css/bootstrap.min.css'; ?>" rel="stylesheet">
      <link href="<?php echo SITE_URL .'css/toastr.min.css'; ?>" rel="stylesheet">
      <link href="<?php echo SITE_URL .'css/font-awesome.css'; ?>" rel="stylesheet">
      <link href="<?php echo SITE_URL .'css/style.css?v='. VERSAO; ?>" rel="stylesheet">
        <script type="text/javascript">
    window.smartlook||(function(d) {
    var o=smartlook=function(){ o.api.push(arguments)},h=d.getElementsByTagName('head')[0];
    var c=d.createElement('script');o.api=new Array();c.async=true;c.type='text/javascript';
    c.charset='utf-8';c.src='https://rec.smartlook.com/recorder.js';h.appendChild(c);
    })(document);
    smartlook('init', '68f266d55e69565111986f5b5f9c7c38980ba372');
</script>
   </head>
   <body>
      <div class="container">
         <div class="pagina">
            <?php
               if ($total == 0) {
               	echo '
               		<div class="pagina-conteudo">
               			<div class="alert alert-danger text-center">Produto inválido. Entre em contato com o suporte.</div>
               		</div>';
               } else { ?>
            <div class="topo">
               <div class="compra-selo">
                  <i class="fa fa-lock"></i> COMPRA SEGURA
               </div>
               <div class="compra-titulo">
                  <?php echo empty($compraProduto) ? 'NOME DO PRODUTO' : $compraProduto; ?>
               </div>
               <div class="clearfix"></div>
            </div>
            <?php
               if (isset($_GET['id'])) {
               	$pagamentoID = isset($_GET['id']) ? anti_injection($_GET['id']) : '';

               	$query = mysqli_query($con, "SELECT *
               		FROM pedidos
               		WHERE
               			pedidoCodigo = '$pagamentoID'
               		LIMIT 1;");

               	$total = mysqli_num_rows($query);
               	if ($total > 0) {
               		$lista = mysqli_fetch_array($query);
               		if ($lista['pedidoPagamento'] == 'Boleto bancário') {
               			require_once 'barras/boletosPHP.php';

               			$barras = new boletosPHP();
               			$barras->setBarras($lista['pedidoBarcode']); ?>
            <div class="pagina-conteudo">
               <h4 class="text-center">Agora só falta pagar o seu boleto</h4>
               <div style="margin: 0 auto; max-width: 600px;">
                  <div class="text-center">
                     <a class="botao-boleto" href="<?php echo $lista['pedidoLinkBoleto']; ?>" target="_blank">
                     <i class="fa fa-download"></i> BAIXAR BOLETO
                     </a>
                     <p>Ou copie o código de barras abaixo para efetuar o pagamento:</p>
                  </div>
                  <div class="boleto-campo">
                     <span class="boleto-label">CÓDIGO DE BARRAS</span>
                     <span class="boleto-code" id="boleto-code"><?php echo $barras->getIpte(); ?></span>
                     <a href="javascript:;" class="boleto-copy" onclick="copyToClipboard('#boleto-code')"><i class="fa fa-copy"></i></a>
                  </div>
               </div>
            </div>
            <?php
               } else {
               	$pagamentoTipo = 'cartao'; ?>
            <div class="pagina-conteudo">
               <!--msg cartao-->
               <div class="row">
                  <div class="col-sm-12">
                     <center>
                        <img width="155px" src="img/valido.png" alt=""/><br>
                        <h1>Pedido Finalizado</h1>
                        <h3>Obrigado por comprar em nossa loja! </h3>
                        <br> Seu numero de pedido é: <?php echo $lista['pedidoCodigo']; ?> <br>
                     </center>
                  </div>
               </div>
               <!--end msg cartao-->
            </div>
            <?php
               }
               }
               } else {

               $pagina = isset($_GET['pg']) ? $_GET['pg'] : '';

               if ($pagina == '') { ?>
            <form action="<?php echo SITE_URL .'?p='. $compraCodigo .'&pg=pagamento'; ?>" method="POST" id="frmDados">
               <div class="etapa-titulo">
                  <span class="num">1</span> Dados Pessoais
                  <span class="linha"></span>
               </div>
               <p class="pagina-info">Digite seus dados pessoais abaixo para iniciar a sua compra.</p>
               <div class="pagina-conteudo">
                  <div class="form-group">
                     <div class="campo-icone" id="div-cliente_email">
                        <i class="fa fa-envelope"></i>
                        <label for="cliente_email">Seu email</label>
                        <input required placeholder="Seu email" type="text" id="cliente_email" name="cliente_email" maxlength="120" class="form-control floatlabel" value="<?php echo isset($_SESSION['cliente_email']) ? $_SESSION['cliente_email'] : ''; ?>" />
                     </div>
                     <span class="error"></span>
                  </div>
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <div class="form-campo" id="div-cliente_nome">
                              <label for="cliente_nome">Seu Nome Completo</label>
                              <input required placeholder="Seu Nome Completo" type="text" id="cliente_nome" name="cliente_nome" maxlength="60" class="form-control floatlabel" value="<?php echo isset($_SESSION['cliente_nome']) ? $_SESSION['cliente_nome'] : ''; ?>" />
                           </div>
                           <span class="error"></span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <div class="form-campo" id="div-celular">
                              <label for="celular">Celular</label>
                              <input placeholder="Celular" type="tel" id="celular" name="celular" maxlength="15" class="form-control floatlabel" value="<?php echo isset($_SESSION['celular']) ? $_SESSION['celular'] : ''; ?>" onkeyup="mascara(this, mtel)" />
                           </div>
                           <span class="error"></span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <div class="form-campo" id="div-cliente_cpf">
                              <label for="cliente_cpf">CPF</label>
                              <input required placeholder="CPF" type="tel" id="cliente_cpf" name="cliente_cpf" maxlength="11" class="form-control floatlabel" value="<?php echo isset($_SESSION['cliente_cpf']) ? $_SESSION['cliente_cpf'] : ''; ?>" />
                           </div>
                           <span class="error"></span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="etapa-titulo">
                  <span class="num">2</span> Endereço de Entrega
                  <span class="linha"></span>
               </div>
               <p class="pagina-info">Digite o CEP para onde vamos enviar o seu pedido abaixo.</p>
               <div class="pagina-conteudo">
                  <div class="campo-icone" id="div-endereco_cep">
                     <i class="fa fa-home"></i>
                     <label for="endereco_cep">CEP</label>
                     <input placeholder="CEP" onkeypress="return so_numeros(event)" required type="tel" id="endereco_cep" name="endereco_cep" maxlength="8" class="form-control floatlabel" value="<?php echo isset($_SESSION['endereco_cep']) ? $_SESSION['endereco_cep'] : ''; ?>" />
                  </div>
                  <span class="error"></span>
               </div>
               <?php $rua = isset($_SESSION['endereco_rua']) ? $_SESSION['endereco_rua'] : ''; ?>
               <div class="pagina-conteudo" id="endereco" style="<?php echo empty($rua) ? 'display: none;' : ''; ?> padding-top: 0;">
                  <div class="row">
                     <div class="col-md-9">
                        <div class="form-group">
                           <div class="form-campo" id="div-endereco_rua">
                              <label for="endereco_rua">Endereço</label>
                              <input placeholder="Endereço" required type="text" id="endereco_rua" name="endereco_rua" maxlength="120" class="form-control floatlabel" value="<?php echo $rua; ?>" />
                           </div>
                           <span class="error"></span>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <div class="form-campo" id="div-endereco_n">
                              <label for="endereco_n">Número</label>
                              <input placeholder="Número" required type="tel" id="endereco_n" name="endereco_n" maxlength="20" class="form-control floatlabel" value="<?php echo isset($_SESSION['endereco_n']) ? $_SESSION['endereco_n'] : ''; ?>" onkeypress="return so_numeros(event)" />
                           </div>
                           <span class="error"></span>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <div class="form-campo" id="div-endereco_bairro">
                              <label for="endereco_bairro">Bairro</label>
                              <input placeholder="Bairro" required type="text" id="endereco_bairro" name="endereco_bairro" maxlength="60" class="form-control floatlabel" value="<?php echo isset($_SESSION['endereco_bairro']) ? $_SESSION['endereco_bairro'] : ''; ?>" />
                           </div>
                           <span class="error"></span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <div class="form-group">
                              <div class="form-campo" id="div-endereco_cidade">
                                 <label for="endereco_cidade">Cidade</label>
                                 <input placeholder="Cidade" required type="text" id="endereco_cidade" name="endereco_cidade" maxlength="80" class="form-control floatlabel" value="<?php echo isset($_SESSION['endereco_cidade']) ? $_SESSION['endereco_cidade'] : ''; ?>" />
                              </div>
                              <span class="error"></span>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <div class="form-group">
                              <div class="form-campo" id="div-endereco_uf">
                                 <label for="endereco_uf">Estado</label>
                                 <input placeholder="Estado" required type="text" id="endereco_uf" name="endereco_uf" maxlength="2" class="form-control floatlabel" value="<?php echo isset($_SESSION['endereco_uf']) ? $_SESSION['endereco_uf'] : ''; ?>" />
                              </div>
                              <span class="error"></span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="form-group">
                        <div class="form-campo" id="div-complemento">
                           <label for="complemento">Complemento</label>
                           <input placeholder="Complemento" required type="text" id="complemento" name="complemento" maxlength="80" class="form-control floatlabel" value="<?php echo isset($_SESSION['complemento']) ? $_SESSION['complemento'] : ''; ?>" />
                        </div>
                        <span class="error"></span>
                     </div>
                  </div>
               </div>
               <div class="pagina-conteudo">
                  <div class="info-seguro"><i class="fa fa-lock"></i> Você está em uma página segura.</div>
                  <a href="javascript:;" class="btn btn-continuar pull-right btn-dados"><i class="fa fa-lock"></i> CONTINUAR</a>
                  <div class="clearfix"></div>
               </div>
            </form>
            <?php
               } else if ($pagina == 'pagamento') {

               	if (isset($_POST['endereco_cep']))
               		$_SESSION['endereco_cep'] = anti_injection($_POST['endereco_cep']);

               	if (isset($_POST['endereco_rua']))
               		$_SESSION['endereco_rua'] = anti_injection($_POST['endereco_rua']);

               	if (isset($_POST['endereco_n']))
               		$_SESSION['endereco_n'] = anti_injection($_POST['endereco_n']);

               	if (isset($_POST['endereco_bairro']))
               		$_SESSION['endereco_bairro'] = anti_injection($_POST['endereco_bairro']);

               	if (isset($_POST['endereco_cidade']))
               		$_SESSION['endereco_cidade'] = anti_injection($_POST['endereco_cidade']);

               	if (isset($_POST['endereco_uf']))
               		$_SESSION['endereco_uf'] = anti_injection($_POST['endereco_uf']);

               	if (isset($_POST['complemento']))
               		$_SESSION['complemento'] = anti_injection($_POST['complemento']);

               	if (isset($_POST['cliente_email']))
               		$_SESSION['cliente_email'] = anti_injection($_POST['cliente_email']);

               	if (isset($_POST['cliente_nome']))
               		$_SESSION['cliente_nome'] = anti_injection($_POST['cliente_nome']);

               	if (isset($_POST['celular']))
               		$_SESSION['celular'] = anti_injection($_POST['celular']);

               	if (isset($_POST['cliente_cpf']))
               		$_SESSION['cliente_cpf'] = anti_injection($_POST['cliente_cpf']); ?>
            <form action="" method="POST" id="frmPagar">
               <ul class="pagamento-tabs">
                  <?php
                     if (OPCAO_CARTAO)
                     	echo '<li><a href="javascript:;" data-id="cartao" class="ativo"><i class="fa fa-credit-card"></i> Cartão</a></li>';

                     if (OPCAO_BOLETO)
                     	echo '<li><a href="javascript:;" data-id="boleto"><i class="fa fa-barcode"></i> Boleto</a></li>'; ?>
                  <div class="clearfix"></div>
               </ul>
               <?php
                  if (OPCAO_CARTAO) { ?>
               <div class="etapa-titulo pagamento-titulo botao-tab" data-id="cartao">
                  <span class="num"><i class="fa fa-credit-card"></i></span> Pagar com Cartão
                  <span class="linha"></span>
               </div>
               <div class="pagina-conteudo pagamento-conteudo" id="cartao">
                  <p>Para finalizar sua compra, digite os dados que ficam na frente do seu cartão abaixo.</p>
                  <div class="card-imagem">
                     <div class="card-conteudo">
                        <div class="card-background">
                           <img src="<?php echo SITE_URL .'img/card.png'; ?>" />
                        </div>
                        <div id="card-frente" class="card-frente">
                           <div class="card-imagem-num">0000 0000 0000 0000</div>
                           <div class="card-imagem-data-label">VÁLIDO ATÉ</div>
                           <div class="card-imagem-data">00/00</div>
                           <img src="<?php echo SITE_URL .'img/card.png'; ?>" />
                        </div>
                        <div id="card-cvv" class="card-cvv">
                           <img src="<?php echo SITE_URL .'img/card-fundo.png'; ?>" />
                           <div class="card-imagem-cvv">000</div>
                        </div>
                     </div>
                  </div>
                  <div class="card-campos">
                     <div class="form-group">
                        <div class="campo-icone" id="div-cardholderName">
                           <i class="fa fa-user"></i>
                           <label for="cardholderName">Nome: (igual no cartão)</label>
                           <input data-checkout="cardholderName" placeholder="Nome: (igual no cartão)" type="text" id="cardholderName" name="cardholderName" maxlength="45" class="form-control floatlabel" value="" />
                        </div>
                        <span class="error"></span>
                     </div>
                     <div class="form-group">
                        <div class="campo-icone" id="div-docNumber">
                           <i class="fa fa-user"></i>
                           <label for="docNumber">CPF: (Titular do cartão)</label>
                           <input data-checkout="docNumber" placeholder="CPF: (Titular do cartão)" type="text" id="docNumber" name="docNumber" maxlength="15" class="form-control floatlabel" value="" />
                        </div>
                        <span class="error"></span>
                     </div>
                     <div class="form-group">
                        <div class="campo-icone" id="div-cardNumber">
                           <i class="fa fa-credit-card"></i>
                           <label for="cardNumber">Número do Cartão</label>
                           <input data-checkout="docType" type="hidden" value="CPF"/>
                           <input data-checkout="cardNumber" placeholder="Número do Cartão" type="text" id="cardNumber" name="cardNumber" maxlength="16" class="form-control floatlabel" value="" onkeypress="return so_numeros(event)" />
                           <span id="bandeira" class="bandeira"></span>
                        </div>
                        <span class="error"></span>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <div class="campo-icone" id="div-cartao_venc">
                                 <i class="fa fa-calendar"></i>
                                 <label for="cartao_venc">Expiração - MM/AAAA</label>
                                 <input placeholder="Expiração - MM/AAAA" type="tel" id="cartao_venc" name="cartao_venc" maxlength="7" class="form-control floatlabel" value="" onkeyup="mascara(this, mvenc)" />
                              </div>
                              <span class="error"></span>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <div class="campo-icone" id="div-securityCode">
                                 <i class="fa fa-lock"></i>
                                 <label for="securityCode">Código de Segurança</label>
                                 <input data-checkout="securityCode" placeholder="CVV" type="tel" id="securityCode" name="securityCode" maxlength="4" class="form-control floatlabel" value="" onkeypress="return so_numeros(event)" />
                              </div>
                              <span class="error"></span>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group" id="div-installments">
                              <label for="installments" class="initlabel transition showlabel" style="left: 0; top: -15px;">Parcelas</label>
                              <select class="form-control" id="installments" name="installmentsOption" style="padding: 24px 18px 10px;">
                                 <option value="0" data-price="0">Escolha...</option>
                              </select>
                              <span class="error"></span>
                           </div>
                           <span class="error"></span>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <a href="javascript:;" class="btn btn-continuar btn-block pull-right botao-fechar finalizar-cartao"><i class="fa fa-lock"></i> FINALIZAR</a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="clearfix"></div>
               </div>
               <?php
                  }

                  if (OPCAO_BOLETO) { ?>
               <div class="etapa-titulo pagamento-titulo botao-tab" data-id="boleto">
                  <span class="num"><i class="fa fa-barcode"></i></span> Pagar com Boleto <span class="avista"><i class="fa fa-exclamation-circle"></i> À Vista</span>
                  <span class="linha"></span>
               </div>
               <div class="pagina-conteudo pagamento-conteudo" id="boleto" <?php echo OPCAO_CARTAO ? 'style="display: none"' : ''; ?>>
                  <div class="form-group text-center">
                     <h4>Clique no botão para gerar o boleto:</h4>
                  </div>
                  <div class="form-group text-center">
                     <a href="javascript:;" class="btn btn-continuar finalizar-boleto botao-fechar"><i class="fa fa-check"></i> Gerar Boleto</a>
                  </div>
                  <div class="row">
                     <div class="col-md-4">
                        <div class="boleto-info">
                           <span class="boleto-info-num">1</span><br />
                           <span class="boleto-info-titulo">Valor à vista:</span><br />
                           <strong>R$ <?php echo fmoney($compraValor + $compraFrete); ?></strong> Não pode ser parcelado.
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="boleto-info">
                           <span class="boleto-info-num">2</span><br />
                           <span class="boleto-info-titulo">Pode levar até:</span><br />
                           <strong>2 dias úteis</strong> para compensar.
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="boleto-info">
                           <span class="boleto-info-num">3</span><br />
                           <span class="boleto-info-titulo">Atenção:</span><br />
                           ao <strong>prazo para pagamento</strong> do boleto!
                        </div>
                     </div>
                  </div>
                  <div class="clearfix"></div>
               </div>
               <?php
                  } ?>
               <input type="hidden" name="cardExpirationMonth" id="cardExpirationMonth" value="" data-checkout="cardExpirationMonth" />
               <input type="hidden" name="cardExpirationYear" id="cardExpirationYear" value="" data-checkout="cardExpirationYear" />
               <input type="hidden" name="tipo" id="tipo" value="cartao" />
               <input type="hidden" id="docType" data-checkout="docType" value="CPF" />
               <input type="hidden" id="total_parcelas" name="total_parcelas" value="" />
               <input type="hidden" name="codigo" value="<?php echo $compraCodigo; ?>" />
               <input type="hidden" id="amount" name="amount" value="<?php echo number_format($compraValor + $compraFrete, 2); ?>" />
            </form>
            <?php
               }
               } ?>
            <footer>
               &copy; Checkout MP - <?php echo date('Y'); ?>
            </footer>
            <?php
               } ?>
         </div>
      </div>
      <?php
         if (!empty($compraPixel)) { ?>
      <!-- Facebook Pixel Code -->
      <script>
         !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
         n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
         n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
         t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
         document,'script','//connect.facebook.net/en_US/fbevents.js');
         fbq('init', '<?php echo $compraPixel; ?>');
      </script>
      <!-- End Facebook Pixel Code -->
      <?php
         if (isset($_GET['id'])) {

         	if (($pagamentoTipo == 'cartao' && PIXEL_CARTAO) ||
         		($pagamentoTipo == 'boleto' && PIXEL_BOLETO)) { ?>
      <!-- Facebook Conversion Event: Purchase -->
      <script>
         fbq('track', 'Purchase', {
         	value: <?php echo $compraTotal; ?>,
         	currency: 'BRL',
         	content_name: '<?php echo $compraProduto; ?>'
         });
      </script>
      <!-- End Facebook Conversion Event: Purchase -->
      <?php
         }
         } else { ?>
      <script>
         fbq('track', 'PageView');
         fbq('track', 'InitiateCheckout');
      </script>
      <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo $compraPixel; ?>&&amp;ev=PageView&amp;noscript=1"></noscript>
      <?php
         } ?>
      <?php
         } ?>
      <!-- JS -->
      <script type="text/javascript">
         window.redirecionar = '<?php echo REDIRECIONAR_APOS_PAGAMENTO; ?>';
         window.mp_key       = '<?php echo MP_KEY; ?>';
      </script>
      <script src="<?php echo SITE_URL .'js/jquery.js'; ?>"></script>
      <script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
      <script src="<?php echo SITE_URL .'js/bootstrap.min.js'; ?>"></script>
      <script src="<?php echo SITE_URL .'js/toastr.min.js'; ?>"></script>
      <script src="<?php echo SITE_URL .'js/floatlabel.js'; ?>"></script>
      <script src="<?php echo SITE_URL .'js/funcoes.js?v='. VERSAO; ?>"></script>
   </body>
</html>
