<?php
require_once('config.php');
require_once('lib/mercadopago.php');

define('ANE_VERSAO', '1.15.21');

if (!function_exists('anti_injection')) {
    function anti_injection($sql = '') {
        $sql = @trim($sql);
        $sql = @strip_tags($sql);
        $sql = @addslashes($sql);

        return $sql;
    }
}

include('inc/simple_html_dom.php');

if (!function_exists('arrStatus')) {
    function arrStatus() {
        return array(
            1 => 'Aguardando pagamento',
            2 => 'Aprovado',
            3 => 'Cancelado'
        );
    }
}

if (!function_exists('method_post')) {
    function method_post() {
        $post = array();

        if (isset($_POST)) {
            foreach ($_POST as $k => $v) {
                if (empty($v)) {
                    $post[$k] = '';
                } else {
                    $post[$k] = anti_injection($v);
                }
            }
        }

        return $post;
    }
}

if (!function_exists('is_valid_email')) {
    function is_valid_email($mail = '') {
        $regexp = '/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/';
        return preg_match($regexp, $mail);
    }
}

if (!function_exists('fmoney')) {
    function fmoney($str) {
        if (empty($str) || ($str == '0.00'))
            return '0,00';
        
        return number_format($str, 2, ',', '.');
    }
}

if (!function_exists('moeda_para_db')) {
    function moeda_para_db($valor = 0) {
        if (empty($valor))
            return '0.00';

        if (preg_match('/^[0-9]*\.[0-9]+$/', $valor))
            return $valor;
        
        return str_replace(array('.', ',', 'R$'), array('', '.', ''), $valor);
    }
}

function validaCPF($cpf = null) {

	if (empty($cpf))
		return false;

	$cpf = preg_replace("/[^0-9]/", "", $cpf);
	$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
	
	if (strlen($cpf) != 11)
		return false;

	else if ($cpf == '00000000000' || 
		$cpf == '11111111111' || 
		$cpf == '22222222222' || 
		$cpf == '33333333333' || 
		$cpf == '44444444444' || 
		$cpf == '55555555555' || 
		$cpf == '66666666666' || 
		$cpf == '77777777777' || 
		$cpf == '88888888888' || 
		$cpf == '99999999999') {
		
		return false;
	 } else {   
		
		for ($t = 9; $t < 11; $t++) {
			
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf{$c} * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($cpf{$c} != $d) {
				return false;
			}
		}

		return true;
	}
}
function getStatus($id, $mp_token = '') {
    if ($id != null) {
        if($mp_token == ''){
            $mp = new MP (MP_TOKEN);
        } else {
            $mp = new MP ($mp_token);
        }
        $payment_info = $mp->get("/v1/payments/". $id, false);
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
        $query = mysqli_query($con, "UPDATE pedidos SET pedidoStatus='$status' WHERE pedidoIDMP='$id' LIMIT 1;") or die(mysqli_error($con));
    } else {
        
    }
}

function updateStatus($mp_token = '') {
    $query2 = mysqli_query($con, "SELECT * FROM pedidos ORDER BY pedidoID DESC");
    $array2 = array();
    while($row = mysqli_fetch_array($query2)){
        $array2[] = $row['pedidoIDMP'];
    };
    if($mp_token == ''){
        $mp = new MP (MP_TOKEN);
    } else {
        $mp = new MP ($mp_token);
    }
    foreach ($array2 as $value) {
        echo "$value <br>";
        if ($value != null) {
            $payment_info = $mp->get("/v1/payments/". $value, false);
            $statusSwitch = $payment_info["response"]["status"];
            switch ($statusSwitch) {
                    case "approved": 
                        $status = "2";          
                        break;
                    case "pending": 
                        $status = "2";
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
        } else {
            
        }
    }
}
function stringToBool($str){
    if($str === 'true' || $str === 'TRUE' || $str === 'True' || $str === 1 || $str === '1' || $str === 'ON'){
        $str = 1;
    }else{
        $str = 0;
    }
    return $str;
}