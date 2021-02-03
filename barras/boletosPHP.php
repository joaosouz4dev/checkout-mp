<?php
/*
 * Name: boletoPHP
 * Version: 1.0
 * Date: 19/03/2015 - Dia do glorioso São José
 * Author: Jayr Alencar - jayralencarpereira@gmail.com
 * Faculdade Leão Sampaio / Clube dos Geeks - Local: Ceará - Brasil
 * Visite: www.clubedosgeeks.com.br / www.jayralencar.com.br
 * Description: Função feita para geração de boletos bancários
 * Warning: Se usar diga que foi Jayr que fez, se você não disse eu farei questão de lembrar que fui eu.
 */

class boletosPHP {
    private $ipte;
    private $barras;
    private $codBanco;
    private $codMoeda;
    private $codVerificador;
    private $dtVencimento;
    private $fatorVencimento;
    private $valorDocumento;
    private $nossoNumero;
    private $carteira;

    /* ============= SETS ============== */
    
    public function setIpte($ipte) {
        //Setando IPTE ou Linha digitável
        $this -> ipte = $ipte;
    }

    public function setBarras($barras) {
        //Setando Código de Barras
        $this -> barras = $barras;
    }

    public function setCodBanco($codBanco) {
        //Setando Código do Banco
        $this -> codBanco = substr($codBanco, 0, 3);
    }

    public function setCodMoeda($codMoeda) {
        //Setando Código do Tipo da Moeda
        $this -> codMoeda = $codMoeda;
    }

    public function setCodVerificador($codVerificador) {
        //Setando Dígito Veerificador
        $this -> codVerificador = $codVerificador;
    }

    public function setDtVencimento($dtVencimento) {
        //Setando data do vencimento
        $this -> dtVencimento = $dtVencimento;
        
        /* 
         * A data base para cauculo do fator de vencimento é 07/10/1997, 
         * O fator de Vencimento é a diferença de dias entre a data base e a data de vencimento do boleto
         */        
        $base = new DateTime("07/10/1997");
        $data = new DateTime($this -> dtVencimento);
        
        //Cauculando Diferença
        $intervalo = $base -> diff($data);

        //Setando o fator de vencimento
        $this -> setFatorVencimento($intervalo);
    }

    public function setFatorVencimento($fatorVencimento) {
        //Setando o fator de vencimento
        $this -> fatorVencimento = $fatorVencimento;
    }

    public function setValorDocumento($valorDocumento) {
        //Setando valor do documento
        $valorDocumento = str_replace(".", "", $valorDocumento);
        $this -> valorDocumento = str_replace(",", "", $valorDocumento);
    }

    public function setNossoNumero($nossoNumero) {
        //Setando Nosso Número
        $this -> nossoNumero = $nossoNumero;
    }

    public function setCarteira($carteira) {
        //Setando Número da Carteira
        $this -> carteira = $carteira;
    }

    /* ===================== GETS ========================= */
    
    public function getIpte() {
        //Pegando IPTE ou linha digitável
        if (empty($this -> ipte)) {
            $this -> ipte = $this -> calcIpte();
        }
        return $this -> ipte;
    }

    public function getBarras() {
        //Pegando Código de Barras
        if (empty($this -> barras)) {
            $this -> calcBarras();
        }
        $this -> calcVerificadorBarras($this->barras);
        return $this -> barras;
    }

    public function getCodBanco() {
        //Pegando Código do Banco
        if (empty($this -> codBanco)) {
            if (!empty($this -> barras)) {
                $this -> codBanco = substr($this -> barras, 0, 3);
            } else if (!empty($this -> ipte)) {
                $this -> codBanco = substr($this -> ipte, 0, 3);
            } else {
                return "Código do Banco não informado";
            }

        }
        return $this -> codBanco;
    }

    public function getCodMoeda() {
        //Pegando Código da Moeda
        if (empty($this -> codMoeda)) {
            if (!empty($this -> barras)) {
                $this -> codMoeda = substr($this -> barras, 3, 1);
            } else if (!empty($this -> ipte)) {
                $this -> codMoeda = substr($this -> ipte, 3, 1);
            } else {
                return "Código do moeda não informado";
            }

        }
        return $this -> codMoeda;
    }

    public function getCodVerificador() {
        //Pegando Digito Verificador
        if (empty($this -> codVerificador)) {
            if (!empty($this -> barras)) {
                $this->calcVerificadorBarras($this->barras);
                $this -> codVerificador = substr($this -> barras, 4, 1);
            } else if (!empty($this -> ipte)) {
                $this -> codVerificador = substr($this -> ipte, 38, 1);
            } else {
                return "Código Verificador não informado";
            }
        }
        return $this -> codVerificador;
    }

    public function getDtVencimento($formato = "d/m/Y") {
        //Pegando data de vencimento
        if (empty($this -> dtVencimento)) {
            if (!empty($this -> barras)) {
                $this -> calcDtVencimento(substr($this -> barras, 5, 4));
            } else if (!empty($this -> ipte)) {
                $this -> calcDtVencimento(substr($this -> ipte, 40, 4));
            } else {
                return "Data de Vencimento não informada";
            }
        }
        return date($formato, strtotime($this -> dtVencimento));
    }

