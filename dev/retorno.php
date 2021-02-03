<?php

    require_once('config.php');
    require_once('funcoes.php');
    require_once('lib/mercadopago.php');

    $userID = $_GET["ucod"];

    $userQuery = mysqli_query($con, "SELECT * FROM admins WHERE adminID = $userID limit 1");
    $user = mysqli_fetch_array($userQuery);
    
    $mp = new MP ($user["MP_TOKEN"]);
    $mp->sandbox_mode($user["useSandbox"]);
    
    $json_event = file_get_contents('php://input', true);
    $event = json_decode($json_event);

    if (!isset($event->type, $event->data) || !ctype_digit($event->data->id)) {
        http_response_code(400);
        return;
    }

    if ($event->type == 'payment'){
        $payment_info = $mp->get('/v1/payments/'.$event->data->id);
        if ($payment_info["status"] == 200) {
            print_r($payment_info["response"]);
            $array = $payment_info["response"];
            file_put_contents('mplog.txt', print_r($array, true));

            getStatus($_GET["id"], $user["MP_TOKEN"]);
        }
    }
?>