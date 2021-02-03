<?php
define('DEBUG', false);

$url = "https://seguro.babyshop.club";

define('OPCAO_CARTAO', true);
define('OPCAO_BOLETO', true);

define('PIXEL_CARTAO', true);
define('PIXEL_BOLETO', true);

define('REDIRECIONAR_APOS_PAGAMENTO', "$url");
define('SITE_URL',     "$url/");
define('NOTIFICA_URL', "$url/retorno.php");

define('BANCO_HOST', 'localhost');
define('BANCO_USER', 'checkout_novo');
define('BANCO_PASS', 'PItFwcAeOx');
define('BANCO_DB',   'checkout_novo');

$con = new mysqli(BANCO_HOST, BANCO_USER, BANCO_PASS, BANCO_DB) or die("Connect failed: %s\n". $con -> error);

$logado = isset($_SESSION['logado']) ? (bool) $_SESSION['logado'] : false;
if($logado){
    $query = mysqli_query($con, "SELECT *
        	FROM admins
			WHERE
				adminID  = {$_SESSION["adminID"]}
			LIMIT 1");
    $lista = mysqli_fetch_array($query);
    if (isset($lista['adminID'])) {
        $sandbox = $lista["useSandbox"] ? true : false;
        define('SANDBOX', $sandbox);

        define('MP_KEY',  $lista["MP_KEY"]);
        define('MP_TOKEN', $lista["MP_TOKEN"]);

        define('CLIENT_ID', $lista["CLIENT_ID"]);
        define('CLIENT_SECRET', $lista["CLIENT_SECRET"]);
    }
}
