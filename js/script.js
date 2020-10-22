$('.bankName').hide()
$('.creditCard').hide()
$('#shippingAddressPostalCode').hide().val($('#billingAddressPostalCode').val())
$('#shippingAddressStreet').hide().val($('#billingAddressStreet').val())
$('#shippingAddressNumber').hide().val($('#billingAddressNumber').val())
$('#shippingAddressComplement').hide().val($('#billingAddressComplement').val())
$('#shippingAddressDistrict').hide().val($('#billingAddressDistrict').val())
$('#shippingAddressCity').hide().val($('#billingAddressCity').val())
$('#shippingAddressState').hide().val($('#billingAddressState').val())
$('#shippingAddressCountry').hide().val($('#billingAddressCountry').val())


/**
 * seta o valor utilizado na transacao em todas as funcoes
 */
var amount = 0
var frete = 0

    
/**
 * carrega a sessao aou entrar na pagina index
 */
pagamento()

/**
 * Inicia a sessao de pagamento obrigatoria
 */
function pagamento()
{
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
            //listarMeiosPag()
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
        
        amount: $("#amount").val(),
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
 * Preenche parte do endereco pelo Cep
 */
$('#billingAddressPostalCode').on('keyup', function(){
    
    var cep = $(this).val()
    var caracteres = cep.length

    if(caracteres >= 8) {
        
        $.ajax({
            url: 'https://viacep.com.br/ws/' + cep + '/json/',
            method: 'GET',
            data: cep,
            success: function(dados) {
                $('#billingAddressStreet').val(dados.logradouro)
                $('#billingAddressDistrict').val(dados.bairro)
                $('#billingAddressCity').val(dados.localidade)
                $('#billingAddressState').val(dados.uf)
                $('#billingAddressCountry').val('BRA')
                $('#shippingAddressPostalCode').val($('#billingAddressPostalCode').val())
                $('#shippingAddressStreet').val($('#billingAddressStreet').val())
                $('#shippingAddressDistrict').val($('#billingAddressDistrict').val())
                $('#shippingAddressCity').val($('#billingAddressCity').val())
                $('#shippingAddressState').val($('#billingAddressState').val())
                $('#shippingAddressCountry').val($('#billingAddressCountry').val())  
            },
            complete: function(retorno) {
                //tratamento comum para todas chamadas
                $('#billingAddressNumber').on('blur', function() {

                    $('#shippingAddressNumber').val($('#billingAddressNumber').val())
                })
                
                $('#billingAddressComplement').on('blur', function() {
                
                    $('#shippingAddressComplement').val($('#billingAddressComplement').val());    
                })
            }
        })
    }
})

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
        amount: $("#amount").val(),
        maxInstallmentNoInterest: noIntInstalQuantity,
        brand: bandeira,
        success: function(retorno){

            //duas verificacoes do array
             $.each(retorno.installments, function(ia, obja) {
                $.each(obja, function(ib, objb) {

                    //formatacao de valor para padrao brl
                    var valorParcela = objb.installmentAmount.toFixed(2).replace(".", ",")

                    // duas casas decimais apos o ponto
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

    var paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value
    //console.log(paymentMethod);

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
                //console.log(retorno.card.token)
            },
            error: function(retorno) {
                    // Callback para chamadas que falharam.
            },
            complete: function(retorno) {
                // callback para o token do cartao
                checkout()  
                //createPlanPreApproval()
                    

            }
        })

    } else if (paymentMethod == 'boleto') {
        checkout();

    } else if (paymentMethod == 'eft') {
        checkout();
    }    
})

/**
 * funcao que recupera o hash do cartao do comprador
 */
