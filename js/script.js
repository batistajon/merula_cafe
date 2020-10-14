/**
 * seta o valor utilizado na transacao em todas as funcoes
 */
var amount = $('#amount').val();
//var amount = "600.00"

/**
 * carrega a sessao aou entrar na pagina index
 */
pagamento()

/**
 * Inicia a sessao de pagamento obrigatoria
 */
function pagamento()
{
    $('.bankName').hide();
    $('.creditCard').hide();

    var endereco = jQuery('.endereco').attr('data-endereco');
    //console.log(endereco)
    $.ajax({
        url: endereco + "pagamento",
        type: 'POST',
        dataType: 'json',
        success: function (retorno) {
            console.log(retorno);
            PagSeguroDirectPayment.setSessionId(retorno.id)
        },

        complete: function(retorno) {
            listarMeiosPag()
            //console.log(retorno)        
        }
    });
}

/**
 * Lista os meios de pagamento disponiveis na api
 */
function listarMeiosPag()
{
    PagSeguroDirectPayment.getPaymentMethods({
        amount: amount,
        success: function(retorno) {
            //console.log(retorno)

            /* recupera imagem bandeiras cartao de credito */
            $('.meio-pag').append("<div>Credit card</div>")

            $.each(retorno.paymentMethods.CREDIT_CARD.options, function(i, obj) {
                //$('.meio-pag').append('<span>'+ obj.name +'</span>') //imprime o nome da bandeira do cartao
                $('.meio-pag').append("<span class='img-band'><img src='https://stc.pagseguro.uol.com.br"+ obj.images.SMALL.path +"'></span>")
            })

            /* recupera imagem bandeiras */
            $('.meio-pag').append("<div>Boleto</div>")

            $('.meio-pag').append("<span class='img-band'><img src='https://stc.pagseguro.uol.com.br" + retorno.paymentMethods.BOLETO.options.BOLETO.images.SMALL.path + "'></span>")

            /* recupera imagem bandeiras cartao de debito */
            $('.meio-pag').append("<div>Debit card</div>")

            $.each(retorno.paymentMethods.ONLINE_DEBIT.options, function(i, obj){
                
                $('.meio-pag').append("<span class='img-band'><img src='https://stc.pagseguro.uol.com.br"+ obj.images.SMALL.path +"'></span>")
                $('#bankName').show().append("<option value='" + obj.name + "'>" + obj.displayName + "</option>");
                $('.bankName').hide();
            })
        },
        error: function(retorno) {
            // Callback para chamadas que falharam.
        },
        complete: function(retorno) {
            // Callback para todas chamadas.
            //recupTokenCartao()
        }
    });    
}

/**
 * Recupera a bandeira do cartao digitado no formulario
 */
$('#numCartao').on('keyup', function(){

    var numCartao = $(this).val()
    var qntNumero = numCartao.length

    //console.log(numCartao) //verifica o retorno do keyup no console
    
    if(qntNumero >= 6) {

        PagSeguroDirectPayment.getBrand({

            cardBin: numCartao,
            success: function(retorno) {

                //console.log(retorno) //retorno ok
                //envia para o index o retorno da bandeira
                var imgBand = retorno.brand.name;
                $('.bandeira-cartao').html("<img src='https://stc.pagseguro.uol.com.br/public/img/payment-methods-flags/42x20/"+ imgBand +".png'>");

                $('#bandeiraCartao').val(imgBand)

                recupParcelas(imgBand)
                $('#msg').empty()
            },
            error: function(retorno) {
                
                $('#msg').html("Cartão inválido")
            },
            complete: function(retorno) {
                //tratamento comum para todas chamadas
            }
        });

    } else if(qntNumero < 6) {

        $('#msg').empty()
        $('.bandeira-cartao').empty()
    }       
})

/**
 * Lista a quantidade de parcelas 
 */
function recupParcelas(bandeira)
{
    var noIntInstalQuantity = $('#noIntInstalQuantity').val()
    $('#qntParcelas').html('<option value="">Selecione</option>')

    PagSeguroDirectPayment.getInstallments({
        amount: amount,
        maxInstallmentNoInterest: noIntInstalQuantity,
        brand: bandeira,
        success: function(retorno){

            //duas verificacoes do array
            $.each(retorno.installments, function(ia, obja) {
                $.each(obja, function(ib, objb) {

                    //formatacao de valor para padrao brl
                    var valorParcela = objb.installmentAmount.toFixed(2).replace(".", ",")

                    /* duas casas decimais apos o ponto */
                    var valorParcelaDouble = objb.installmentAmount.toFixed(2)

                    $('#qntParcelas').show().append("<option value='"+ objb.quantity +"' data-parcelas='"+ valorParcelaDouble +"'>"+ objb.quantity +" parcelas de R$ "+ valorParcela +"</option>")
                })
            })
       },
        error: function(retorno) {
            // callback para chamadas que falharam.
       },
        complete: function(retorno){
            // Callback para todas chamadas.
       }
    });
}