    public function getFatorVencimento() {
        //Pegando fator do vencimento
        if (empty($this -> fatorVencimento)) {
            if (!empty($this -> barras)) {
                $this -> fatorVencimento = substr($this -> barras, 5, 4);
            } else if (!empty($this -> ipte)) {
                $this -> fatorVencimento = substr($this -> ipte, 40, 4);
            } else {
                return "Fator de Vencimento não informado";
            }
        }
        return $this -> fatorVencimento;
    }

    public function getValorDocumento($centavos = ",", $milhar = ".") {
        //Pegando Valor do documento
        if (empty($this -> valorDocumento)) {
            if (!empty($this -> barras)) {
                $this -> valorDocumento = substr($this -> barras, 9, 10);
            } else if (!empty($this -> ipte)) {
                $this -> valorDocumento = substr($this -> ipte, 44, 10);
            } else {
                return "Valor do Documento não informado";
            }
        }

        $valor = substr($this -> valorDocumento, 0, 8) . "." . substr($this -> valorDocumento, 8, 2);
        
        $caractere = substr($valor, 1,1);
        
        while($caractere=='0'){
            $valor = substr_replace($valor, "", 0, 1);
            $caractere = substr($valor, 1,1);
        }
        
        (double) $valor_double = $valor; 
        
        return number_format($valor_double, 2, $centavos, $milhar);
    }
    
    public function getNossoNumero(){
        //Pegando Nosso Número
        if(empty($this->nossoNumero)){
            if (!empty($this -> barras)) {
                $this -> nossoNumero = substr($this -> barras, 25, 17);
            } else if (!empty($this -> ipte)) {
                $this -> nossoNumero = str_replace(".", "", substr($ipte, 12, 11)) . str_replace(".", "", substr($ipte, 25, 9));
            } else {
                return "Nosso Número não informado";
            }
        }
        return $this->nossoNumero;
    }
    
    public function getCarteira(){
        //Pegando Carteira
        if(empty($this->carteira)){
            if (!empty($this -> barras)) {
                $this -> carteira = substr($this -> barras, 42, 2);
            } else if (!empty($this -> ipte)) {
                $this -> carteira = substr($this->ipte, 34,2);
            } else {
                return "Número da Carteira não informado";
            }
        }
        return $this->carteira;
    }
    
    //Desenhar barras
    public function desenhaBarras(){
        
        if(empty($this->barras)){
            $valor = $this->getBarras();  
        }
        
        $valor = $this->barras; 
        
        $fino = 1 ;
        $largo = 3 ;
        $altura = 50 ;
        
        $barcodes[0] = "00110" ;
        $barcodes[1] = "10001" ;
        $barcodes[2] = "01001" ;
        $barcodes[3] = "11000" ;
        $barcodes[4] = "00101" ;
        $barcodes[5] = "10100" ;
        $barcodes[6] = "01100" ;
        $barcodes[7] = "00011" ;
        $barcodes[8] = "10010" ;
        $barcodes[9] = "01010" ;
        for($f1=9;$f1>=0;$f1--){
            for($f2=9;$f2>=0;$f2--){
                $f = ($f1 * 10) + $f2 ;
                $texto = "" ;
                for($i=1;$i<6;$i++){
                    $texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
                }
                $barcodes[$f] = $texto;
            }
        }
        ?>
        <div style="overflow:hidden;width:417px;height:50px;">
            <img src="barras/img/p.gif" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img
            src="barras/img/b.gif" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img
            src="barras/img/p.gif" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img
            src="barras/img/b.gif" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img
            <?php
            $texto = $valor ;
            if((strlen($texto) % 2) <> 0){
                $texto = "0" . $texto;
            }
    
            // Draw dos dados
            while (strlen($texto) > 0) {
                $i = round($this->esquerda($texto,2));
                $texto = $this->direita($texto,strlen($texto)-2);
                $f = $barcodes[$i];
                for($i=1;$i<11;$i+=2){
                    if (substr($f,($i-1),1) == "0") {
                        $f1 = $fino ;
                    }else{
                        $f1 = $largo ;
                    }
                    ?>
                    src="barras/img/p.gif" width=<?php echo $f1?> height=<?php echo $altura?> border=0><img
                    <?php
                    if (substr($f, $i, 1) == "0") {
                        $f2 = $fino;
                    } else {
                        $f2 = $largo;
                    }
                    ?>
                    src="barras/img/b.gif" width=<?php echo $f2?> height=<?php echo $altura?> border=0><img
                    <?php
                }
            }

            // Draw guarda final
            ?>
            src="barras/img/p.gif" width=<?php echo $largo?> height=<?php echo $altura?> border=0><img
            src="barras/img/b.gif" width=<?php echo $fino?> height=<?php echo $altura?> border=0><img
            src="barras/img/p.gif" width=<?php echo 1 ?> height=<?php echo $altura?> border=0>
        </div>
        <?php
    } //Fim da fun��o