function checkout()
{
    PagSeguroDirectPayment.onSenderHashReady(function(retorno){
        if(retorno.status == 'error') {
            console.log(retorno.message);
            return false;
        } else {
            $('#hashCartao').val(retorno.senderHash) //Hash estará disponível nesta variável.
            
            var dados = $('#formPagamento').serialize()
            //console.log(dados)

            var endereco = jQuery('.endereco').attr('data-endereco')
            //console.log(endereco)
            
            $.ajax({
                type: "POST",
                url: endereco + "checkout",
                data: dados,
                dataType: 'json',
                success: function(retorna) {
                    console.log("Sucesso " + JSON.stringify(retorna))
                    $("#msg").html('<p style="color: green">Aguardando confirmação da operadora.</p>')
                },
                error: function(retorna) {
                    console.log("erro")
                    $("#msg").html('<p style="color: #FF0000">Erro ao realizar a transação</p>')
                }
            })
        }    
    });
}

function createPlanPreApproval()
{
    var dados = $('#amount').serialize()
    //console.log(dados)

    var endereco = jQuery('.endereco').attr('data-endereco')
    //console.log(endereco)
    
    $.ajax({
        url: endereco + "create-plan",
        type: 'POST',
        data: dados,
        dataType: 'json',
        success: function (retorna) {
            //console.log("Sucesso " + JSON.stringify(retorna))
            $.each(retorna, function(i, obj) {
                //console.log(obj.code)
                $('#codePlan').val(obj.code)    
            })
        },

        complete: function(retorna) {
           subscribe()
        }
    })        
}

function subscribe()
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
            //console.log(endereco)
    
            $.ajax({
                url: endereco + "subscribe",
                type: 'POST',
                data: dados,
                dataType: 'json',
                success: function (retorna) {
                    console.log("Sucesso " + JSON.stringify(retorna))  
                },

                complete: function(retorna) {
                    
                }
            })
        }
    })        
}

function tipoPagamento(paymentMethod)
{
    if(paymentMethod == "creditCard"){

        $(function() {
            
            $('#formCreditCardOption').show()
            $('.bankName').hide()
            
            $('#shippingAdressOther').on('click', function() {
        
                if($('#shippingAdressOther').prop('checked') == true) {
        
                    $('.creditCard').show() 
                    $('#formShippingAdressOption').show()
                    $('#shippingAddressPostalCode').show().val('')
                    $('#shippingAddressStreet').show().val('')
                    $('#shippingAddressNumber').show().val('')
                    $('#shippingAddressComplement').show().val('')
                    $('#shippingAddressDistrict').show().val('')
                    $('#shippingAddressCity').show().val('')
                    $('#shippingAddressState').show().val('')
                    $('#shippingAddressCountry').show().val('')

                    $('#shippingAddressPostalCode').on('keyup', function(){
    
                        var cep = $(this).val()
                        var caracteres = cep.length
                    
                        if(caracteres >= 8) {
                            
                            $.ajax({
                                url: 'https://viacep.com.br/ws/' + cep + '/json/',  
                                method: 'GET',
                                data: cep,
                                success: function(dados) {
                                    $('#shippingAddressStreet').val(dados.logradouro)
                                    $('#shippingAddressDistrict').val(dados.bairro)
                                    $('#shippingAddressCity').val(dados.localidade)
                                    $('#shippingAddressState').val(dados.uf)
                                    $('#shippingAddressCountry').val('BRA')
                                }
                            })
                        }
                    })

                } else if($('#shippingAdressOther').prop('checked') == false) {
        
                    $('.creditCard').hide() 
                    $('#formShippingAdressOption').hide()
                    $('#shippingAddressPostalCode').hide().val($('#billingAddressPostalCode').val())
                    $('#shippingAddressStreet').hide().val($('#billingAddressStreet').val())
                    $('#shippingAddressNumber').hide().val($('#billingAddressNumber').val())
                    $('#shippingAddressComplement').hide().val($('#billingAddressComplement').val())
                    $('#shippingAddressDistrict').hide().val($('#billingAddressDistrict').val())
                    $('#shippingAddressCity').hide().val($('#billingAddressCity').val())
                    $('#shippingAddressState').hide().val($('#billingAddressState').val())
                    $('#shippingAddressCountry').hide().val($('#billingAddressCountry').val()) 
                } 
            })
        
        })
    }

    if(paymentMethod == "boleto"){
        $('.creditCard').hide()
        $('#formCreditCardOption').hide()
        $('.bankName').hide()

        $(function() {
            
            $('#shippingAdressOther').on('click', function() {
        
                if($('#shippingAdressOther').prop('checked') == true) {
        
                  $('.creditCard').show() 
                  $('#formShippingAdressOption').show()
                  $('#shippingAddressPostalCode').show().val('')
                  $('#shippingAddressStreet').show().val('')
                  $('#shippingAddressNumber').show().val('')
                  $('#shippingAddressComplement').show().val('')
                  $('#shippingAddressDistrict').show().val('')
                  $('#shippingAddressCity').show().val('')
                  $('#shippingAddressState').show().val('')
                  $('#shippingAddressCountry').show().val('')
                  
                  $('#shippingAddressPostalCode').on('keyup', function(){
    
                        var cep = $(this).val()
                        var caracteres = cep.length
                    
                        if(caracteres >= 8) {
                            
                            $.ajax({
                                url: 'https://viacep.com.br/ws/' + cep + '/json/',  
                                method: 'GET',
                                data: cep,
                                success: function(dados) {
                                    $('#shippingAddressStreet').val(dados.logradouro)
                                    $('#shippingAddressDistrict').val(dados.bairro)
                                    $('#shippingAddressCity').val(dados.localidade)
                                    $('#shippingAddressState').val(dados.uf)
                                    $('#shippingAddressCountry').val('BRA')
                                }
                            })
                        }
                    })
        
                } else if($('#shippingAdressOther').prop('checked') == false) {
        
                  $('.creditCard').hide() 
                  $('#formShippingAdressOption').hide() 
                  $('#shippingAddressPostalCode').hide().val($('#billingAddressPostalCode').val())
                  $('#shippingAddressStreet').hide().val($('#billingAddressStreet').val())
                  $('#shippingAddressNumber').hide().val($('#billingAddressNumber').val())
                  $('#shippingAddressComplement').hide().val($('#billingAddressComplement').val())
                  $('#shippingAddressDistrict').hide().val($('#billingAddressDistrict').val())
                  $('#shippingAddressCity').hide().val($('#billingAddressCity').val())
                  $('#shippingAddressState').hide().val($('#billingAddressState').val())
                  $('#shippingAddressCountry').hide().val($('#billingAddressCountry').val())
                } 
            })
        
        })
    }
    
    if(paymentMethod == "eft"){
        $('.creditCard').hide()
        $('.bankName').show()
        $('#formCreditCardOption').hide()
    }
}

