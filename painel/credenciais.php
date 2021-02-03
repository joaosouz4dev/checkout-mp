<?php
$post = method_post();
if(isset($post["MPKEY"]) || isset($post["MPTOKEN"]) || isset($post["CLIENTID"]) || isset($post["CLIENTSECRET"])){
    $useSandbox = isset($post["useSandbox"]) ? $post["useSandbox"] : '';
    $mpkey = isset($post["MPKEY"]) ? $post["MPKEY"] : '';
    $mptoken = isset($post["MPTOKEN"]) ? $post["MPTOKEN"] : '';
    $clientid = isset($post["CLIENTID"]) ? $post["CLIENTID"] : '';
    $clientsecret = isset($post["CLIENTSECRET"]) ? $post["CLIENTSECRET"] : '';

    $query = mysqli_query($con, "UPDATE admins SET
                        useSandbox = $useSandbox,
                        MP_KEY = '{$mpkey}',
                        MP_TOKEN = '{$mptoken}',
                        CLIENT_ID = '{$clientid}',
                        CLIENT_SECRET = '{$clientsecret}'
                        WHERE adminID = {$_SESSION["adminID"]}");
    if($query){
        $mensagem = "Dados Salvos com Sucesso";
    }
}

$query = mysqli_query($con, "SELECT * FROM admins WHERE adminID = {$_SESSION["adminID"]} LIMIT 1");

$dados = mysqli_fetch_array($query);
?>

<form action="<?php echo SITE_URL .'painel/?pg=credenciais'; ?>" method="POST">
    <p>Em caso de dúvida, suas credenciais estão disponíveis através deste <a target="_blank" href="https://www.mercadopago.com/mlb/account/credentials">Link</a></p>
    <div class="form-group" style="display:none">
        <label>Modo Sandbox</label>
        <select name="useSandbox" class="form-control">
            <option value="true" <?php if($dados["useSandbox"]) echo "selected"; ?>>Ativo</option>
            <option value="false" <?php if(!$dados["useSandbox"]) echo "selected"; ?>>Inativo</option>
        </select>
    </div>
    <div class="form-group">
        <label>Public key</label>
        <input type="text" name="MPKEY" value="<?php echo isset($dados['MP_KEY']) ? $dados['MP_KEY'] : ''; ?>" class="form-control" />
    </div>
    <div class="form-group">
        <label>Access token</label>
        <input type="text" name="MPTOKEN" value="<?php echo isset($dados['MP_TOKEN']) ? $dados['MP_TOKEN'] : ''; ?>" class="form-control" />
    </div>
    <div class="form-group">
        <label>CLIENT_ID</label>
        <input type="text" name="CLIENTID" value="<?php echo isset($dados['CLIENT_ID']) ? $dados['CLIENT_ID'] : ''; ?>" class="form-control" />
    </div>
    <div class="form-group">
        <label>CLIENT_SECRET</label>
        <input type="text" name="CLIENTSECRET" value="<?php echo isset($dados['CLIENT_SECRET']) ? $dados['CLIENT_SECRET'] : ''; ?>" class="form-control" />
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-success btn-block">Salvar</button>
    </div>
</form>