/**
 * enviar o valor da parcela para o formulario
 */
$('#qntParcelas').change(function() {
    $('#valorParcelas').val($('#qntParcelas').find(':selected').attr('data-parcelas'))
})

/**
 * Reacuperando o token e, em seguida, o hash do cartao
 */
$('#formPagamento').on("submit", function(event) {
    event.preventDefault();

    var paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
    console.log(paymentMethod);

    //recupera o token do cartao ao clicar do botao
    if(paymentMethod == 'creditCard') {
        
        PagSeguroDirectPayment.createCardToken({

            cardNumber: $('#numCartao').val(), // Número do cartão de crédito
            brand: $('#bandeiraCartao').val(), // Bandeira do cartão
            cvv: $('#cvvCartao').val(), // CVV do cartão
            expirationMonth: $('#mesValidade').val(), // Mês da expiração do cartão
            expirationYear: $('#anoValidade').val(), // Ano da expiração do cartão, é necessário os 4 dígitos.

            success: function(retorno) {
                $('#tokenCartao').val(retorno.card.token)
                console.log(retorno)
            },
            error: function(retorno) {
                    // Callback para chamadas que falharam.
            },
            complete: function(retorno) {
                // callback para o token do cartao
                recupHashCartao()
                
            }
        }); 

    } else if (paymentMethod == 'boleto') {
        recupHashCartao();

    } else if (paymentMethod == 'eft') {
        recupHashCartao();
    }    
})

/**
 * funcao que recupera o hash do cartao do comprador
 */
function recupHashCartao()
{
    PagSeguroDirectPayment.onSenderHashReady(function(retorno){
        if(retorno.status == 'error') {
            console.log(retorno.message);
            return false;
        } else {
            $('#hashCartao').val(retorno.senderHash) //Hash estará disponível nesta variável.

            var dados = $('#formPagamento').serialize()
            console.log(dados)

            var endereco = jQuery('.endereco').attr('data-endereco')
            console.log(endereco)

            $.ajax({
                method: "POST",
                url: endereco + "checkout",
                data: dados,
                dataType: 'json',
                success: function(retorna) {
                    console.log("Sucesso " + JSON.stringify(retorna))
                    $("#msg").html('<p style="color: green">Transação realizada com sucesso</p>')
                },
                error: function(retorna) {
                    console.log("erro")
                    $("#msg").html('<p style="color: #FF0000">Erro ao realizar a transação</p>')
                }
            })
        }    
    });
}

function tipoPagamento(paymentMethod){
    if(paymentMethod == "creditCard"){
        $('.creditCard').show();
        $('.bankName').hide();
    }
    if(paymentMethod == "boleto"){
        $('.creditCard').hide();
        $('.bankName').hide();
    }
    if(paymentMethod == "eft"){
        $('.creditCard').hide();
        $('.bankName').show();
    }
}
 
//Busca do CEP
$('#Form1').on('submit',function(event){
    event.preventDefault();
    var Dados=$(this).serialize();
    var Cep=$('#Cep').val();

    $.ajax({
        url: 'https://viacep.com.br/ws/'+Cep+'/json/',
        method:'get',
        dataType:'json',
        data: Dados,
        success:function(Dados){
            $('.ResultadoCep').html('').append('<div>'+Dados.logradouro+','+Dados.bairro+'-'+Dados.localidade+'-'+Dados.uf+'</div>');
            console.log(Dados)
        },
        error:function(Dados){
            alert('Cep não encontrado. Tente Novamente');
            $('#Cep').val('');
        }
    });
});

//Busca preco e prazo
$('#Form2').on('submit',function(event){
    event.preventDefault();
    var Dados=$(this).serialize();

    $.ajax({
        url: 'ControllerCorreios.php',
        method:'post',
        dataType:'html',
        data: Dados,
        success:function(Dados){
            $('.ResultadoPrecoPrazo').html(Dados)
            console.log(Dados)
        },
        error:function(Dados){
            console.log(Dados)
            alert('Cep não encontrado. Tente Novamente');
            $('#Cep').val('');
        }
    });
})

//estilo nav bar
$(window).scroll(function() {    
    var scroll = $(window).scrollTop();
      if (scroll >= 40) {               // se rolar 40px ativa o evento
        $("#menu").addClass("ativo");    //coloca a classe "ativo" no id=menu
      } else {
        $("#menu").removeClass("ativo"); //se for menor que 40px retira a classe "ativo" do id=menu
      }
    });

//LGPD
window.addEventListener("load", function(){
    window.cookieconsent.initialise({
      "palette": {
        "popup": {
          "background": "#C0C0C0",
          "text": "#000"
        },
        "button": {
          "background": "#000"
        }
      },
      "content": {
        "message": "Este site usa cookies para garantir que você obtenha a melhor experiência em nosso site.",
        "dismiss": "Aceito!",
        "href": "https://www.cafemerula.com.br/politica-de-cookies/"
      }
    })});    