/**
 * recupera o preco e prazo do frete
 */
$('input[name="shippingType"]').on('click', function() {
    
    var shippingType = document.querySelector('input[name="shippingType"]:checked').value

    var dados = $('#formPagamento').serialize()
    //console.log(dados)

    var endereco = jQuery('.endereco').attr('data-endereco')

    if(shippingType != 3) {
        
        $.ajax({
            url: endereco + 'precos-e-prazos-correios',
            method:'post',
            dataType:'json',
            data: dados,
            success:function(dados){
                var prazo = dados.PrazoEntrega
                var shippingCost = dados.Valor.replace(',','.')

                if($('#shippingAddressCity').val() == 'Rio de Janeiro') {
                    shippingCost = 8
                    prazo = 3    
                }

                var amount = (parseFloat(shippingCost) + parseFloat($('#review').val())).toFixed(2)

                //console.log(dados)
                $('#shippingCost').val(parseFloat(shippingCost).toFixed(2))
                $('#amount').val(amount)

                if($('#shippingAddressCity').val() == 'Rio de Janeiro') {

                    $('#subtotal').html("<h4>Subtotal: " + parseFloat(amount).toFixed(2).replace('.', ',') + "</h4><br><h5>Prazo para a entrega de " + prazo + " dias úteis</h5>")    
                }
                $('#subtotal').html("<h4>Subtotal: " + parseFloat(amount).toFixed(2).replace('.', ',') + "</h4><br><h5>Prazo para a entrega de " + prazo + " dias úteis</h5>")
                 
            },
            error:function(dados){
                //console.log(dados)
                alert('Cep não encontrado. Tente Novamente');
            },
            complete: function(dados){
                listarMeiosPag()
            }
        });
    }

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
    })
});    