$(function() {
    function notifica(titulo, mensagem, tipo) {
        if (tipo == 'success') {
            toastr.success(mensagem, titulo);
        } else if (tipo == 'info') {
            toastr.info(mensagem, titulo);
        } else if (tipo == 'warning') {
            toastr.warning(mensagem, titulo);
        } else if (tipo == 'error') {
            toastr.error(mensagem, titulo);
        } else {
            toastr.success(mensagem, titulo);
        }
    
        toastr.options = {
            'closeButton': true,
            'debug': false,
            'newestOnTop': true,
            'progressBar': true,
            'positionClass': 'toast-top-right',
            'preventDuplicates': false,
            'onclick': null,
            'showDuration': '300',
            'hideDuration': '1000',
            'timeOut': '3000',
            'extendedTimeOut': '1000',
            'showEasing': 'swing',
            'hideEasing': 'linear',
            'showMethod': 'fadeIn',
            'hideMethod': 'fadeOut'
        }
    }
    $("#endereco_cep").mask("99.999-999");
    $("#cliente_cpf").mask("999.999.999-99");

    $('form').floatlabelform();

    $('#btn-cep').click(function() {
        var dInput = $('#endereco_cep').val().length;
        
        var cep = $('#endereco_cep').val().replace(/\D/g, '');
        if (dInput == 10) {
            var validacep = /^[0-9]{8}$/;
            if (validacep.test(cep)) {

                $('#endereco').show(200);
                $('#endereco_rua').val('...');
                $('#endereco_bairro').val('...');
                $('#endereco_cidade').val('...');

                $.getJSON('//viacep.com.br/ws/' + cep + '/json/?callback=?', function(dados) {
                    if (!('erro' in dados)) {
                        $('#endereco_rua').val(dados.logradouro);
                        $('#endereco_bairro').val(dados.bairro);
                        $('#endereco_cidade').val(dados.localidade);
                        $('#endereco_uf').val(dados.uf);
                        $('#endereco_n').focus();

                        if ($('#endereco_uf').val() != '') {
                            $('#endereco_uf').removeClass('campo-erro');
                            $('#endereco_uf').parent().next('span').hide();
                        }

                        if ($('#endereco_cidade').val() != '') {
                            $('#endereco_cidade').removeClass('campo-erro');
                            $('#endereco_cidade').parent().next('span').hide();
                        }

                        if ($('#endereco_bairro').val() != '') {
                            $('#endereco_bairro').removeClass('campo-erro');
                            $('#endereco_bairro').parent().next('span').hide();
                        }

                        if ($('#endereco_rua').val() != '') {
                            $('#endereco_rua').removeClass('campo-erro');
                            $('#endereco_rua').parent().next('span').hide();
                        }

                    } else {
                        limpa_formulario_cep();
                    }
                });
            } else {
                limpa_formulario_cep();
                toastr.error('Erro', 'Formato de CEP inválido.');
            }
        }
    });

    $('#endereco_cep').change(function() {
        var dInput = this.value.length;
        
        var cep = $(this).val().replace(/\D/g, '');
        if (dInput == 10) {
            var validacep = /^[0-9]{8}$/;
            if (validacep.test(cep)) {

                $('#endereco').show(200);
                $('#endereco_rua').val('...');
                $('#endereco_bairro').val('...');
                $('#endereco_cidade').val('...');

                $.getJSON('//viacep.com.br/ws/' + cep + '/json/?callback=?', function(dados) {
                    if (!('erro' in dados)) {
                        $('#endereco_rua').val(dados.logradouro);
                        $('#endereco_bairro').val(dados.bairro);
                        $('#endereco_cidade').val(dados.localidade);
                        $('#endereco_uf').val(dados.uf);
                        $('#endereco_n').focus();

                        if ($('#endereco_uf').val() != '') {
                            $('#endereco_uf').removeClass('campo-erro');
                            $('#endereco_uf').parent().next('span').hide();
                        }

                        if ($('#endereco_cidade').val() != '') {
                            $('#endereco_cidade').removeClass('campo-erro');
                            $('#endereco_cidade').parent().next('span').hide();
                        }

                        if ($('#endereco_bairro').val() != '') {
                            $('#endereco_bairro').removeClass('campo-erro');
                            $('#endereco_bairro').parent().next('span').hide();
                        }

                        if ($('#endereco_rua').val() != '') {
                            $('#endereco_rua').removeClass('campo-erro');
                            $('#endereco_rua').parent().next('span').hide();
                        }

                    } else {
                        limpa_formulario_cep();
                    }
                });
            } else {
                limpa_formulario_cep();
                toastr.error('Erro', 'Formato de CEP inválido.');
            }
        }
    });

    $('#endereco_uf').change(function() {
        var _this = $(this);

        if (_this.val() != '') {
            _this.removeClass('campo-erro');
            _this.parent().next('span').hide();
        }
    });

    $('#endereco_cidade').change(function() {
        var _this = $(this);

        if (_this.val() != '') {
            _this.removeClass('campo-erro');
            _this.parent().next('span').hide();
        }
    });

    $('#endereco_bairro').change(function() {
        var _this = $(this);

        if (_this.val() != '') {
            _this.removeClass('campo-erro');
            _this.parent().next('span').hide();
        }
    });

    $('#endereco_n').change(function() {
        var _this = $(this);

        if (_this.val() != '') {
            _this.removeClass('campo-erro');
            _this.parent().next('span').hide();
        }
    });

    $('#endereco_rua').change(function() {
        var _this = $(this);

        if (_this.val() != '') {
            _this.removeClass('campo-erro');
            _this.parent().next('span').hide();
        }
    });

    $('#endereco_cep').change(function() {
        var _this = $(this);

        if (_this.val() != '') {
            _this.removeClass('campo-erro');
            _this.parent().next('span').hide();
        }
    });

    $('#celular').change(function() {
        var _this = $(this);

        if (_this.val() != '') {
            _this.removeClass('campo-erro');
            _this.parent().next('span').hide();
        }
    });

    $('#cliente_nome').change(function() {
        var _this = $(this);

        if (_this.val().length > 1) {
            _this.removeClass('campo-erro');
            _this.parent().next('span').hide();
        }
    });

    $('#cliente_cpf').change(function() {
        var _this = $(this);

        if (TestaCPF(_this.val())) {
            _this.removeClass('campo-erro');
            _this.parent().next('span').hide();
        }
    });

    $('#cliente_email').change(function() {
        var _this = $(this);
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

        if (emailReg.test(_this.val())) {
            _this.removeClass('campo-erro');
            _this.parent().next('span').hide();
        }
    });

    $('.btn-dados').click(function() {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

        var valido = true;
        var campoNome = $('#cliente_nome');
        var campoCPF = $('#cliente_cpf');
        var campoEmail = $('#cliente_email');
        var campoTelefone = $('#celular');
        var campoCEP = $('#endereco_cep');
        var campoEnd = $('#endereco_rua');
        var campoNumero = $('#endereco_n');
        var campoBairro = $('#endereco_bairro');
        var campoCidade = $('#endereco_cidade');
        var campoEstado = $('#endereco_uf');
        var arrNome = campoNome.val().split(' ');
        var campoGoTo = '';

        if (!emailReg.test(campoEmail.val()) || campoEmail.val() == '') {
            campoEmail.addClass('campo-erro');
            campoEmail.parent().next('span').css('display', 'block').text('Esse email não e válido. Verifique se você digitou corretamente.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#cliente_email';
        } else {
            campoEmail.removeClass('campo-erro');
            campoEmail.parent().next('span').hide();
        }

        if (arrNome.length < 2) {
            campoNome.addClass('campo-erro');
            campoNome.parent().next('span').css('display', 'block').text('Preencha corretamente o campo com seu Nome Completo.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#cliente_nome';
        } else {
            campoNome.removeClass('campo-erro');
            campoNome.parent().next('span').hide();
        }

        if (campoTelefone.val() == '') {
            campoTelefone.addClass('campo-erro');
            campoTelefone.parent().next('span').css('display', 'block').text('Digite um telefone válido.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#celular';
        } else {
            campoTelefone.removeClass('campo-erro');
            campoTelefone.parent().next('span').hide();
        }

        if (TestaCPF(campoCPF.val())) {
            campoCPF.removeClass('campo-erro');
            campoCPF.parent().next('span').hide();
        } else {
            campoCPF.addClass('campo-erro');
            campoCPF.parent().next('span').css('display', 'block').text('Esse CPF não é válido. Verifique se você digitou corretamente.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#cliente_cpf';
        }

        if (campoCEP.val() == '') {
            campoCEP.addClass('campo-erro');
            campoCEP.parent().next('span').css('display', 'block').text('Digite um CEP válido.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#endereco_cep';
        } else {
            campoCEP.removeClass('campo-erro');
            campoCEP.parent().next('span').hide();
        }

        if (campoEnd.val() == '') {
            campoEnd.addClass('campo-erro');
            campoEnd.parent().next('span').css('display', 'block').text('Preencha corretamente o campo Endereço.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#endereco_rua';
        } else {
            campoEnd.removeClass('campo-erro');
            campoEnd.parent().next('span').hide();
        }

        if (campoNumero.val() == '') {
            campoNumero.addClass('campo-erro');
            campoNumero.parent().next('span').css('display', 'block').text('Preencha corretamente o campo Número.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#endereco_n';
        } else {
            campoNumero.removeClass('campo-erro');
            campoNumero.parent().next('span').hide();
        }

        if (campoBairro.val() == '') {
            campoBairro.addClass('campo-erro');
            campoBairro.parent().next('span').css('display', 'block').text('Preencha corretamente o campo Bairro.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#endereco_bairro';
        } else {
            campoBairro.removeClass('campo-erro');
            campoBairro.parent().next('span').hide();
        }

        if (campoCidade.val() == '') {
            campoCidade.addClass('campo-erro');
            campoCidade.parent().next('span').css('display', 'block').text('Preencha corretamente o campo Cidade.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#endereco_cidade';
        } else {
            campoCidade.removeClass('campo-erro');
            campoCidade.parent().next('span').hide();
        }

        if (campoEstado.val() == '') {
            campoEstado.addClass('campo-erro');
            campoEstado.parent().next('span').css('display', 'block').text('Selecione corretamente o campo Estado.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#endereco_uf';
        } else {
            campoEstado.removeClass('campo-erro');
            campoEstado.parent().next('span').hide();
        }

        if (valido) {
            $('#frmDados').submit();
        } else {
            $('html, body').animate({
                scrollTop: $(campoGoTo).offset().top
            }, 500);
        }

        return false;
    });

    $('.pagamento-tabs a').click(function() {
        var _this = $(this);
        var id = _this.data('id');

        $('.pagamento-tabs a').removeClass('ativo');
        _this.addClass('ativo');

        $('#tipo').val(id);
        $('.pagamento-conteudo').hide();
        $('#' + id).show();
    });

    $('.botao-tab').click(function() {
        var id = $(this).data('id');

        $('#tipo').val(id);
        $('.pagamento-conteudo').hide();
        $('#' + id).show();
    });

    $("#cardNumber").change(function() {
        var _this = $(this);
        var valor = _this.val();

        if (valor == '')
            $('#bandeira').html('');
    });

    $("#cardNumber").keyup(function() {
        var _this = $(this);
        var valor = _this.val();

        if (valor == '') {
            valor = '0000 0000 0000 0000';

            $('#bandeira').html('');
        }

        $('.card-imagem-num').text(cc_format(valor));
    });

    $("#cartao_venc").keyup(function() {
        var _this = $(this);
        var valor = _this.val();

        if (valor == '')
            valor = '00/0000';

        arr = valor.split('/');

        if (arr[0] != undefined)
            $('#cardExpirationMonth').val(arr[0]);

        if (arr[1] != undefined)
            $('#cardExpirationYear').val(arr[1]);

        $('.card-imagem-data').text(valor);
    });

    $("#securityCode").keyup(function() {
        var _this = $(this);
        var valor = _this.val();

        if (valor == '')
            valor = '000';

        $('.card-imagem-cvv').text(valor);

        $('#card-frente')
            .css('backface-visibility', 'hidden')
            .css('left', '0px')
            .css('position', 'absolute')
            .css('top', '0px')
            .css('transform', 'rotateY(180deg)')
            .css('transform-style', 'preserve-3d')
            .css('width', '100%')
            .css('z-index', '2')
            .css('transition', 'all 0.9s ease 0s');

        $('#card-cvv')
            .css('backface-visibility', 'hidden')
            .css('left', '0px')
            .css('position', 'absolute')
            .css('top', '0px')
            .css('transform', 'rotateY(0deg)')
            .css('transform-style', 'preserve-3d')
            .css('width', '100%')
            .css('z-index', '2')
            .css('transition', 'all 0.9s ease 0s');
    });

    $("#securityCode").focusout(function() {
        $('#card-frente')
            .css('backface-visibility', 'hidden')
            .css('left', '0px')
            .css('position', 'absolute')
            .css('top', '0px')
            .css('transform', 'rotateY(0deg)')
            .css('transform-style', 'preserve-3d')
            .css('width', '100%')
            .css('z-index', '2')
            .css('transition', 'all 0.9s ease 0s');

        $('#card-cvv')
            .css('backface-visibility', 'hidden')
            .css('left', '0px')
            .css('position', 'absolute')
            .css('top', '0px')
            .css('transform', 'rotateY(-180deg)')
            .css('transform-style', 'preserve-3d')
            .css('width', '100%')
            .css('z-index', '2')
            .css('transition', 'all 0.9s ease 0s');
    });

    $('.finalizar-cartao').click(function() {
        var valido = true;
        var cardholderName = $('#cardholderName');
        var docNumber = $('#docNumber');
        var cardNumber = $('#cardNumber');
        var cartao_venc = $('#cartao_venc');
        var securityCode = $('#securityCode');
        var parcelas = $('#installments');
        var campoGoTo = '';

        if (cardholderName.val() == '') {
            cardholderName.addClass('campo-erro');
            cardholderName.parent().next('span').css('display', 'block').text('Preencha corretamente o campo Nome completo.');
            valido = false;
            campoGoTo = '#cardholderName';
        } else {
            cardholderName.removeClass('campo-erro');
            cardholderName.parent().next('span').hide();
        }

        if (!TestaCPF(docNumber.val())) {
            docNumber.addClass('campo-erro');
            docNumber.parent().next('span').css('display', 'block').text('Preencha corretamente o campo CPF.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#docNumber';
        } else {
            docNumber.removeClass('campo-erro');
            docNumber.parent().next('span').hide();
        }

        if (cardNumber.val() == '') {
            cardNumber.addClass('campo-erro');
            cardNumber.parent().next('span').css('display', 'block').text('Preencha corretamente o Número do Cartão.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#cardNumber';
        } else {
            cardNumber.removeClass('campo-erro');
            cardNumber.parent().next('span').hide();
        }

        if (cartao_venc.val() == '') {
            cartao_venc.addClass('campo-erro');
            cartao_venc.parent().next('span').css('display', 'block').text('Preencha corretamente a Data de Expiração.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#cartao_venc';
        } else {
            cartao_venc.removeClass('campo-erro');
            cartao_venc.parent().next('span').hide();
        }

        if (securityCode.val() == '') {
            securityCode.addClass('campo-erro');
            securityCode.parent().next('span').css('display', 'block').text('Preencha corretamente o Código de Segurança.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#securityCode';
        } else {
            securityCode.removeClass('campo-erro');
            securityCode.parent().next('span').hide();
        }

        if (parcelas.val() == 0) {
            parcelas.addClass('campo-erro');
            parcelas.next('span').css('display', 'block').text('Selecione a parcela.');
            valido = false;
            if (campoGoTo == '')
                campoGoTo = '#installments';
        } else {
            parcelas.removeClass('campo-erro');
            parcelas.next('span').hide();
        }

        if (valido) {
            var $form = document.querySelector('#frmPagar');
            Mercadopago.createToken($form, sdkResponseHandler);

            return false;
        } else {
            $('html, body').animate({
                scrollTop: $(campoGoTo).offset().top
            }, 500);
        }
    });

    $('.finalizar-boleto').click(function() {
        frmcallback();

        return false;
    });

    $('.boleto-copy').tooltip({
        placement: "bottom",
        title: 'Copiado',
        trigger: "click"
    });
});

function limpa_formulario_cep() {
    $('#endereco_rua').val('');
    $('#endereco_bairro').val('');
    $('#endereco_cidade').val('');
    $('#endereco_uf').val('');
}

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}

function formatar(mascara, documento) {
    var i = documento.value.length;
    var saida = mascara.substring(0, 1);
    var texto = mascara.substring(i)

    if (texto.substring(0, 1) != saida)
        documento.value += texto.substring(0, 1);
}

function mascara(o, f) {
    v_obj = o
    v_fun = f
    setTimeout("execmascara()", 1)
}

function execmascara() {
    v_obj.value = v_fun(v_obj.value)
}

function mtel(v) {
    v = v.replace(/\D/g, "");
    v = v.replace(/^(\d{2})(\d)/g, "($1) $2");
    v = v.replace(/(\d)(\d{4})$/, "$1-$2");
    return v;
}

function mdata(v) {
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d{2})(\d)/, "$1/$2");
    v = v.replace(/(\d{2})(\d)/, "$1/$2");

    v = v.replace(/(\d{2})(\d{2})$/, "$1$2");
    return v;
}

function mvenc(v) {
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d{2})(\d)/, "$1/$2");
    return v;
}

function testa_navegador(event) {
    if (document.all)
        tecla = event.keyCode;
    else
        tecla = event.which;
    return tecla;
}

function so_numeros(event) {
    var tecla = testa_navegador(event);
    if ((tecla >= 48) && (tecla <= 57))
        return true;
    else if (tecla == 8 || tecla == 0)
        return true;
    return false;
}

function notifica(titulo, mensagem, tipo) {
    if (tipo == 'success') {
        toastr.success(mensagem, titulo);
    } else if (tipo == 'info') {
        toastr.info(mensagem, titulo);
    } else if (tipo == 'warning') {
        toastr.warning(mensagem, titulo);
    } else if (tipo == 'error') {
        toastr.error(mensagem, titulo);
    } else {
        toastr.success(mensagem, titulo);
    }

    toastr.options = {
        'closeButton': true,
        'debug': false,
        'newestOnTop': true,
        'progressBar': true,
        'positionClass': 'toast-top-right',
        'preventDuplicates': false,
        'onclick': null,
        'showDuration': '300',
        'hideDuration': '1000',
        'timeOut': '3000',
        'extendedTimeOut': '1000',
        'showEasing': 'swing',
        'hideEasing': 'linear',
        'showMethod': 'fadeIn',
        'hideMethod': 'fadeOut'
    }
}

function TestaCPF(strCPF) {
    var Soma;
    var Resto;
    
    strCPF = strCPF.replace(/[^\d]+/g,'');

    Soma = 0;
    if (strCPF == "00000000000")
        return false;

    for (Somai = 1; Somai <= 9; Somai++)
        Soma = Soma + parseInt(strCPF.substring(Somai - 1, Somai)) * (11 - Somai);

    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11))
        Resto = 0;

    if (Resto != parseInt(strCPF.substring(9, 10)))
        return false;

    Soma = 0;
    for (Somai = 1; Somai <= 10; Somai++)
        Soma = Soma + parseInt(strCPF.substring(Somai - 1, Somai)) * (12 - Somai);

    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11))
        Resto = 0;

    if (Resto != parseInt(strCPF.substring(10, 11)))
        return false;

    return true;
}

Mercadopago.setPublishableKey(window.mp_key);
Mercadopago.getIdentificationTypes();

function addEvent(el, eventName, handler) {
    if (el.addEventListener) {
        el.addEventListener(eventName, handler);
    } else {
        el.attachEvent('on' + eventName, function() {
            handler.call(el);
        });
    }
};

function getBin() {
    var ccNumber = document.querySelector('input[data-checkout="cardNumber"]');
    return ccNumber.value.replace(/[ .-]/g, '').slice(0, 6);
};

function guessingPaymentMethod(event) {
    var bin = getBin();

    if (event.type == "keyup") {
        if (bin.length >= 6) {
            Mercadopago.getPaymentMethod({
                "bin": bin
            }, setPaymentMethodInfo);
        }
    } else {
        setTimeout(function() {
            if (bin.length >= 6) {
                Mercadopago.getPaymentMethod({
                    "bin": bin
                }, setPaymentMethodInfo);
            }
        }, 100);
    }
};

function setPaymentMethodInfo(status, response) {
    if (status == 200) {
        var form = document.querySelector('#frmPagar');

        if (document.querySelector("input[name=paymentMethodId]") == null) {
            var paymentMethod = document.createElement('input');
            paymentMethod.setAttribute('name', "paymentMethodId");
            paymentMethod.setAttribute('type', "hidden");
            paymentMethod.setAttribute('value', response[0].id);
            form.appendChild(paymentMethod);

        } else {
            document.querySelector("input[name=paymentMethodId]").value = response[0].id;
        }

        var img = "<img src='" + response[0].thumbnail + "' align='center' style='margin-left:10px;' ' >";
        $("#bandeira").empty();
        $("#bandeira").append(img);

        amount = document.querySelector('#amount').value;
        Mercadopago.getInstallments({
            "bin": getBin(),
            "amount": amount
        }, setInstallmentInfo);
    } else {
        notifica('Erro', 'payment method info error:', `${response}`);
    }
};

addEvent(document.querySelector('input[data-checkout="cardNumber"]'), 'keyup', guessingPaymentMethod);
addEvent(document.querySelector('input[data-checkout="cardNumber"]'), 'change', guessingPaymentMethod);

function sdkResponseHandler(status, response) {

    if (status != 200 && status != 201) {

        console.log(status);
        console.log(response);

        notifica('Erro', 'Verifique as informações do Cartão', 'error');
    } else {
        var form = document.querySelector('#frmPagar');
        var card = document.createElement('input');
        card.setAttribute('name', "token");
        card.setAttribute('type', "hidden");
        card.setAttribute('value', response.id);
        form.appendChild(card);

        frmcallback();
    }
};

function formatReal(int) {
    var tmp = int + '';
    tmp = tmp.replace(/([0-9]{2})$/g, ",$1");
    if (tmp.length > 6)
        tmp = tmp.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");

    return tmp;
}

function setInstallmentInfo(status, response) {
    var selectorInstallments = document.querySelector("#installments"),
        fragment = document.createDocumentFragment();

    selectorInstallments.options.length = 0;

    if (response.length > 0) {
        var option = new Option("Selecione...", '-1'),
            payerCosts = response[0].payer_costs;

        fragment.appendChild(option);
        for (var vari = 0; vari < payerCosts.length; vari++) {

            var valor = Number(payerCosts[vari].installment_amount);
            var parcelas = payerCosts[vari].installments;
            var mensagem = payerCosts[vari].installments + 'x de R$ ' + valor.toFixed(2).replace(".", ",") + ' (R$ ' + (valor * parcelas).toFixed(2).replace(".", ",") + ')';

            option = new Option(mensagem, payerCosts[vari].installments);
            fragment.appendChild(option);
        }

        selectorInstallments.appendChild(fragment);
        selectorInstallments.removeAttribute('disabled');

        $("#installments option:eq(1)").attr("selected", "selected");

        var valor = $("#installments option:selected").text();
        var retorno = valor.split('(');

        if (retorno.length > 0) {
            valor = retorno[1];
            valor = valor.replace('R$ ', '');
            valor = valor.replace(',', '.');
            valor = valor.replace(')', '');
        }

        $("#total_parcelas").val(valor);
    }
};

function cc_format(value) {
    var valor = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '')
    var matches = valor.match(/\d{4,16}/g);
    var match = matches && matches[0] || ''
    var parts = []

    for (vari = 0, len = match.length; vari < len; vari += 4) {
        parts.push(match.substring(vari, vari + 4))
    }

    if (parts.length) {
        return parts.join(' ')
    } else {
        return value
    }
}

window.envioPedido = true;

function frmcallback() {
    var _this = $(this);

    if (!window.envioPedido)
        return false;

    var botaoTexto = $('.botao-fechar').html();
    $('.botao-fechar').html('Carregando...');

    window.envioPedido = false;

    $.ajax({
        url: 'callback.php',
        type: 'post',
        data: $('#frmPagar').serialize(),
        dataType: 'json',
        success: function(resp) {
            if (resp) {
                if (resp.msg) {
                    if (resp.tipo == 'erro') {
                        notifica('Erro', resp.msg, 'error');
                    } else {
                        notifica('Sucesso', resp.msg, 'success');
                    }
                }

                if (resp.redirect)
                    window.location = resp.redirect;
            }

            $('.botao-fechar').html(botaoTexto);

            window.envioPedido = true;
        }
    });
}