    function esquerda($entra,$comp){
        return substr($entra,0,$comp);
    }

    function direita($entra,$comp){
        return substr($entra,strlen($entra)-$comp,$comp);
    }

    /* ================  CAUCULOS  ===================*/
    
    private function calcIpte() {
        if (!empty($this -> barras)) {
            return $this -> barrasToIpte($this -> calcVerificadorBarras($this -> barras));
        } else if (!empty($this -> codBanco) && !empty($this -> codMoeda) && !empty($this -> fatorVencimento) && !empty($this -> valorDocumento)) {
            return $this -> barrasToIpte($this -> calcVerificadorBarras($this -> montaBarras()));
        } else {
            return "Não existem dados suficientes para calcular o IPTE (Você deve preencher os dados: Código do banco, código da moeda, vencimento e valor do documento, ou o código de barras)";
        }
    }

    private function calcBarras() {
        if (!empty($this -> ipte)) {
            $this -> ipteToBarras($this -> ipte);
        } else {
            $this -> montaBarras();
        }
    }

    private function calcVerificadorBarras($barras) {
        $barras = substr_replace($barras, "0", 4, 1);
        $fatores = array(4, 3, 2, 9, 0, 8, 7, 6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);

        $soma = 0;
        for ($i = 0; $i < sizeof($fatores); $i++) {
            $soma += $barras[$i] * $fatores[$i];
        }
        $mod = $soma % 11;
        $resultado = 11 - $mod;
        $this -> codVerificador = $resultado;
        $barras = substr_replace($barras, $resultado, 4, 1);

        return $barras;
    }

    private function calcDtVencimento($fator) {
        // $base = new Date("07/10/1997");
        $this -> dtVencimento = date('Y-m-d', strtotime("1997-10-07" . ' + ' . $fator . ' days'));
    }

    
    /* ================ CONVERSÕES ===================== */
    
    private function barrasToIpte($barras) {
        $campo1 = substr($barras, 0, 4) . substr($barras, 19, 1) . '.' . substr($barras, 20, 4);

        $campo2 = substr($barras, 24, 5) . '.' . substr($barras, 24 + 5, 5);

        $campo3 = substr($barras, 34, 5) . '.' . substr($barras, 34 + 5, 5);

        $campo4 = substr($barras, 4, 1);
        // Digito verificador
        $campo5 = substr($barras, 5, 14);
        // Vencimento + Valor
        //
        if ($campo5 == 0)
            $campo5 = '000';
        //
        $ipte = $campo1 . $this -> verificadorIPTE($campo1) . ' ' . $campo2 . $this -> verificadorIPTE($campo2) . ' ' . $campo3 . $this -> verificadorIPTE($campo3) . ' ' . $campo4 . ' ' . $campo5;

        // linha = campo1 + verificadorIPTE(campo1) + ' ' + campo2 + verificadorIPTE(campo2) + ' ' + campo3 + verificadorIPTE(campo3) + ' ' + campo4 + ' ' + campo5;
        //if (form.linha.value != form.linha2.value) alert('Linhas diferentes');
        return $ipte;
    }

    private function ipteToBarras($ipte) {
        $barras = substr($ipte, 0, 4) . substr($ipte, 38, 1) . substr($ipte, 40, 14) . substr($ipte, 4, 1) . substr($ipte, 6, 4) . str_replace(".", "", substr($ipte, 12, 11)) . str_replace(".", "", substr($ipte, 25, 9)) . substr($ipte, 34, 2);

        $this -> barras = $barras;
    }

    /* ========== MONTAR BARRAS ================ */
    private function montaBarras() {
        if (!empty($this -> codBanco) && !empty($this -> codMoeda) && !empty($this -> fatorVencimento) && !empty($this -> valorDocumento)) {
            $diferenca = 10 - strlen($this -> valorDocumento);

            for ($i = 0; $i < $diferenca; $i++) {
                $this -> valorDocumento = "0" . $this -> valorDocumento;
            }

            $barras = $this -> codBanco . $this -> codMoeda . "X" . $this -> fatorVencimento . $this -> valorDocumento . "000000" . $this -> nossoNumero . $this -> carteira;

            $this -> barras = $this -> calcVerificadorBarras($barras);
        } else {
            return "Não existe código de barras ainda";
        }

    }

    //Caucula veririficadores do IPTE
    private function verificadorIPTE($numero) {
        $numero = str_replace(".", "", $numero);
        $soma = 0;
        $peso = 2;
        $contador = strlen($numero) - 1;
        while ($contador >= 0) {
            $multiplicacao = substr($numero, $contador, 1) * $peso;
            if ($multiplicacao >= 10) {
                $multiplicacao = 1 + ($multiplicacao - 10);
            }
            $soma = $soma + $multiplicacao;
            if ($peso == 2) {
                $peso = 1;
            } else {
                $peso = 2;
            }
            $contador = $contador - 1;
        }
        $digito = 10 - ($soma % 10);
        if ($digito == 10)
            $digito = 0;
        return $digito;
    }

}
?>

