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
var freteAssinatura = 0
var totalAssinatura = 0
var subtotal = 0
var subtotal1 = 0
var subtotal2 = 0
var quantity = 0
var dadosCepVar = null
var session = null
    
/**
 * carrega a sessao aou entrar na pagina index
 */
//pagamento()
dataSession()

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
        beforeSend: function() {
            $('#numCartao').attr('readonly', true)
            $('#loading').html("<img src='assets/loadmini.gif' />")
            $('.actionIcons').html('')
          },
        success: function (retorno) {
            //console.log(retorno);
            PagSeguroDirectPayment.setSessionId(retorno.id)
            $('#numCartao').attr('readonly', false)
            $('#loading').html("")
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
        amount: amount/* $("#amount").val() */,
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
    $('#valorParcelas').val('R$ ' + $('#qntParcelas').find(':selected').attr('data-parcelas').replace('.',','))
})

$('[data-add]').on("click", function(event) {
    event.preventDefault();

    var data = $(this).data()
    //console.log(data)

    var quantity1 = 0
    var quantity2 = 0

    var dadosComprar = $('#comprar').serialize()
    //console.log(dadosComprar)

    var dadosAssinar = $('#assinar').serialize()
    //console.log(dadosAssinar)

    var endereco = jQuery('.endereco').attr('data-endereco')
    //console.log(endereco)
        
    if(data.add == 'addCartComprar') {

        $.ajax({
            type: "POST",
            url: endereco + "add-cart",
            data: dadosComprar,
            dataType: 'json',
            success: function(retorna) {
                //console.log(retorna)
                $.each(retorna.products, function(i, obj) {

                    if(obj.price == 29) {

                        subtotal1 = obj.total
                        //console.log(subtotal1)
                        quantity1 = parseFloat(obj.quantity)
                        //console.log(quantity1)
                        quantity += quantity1
                        //console.log(quantity)
                    }  
                })
            },
            error: function(retorna) {
                //console.log("erro")
            },
            complete:function(retorna) {
                $("#botao-comprar-c").notify("Adicionado ao carrinho", "success")
                $('#countIconCart').html(quantity)
                subtotal = subtotal1 + subtotal2
                //console.log(subtotal)
                /* quantity = quantity1 + quantity2
                console.log(quantity) */
            }
        })  

    } else if(data.add == 'addCartAssinar') {

        $.ajax({
            type: "POST",
            url: endereco + "add-cart",
            data: dadosAssinar,
            dataType: 'json',
            success: function(retorna) {
                //console.log(retorna)
                $.each(retorna.products, function(i, obj) {

                    if(obj.price == 25) {

                        subtotal2 = obj.total
                        //console.log(subtotal2)
                        quantity2 = parseFloat(obj.quantity)
                        //console.log(quantity2)
                        quantity += quantity2
                        //console.log(quantity)
                    } 
                })
            },
            error: function(retorna) {
                //console.log("erro")
            },
            complete:function(retorna) {
                $("#botao-clube-c").notify("Adicionado ao carrinho", "success")
                $('#countIconCart').html(quantity)
                subtotal = subtotal1 + subtotal2
                //console.log(subtotal)
                /* quantity = quantity1 + quantity2
                console.log(quantity) */
            }
        }) 
    }
})

function dataSession()
{
    $('#numCartao').attr('readonly', true)
    $('#staticBackdrop').modal('toggle')
    
    var endereco = jQuery('.endereco').attr('data-endereco')
    //subtotal = 0
    //frete = 0

    $.ajax({
        type: "POST",
        url: endereco + "data-session",
        data: 'ola',
        dataType: 'json',
        success: function(retorna) {
            session = retorna
            //console.log(session)
            //console.log(quantity)

            $.each(session.products, function(i, obj) {

                //console.log(obj)

                var countCart = parseFloat(obj.quantity)
                //console.log(countCart)
                subtotal += obj.total
                quantity += countCart

                $('#countIconCart').html(quantity)
            })

            //console.log(quantity)

            if(session.totalAssinatura) {

                totalAssinatura = session.totalAssinatura.total
                freteAssinatura = session.totalAssinatura.frete

                $.each(session.products, function(i, obj) {

                    if(obj.id == 5 || obj.id == 2 || obj.id == 6) {

                        $('#tdPriceAssinatura'+obj.id).html(parseFloat(obj.price * obj.quantity).toFixed(2).replace('.', ','))
                        $('#freteAssinatura'+obj.id).html(parseFloat(freteAssinatura).toFixed(2).replace('.', ','))
                        $('#tdTotalAssinatura'+obj.id).html(parseFloat(totalAssinatura).toFixed(2).replace('.', ',') + " / mês")
                    }
                })              
            }
  
            $('#subtotalResumoCarrinho').html(parseFloat(subtotal).toFixed(2).replace('.', ','))

            if(!session.frete && session.frete.shippingCost == '') {

                $('#totalResumoCarrinho').html((subtotal).toFixed(2).replace('.', ','))

            } else {

                $('#cepConsultaDados').val(session.frete.cep)
                
                frete = parseFloat(session.frete.shippingCost.replace(',', '.'))
                amount = parseFloat(subtotal) + frete

                $('#loading').html("")

                if(retorna.frete.shippingType == 1) {
                    $('#precoEprazo').html(

                        "<div class='mb-2 mt-3'>"+
                            "<div class='custom-control custom-radio'>"+
                                "<input type='radio' name='shippingType' id='pac' class='custom-control-input' value='1' checked>"+
                                "<label class='custom-control-label' for='pac'>PAC</label><br>"+
                            "</div>"+
                            "<div class='custom-control custom-radio'>"+
                                "<input type='radio' name='shippingType' id='sedex' class='custom-control-input' value='2'>"+
                                "<label class='custom-control-label' for='sedex'>SEDEX</label><br>"+
                            "</div>"+
                            "<div class='custom-control custom-radio'>"+
                                "<input type='radio' name='shippingType' id='semfrete' class='custom-control-input' value='3' disabled>"+
                                "<label class='custom-control-label' for='semfrete'>Especial - Rio de Janeiro</label><br>"+
                            "</div>"+
                            "<div class='mt-3'>"+
                                "<input type='hidden' name='cep' id='cep' value='"+retorna.frete.cep+"'>"+
                                "<input type='text' name='shippingCost' id='shippingCost' placeholder='Preço do frete. Ex: 2.10' class='form-control' value='R$ "+retorna.frete.shippingCost+"'>"+
                            "<p class='mt-3'>Prazo de entrega em 3"+/* +prazoEntrega+ */" dias</p><"+
                            "/div>"+
                        "</div>"
                    )

                } else if(retorna.frete.shippingType == 2) {
                    $('#precoEprazo').html(

                        "<div class='mb-2 mt-3'>"+
                            "<div class='custom-control custom-radio'>"+
                                "<input type='radio' name='shippingType' id='pac' class='custom-control-input' value='1'>"+
                                "<label class='custom-control-label' for='pac'>PAC</label><br>"+
                            "</div>"+
                            "<div class='custom-control custom-radio'>"+
                                "<input type='radio' name='shippingType' id='sedex' class='custom-control-input' value='2' checked>"+
                                "<label class='custom-control-label' for='sedex'>SEDEX</label><br>"+
                            "</div>"+
                            "<div class='custom-control custom-radio'>"+
                                "<input type='radio' name='shippingType' id='semfrete' class='custom-control-input' value='3' disabled>"+
                                "<label class='custom-control-label' for='semfrete'>Especial - Rio de Janeiro</label><br>"+
                            "</div>"+
                            "<div class='mt-3'>"+
                                "<input type='hidden' name='cep' id='cep' value='"+retorna.frete.cep+"'>"+
                                "<input type='text' name='shippingCost' id='shippingCost' placeholder='Preço do frete. Ex: 2.10' class='form-control' value='R$ "+retorna.frete.shippingCost+"'>"+
                            "<p class='mt-3'>Prazo de entrega em 3"+/* +prazoEntrega+ */" dias</p><"+
                            "/div>"+
                        "</div>"
                    )

                } else if(retorna.frete.shippingType == 3) {
                    $('#precoEprazo').html(
                    
                        "<div class='mb-2 mt-3'>"+
                            "<div class='custom-control custom-radio'>"+
                                "<input type='radio' name='shippingType' id='pac' class='custom-control-input' value='1' disabled>"+
                                "<label class='custom-control-label' for='pac'>PAC</label><br>"+
                            "</div>"+
                            "<div class='custom-control custom-radio'>"+
                                "<input type='radio' name='shippingType' id='sedex' class='custom-control-input' value='2' disabled>"+
                                "<label class='custom-control-label' for='sedex'>SEDEX</label><br>"+
                            "</div>"+
                            "<div class='custom-control custom-radio'>"+
                                "<input type='radio' name='shippingType' id='semfrete' class='custom-control-input' value='3' checked>"+
                                "<label class='custom-control-label' for='semfrete'>Especial - Rio de Janeiro</label><br>"+
                            "</div>"+
                            "<div class='mt-3'>"+
                                "<input type='hidden' name='cep' id='cep' value='"+retorna.frete.cep+"'>"+
                                "<input type='text' name='shippingCost' id='shippingCost' placeholder='Preço do frete. Ex: 2.10' class='form-control' value='R$ "+retorna.frete.shippingCost+"'>"+
                            "<p class='mt-3'>Prazo de entrega em 3"+/* +prazoEntrega+ */" dias</p><"+
                            "/div>"+
                        "</div>"
                    )
                }

                $('#totalResumoCarrinho').html(parseFloat(amount).toFixed(2).replace('.', ','))
                $('#shippingType').val(retorna.frete.shippingType)
            }
        },
        complete:function(retorna) {
            $('#totalResumoCarrinho').html(parseFloat(amount).toFixed(2).replace('.', ','))

            //console.log($('#cepConsultaDados').val())
            $("input[name='shippingType']").on('click', function() {
    
                var shippingType = document.querySelector('input[name="shippingType"]:checked').value                        
                var endereco = jQuery('.endereco').attr('data-endereco')
                var dados = $('#cepConsultaDados').val()
                var dadosConsulta = "cep=" + dados + "&shippingType=" + shippingType
                //console.log(dadosConsulta)    

                $.ajax({
                    url: endereco + 'precos-e-prazos-correios-cart', //alterando para outra rota para ver se da certo
                    method:'POST',
                    dataType:'json',
                    data: dadosConsulta,
                    beforeSend: function() {
                        $('#loading').html("<img src='assets/loadmini.gif' />")
                    },
                    success:function(retorno){
                        //console.log(retorno)
                        if(retorno.Codigo == '04510') {
                            $('#loading').html('')
                            //console.log(retorno.Valor)
                            frete = retorno.Valor
                            prazoEntrega = retorno.PrazoEntrega
                            
                            $('#shippingCost').val("R$ " + retorno.Valor) 
                            $('#totalResumoCarrinho').html((parseFloat(subtotal) + parseFloat(frete)).toFixed(2).replace('.',','))
                            $('#prazoFrete').html(prazoEntrega) 
            
                        } else if(retorno.Codigo == '04014') {
                            $('#loading').html('')
                            
                            frete = retorno.Valor
                            prazoEntrega = retorno.PrazoEntrega
                                                    
                            $('#shippingCost').val("R$ " + retorno.Valor)
                            $('#totalResumoCarrinho').html((parseFloat(subtotal) + parseFloat(frete)).toFixed(2).replace('.',','))
                            $('#prazoFrete').html(prazoEntrega) 
            
                        } else if(shippingType == 3) {
            
                            frete = 8
                            prazoEntrega = 3
            
                            $('#shippingCost').val("R$ " + parseFloat(frete).toFixed(2).replace('.',','))
                            $('#totalResumoCarrinho').html((parseFloat(subtotal) + parseFloat(frete)).toFixed(2).replace('.',','))
                            $('#prazoFrete').val(prazoEntrega) 
                        }
                    }
                })
            })
        }
    })        
}

$('[data-action]').on("click", function(event) {
    event.preventDefault();

    var data = $(this).data()
    //console.log(data)
    var endereco = jQuery('.endereco').attr('data-endereco')

    var subtotalItems = 0 
    var totalItem = 0

    $.ajax({
        type: "POST",
        url: endereco + "update-products",
        data: data,
        dataType: 'json',
        success: function(retorna) {
            //console.log(retorna)

            if(retorna.action == 'deleteProduct' ) {
                $('#trItem'+retorna.id).fadeOut()
            } 

            $.each(retorna, function(i, obj) {
                    
                $("#tdQuantity" + obj.id).html(obj.quantity)

                quantity += obj.quantity

                if(obj.id == 2 || obj.id == 5 || obj.id == 6) {
                    
                    totalAssinatura = obj.total

                    $("#tdTotal" + obj.id).html(obj.total.toFixed(2).replace(".", ",") + " / mês + frete")
                    $("#tdTotal" + obj.id).val(obj.total)
                    $('#tdPriceAssinatura'+obj.id).html(totalAssinatura.toFixed(2).replace(".", ","))
                    $('#tdPriceAssinatura'+obj.id).val(totalAssinatura)
                    $('#tdTotalAssinatura'+obj.id).html((totalAssinatura + parseFloat(freteAssinatura)).toFixed(2).replace(".", ",") + " / mês")
                    $('#tdTotalAssinatura'+obj.id).val((totalAssinatura + parseFloat(freteAssinatura)).toFixed(2).replace(".", ","))
                    $('#freteAssinatura'+obj.id).html(freteAssinatura.toFixed(2).replace('.', ','))

                } else {
                    $("#tdTotal" + obj.id).html(obj.total.toFixed(2).replace(".", ","))
                    $("#tdTotal" + obj.id).val(obj.total)
                }
                    
                totalItem = obj.total
                subtotalItems += totalItem
                
            })
        },
        error: function(retorna) {
            //console.log("erro funcoes de update")
        },
        complete: function(retorna) {

            $('#subtotalResumoCarrinho').html((parseFloat(subtotalItems).toFixed(2).replace(".", ",")))
            $('#totalResumoCarrinho').html((subtotalItems + frete).toFixed(2).replace(".", ","))
            //console.log(totalItem)
        }
    })
})

$('#cepConsulta').on('click', function(event) {
    event.preventDefault()

    if($('#cepConsultaDados').val() == '') {
        $("#cepConsultaDados").notify("Preencha o CEP", "error")

    } else {
        
        var endereco = jQuery('.endereco').attr('data-endereco')
        var dados = $('#cepConsultaDados').val()

        $.ajax({
            url: 'https://viacep.com.br/ws/' + dados + '/json/',
            method: 'GET',
            data: dados,
            beforeSend: function() {
                /* $('#loading').html("<img src='assets/loadmini.gif' />") */
            },
            success: function(dadosCep) {
                //console.log(dadosCep)

                if(dadosCep.localidade = "Rio de Janeiro" && dadosCep.uf == "RJ") {  

                    $('#loading').html('')

                    $.each(session.products, function(i, obj) {

                        if(obj.id == 2 || obj.id == 5 || obj.id == 6) {

                            //console.log('retorno de: '+ obj.total)

                            freteAssinatura = 8
                            //console.log('frete da assinatura: '+ freteAssinatura)

                            subtotal2 = obj.total
                            //console.log(subtotal2)

                            totalAssinatura = freteAssinatura + obj.total
                            //console.log('total da assinatura: '+ totalAssinatura)
                            //console.log(subtotal)

                            $('#freteAssinatura'+obj.id).html(parseFloat(freteAssinatura).toFixed(2).replace('.', ','))
                            $('#tdTotalAssinatura'+obj.id).html(totalAssinatura.toFixed(2).replace('.', ',') + " / mês")
                        }    
                    })

                    frete = 8
                    prazoEntrega = 3

                    $('#precoEprazo').html("<div class='mb-2 mt-3'><div class='custom-control custom-radio'><input type='radio' name='shippingType' id='pac' class='custom-control-input' value='1' disabled><label class='custom-control-label' for='pac'>PAC</label><br></div><div class='custom-control custom-radio'><input type='radio' name='shippingType' id='sedex' class='custom-control-input' value='2' disabled><label class='custom-control-label' for='sedex'>SEDEX</label><br></div><div class='custom-control custom-radio'><input type='radio' name='shippingType' id='semfrete' class='custom-control-input' value='3' checked><label class='custom-control-label' for='semfrete'>Especial - Rio de Janeiro</label><br></div><div class='mt-3'><input type='hidden' name='cep' id='cep' value='"+dados+"'><input type='text' name='shippingCost' id='shippingCost' placeholder='Preço do frete. Ex: 2.10' class='form-control' value='R$ "+parseFloat(frete).toFixed(2).replace('.',',')+"'><p>Entrega em "+prazoEntrega+" dias</p></div></div>")

                    $('#totalResumoCarrinho').html((parseFloat(subtotal) + parseFloat(frete)).toFixed(2).replace('.',','))

                } else {

                    var dadosPac = "cep=" + dados + "&shippingType=1"
                    //console.log(dadosPac)

                    //console.log(session.products)

                    $.each(session.products, function(i, obj) {

                        if(obj.id == 2 || obj.id == 5 || obj.id == 6) {
                            //console.log('retorno de: '+ parseFloat(obj.total).toFixed(2).replace('.',','))

                            var dadosConsultaFreteAssinatura = "id="+ obj.id + "&description="+ obj.description + "&cep=" + dados + "&quantity=" + obj.quantity + "&shippingType=1"
                            //console.log(dadosConsultaFreteAssinatura)

                            $.ajax({
                                url: endereco + 'precos-e-prazos-correios-assinatura',
                                method:'POST',
                                dataType:'json',
                                data: dadosConsultaFreteAssinatura,
                                beforeSend: function() {
                                    $('#loading').html("<img src='assets/loadmini.gif' />")
                                },
                                success:function(response){
                                    //console.log("frete somente assinatura:"+response.Valor)
                                    //console.log(JSON.stringify(response))
                                    freteAssinatura = response.Valor
                                    totalAssinatura = obj.total + parseFloat(response.Valor.replace(',', '.'))
                                    subtotal2 = obj.total

                                    $('#freteAssinatura'+obj.id).html(response.Valor)
                                    $('#tdTotalAssinatura'+obj.id).html((parseFloat(response.Valor.replace(',', '.')) + parseFloat(obj.total)).toFixed(2).replace('.',',') + " / mês")
                                }
                            })
                        }    
                    })

                    $.ajax({
                        url: endereco + 'precos-e-prazos-correios-cart',
                        method:'POST',
                        dataType:'json',
                        data: dadosPac,
                        beforeSend: function() {
                            $('#loading').html("<img src='assets/loadmini.gif' />")
                        },
                        success:function(dadosCep){
                            dadosCepVar = dadosCep
                            //console.log(dadosCepVar)

                            frete = dadosCep.Valor.replace(',', '.')
                            prazoEntrega = dadosCep.PrazoEntrega
                            //console.log(subtotal)

                            $('#loading').html('')

                            $('#precoEprazo').html("<div class='mb-2 mt-3'><div class='custom-control custom-radio'><input type='radio' name='shippingType' id='pac' class='custom-control-input' value='1' checked><label class='custom-control-label' for='pac'>PAC</label><br></div><div class='custom-control custom-radio'><input type='radio' name='shippingType' id='sedex' class='custom-control-input' value='2'><label class='custom-control-label' for='sedex'>SEDEX</label><br></div><div class='custom-control custom-radio'><input type='radio' name='shippingType' id='semfrete' class='custom-control-input' value='3' disabled><label class='custom-control-label' for='semfrete'>Especial - Rio de Janeiro</label><br></div><div class='mt-3'><input type='hidden' name='cep' id='cep' value='"+dados+"'><input type='text' name='shippingCost' id='shippingCost' placeholder='Preço do frete. Ex: 2.10' class='form-control' value='R$ "+ frete.replace('.', ',') +"'><p>Entrega em <span id='prazoFrete'>"+prazoEntrega+"</span> dias</p></div></div>")

                            $('#totalResumoCarrinho').html((parseFloat(subtotal) + parseFloat(frete)).toFixed(2).replace('.',','))
                        },
                        complete:function(dadosCep){
                            //console.log(dadosCep)
                            $("input[name='shippingType']").on('click', function() {
                    
                                var shippingType = document.querySelector('input[name="shippingType"]:checked').value                        
                                var endereco = jQuery('.endereco').attr('data-endereco')
                                var dados = $('#cepConsultaDados').val()
                                var dadosConsulta = "cep=" + dados + "&shippingType=" + shippingType
                                //console.log(dadosConsulta)    
                                $.ajax({
                                    url: endereco + 'precos-e-prazos-correios-cart',
                                    method:'POST',
                                    dataType:'json',
                                    data: dadosConsulta,
                                    beforeSend: function() {
                                        $('#loading').html("<img src='assets/loadmini.gif' />")
                                    },
                                    success:function(retorno){
                                        //console.log(retorno)
                                        if(retorno.Codigo == '04510') {
                                            $('#loading').html('')
                                            //console.log(retorno.Valor)
                                            frete = retorno.Valor.replace(',', '.')
                                            prazoEntrega = retorno.PrazoEntrega
                                            
                                            $('#shippingCost').val("R$ " + retorno.Valor) 
                                            $('#totalResumoCarrinho').html((parseFloat(subtotal) + parseFloat(frete)).toFixed(2).replace('.',','))
                                            $('#prazoFrete').html(prazoEntrega)
                                            
                                            $.each(session.products, function(i, obj) {

                                                if(obj.id == 2 || obj.id == 5 || obj.id == 6) {
                        
                                                    //console.log('retorno de: '+ obj.total)
                        
                                                    freteAssinatura = retorno.Valor.replace(',', '.')
                                                    //console.log('frete da assinatura: '+ freteAssinatura)

                                                    totalAssinatura = obj.total + parseFloat(retorno.Valor.replace(',', '.'))
                                                    //console.log('total da assinatura: '+ totalAssinatura)
                                                    //console.log(subtotal)
                        
                                                    $('#freteAssinatura'+obj.id).html(parseFloat(freteAssinatura).toFixed(2).replace('.', ','))
                                                    $('#tdTotalAssinatura'+obj.id).html(totalAssinatura.toFixed(2).replace('.', ',') + " / mês")
                                                }    
                                            })
                            
                                        } else if(retorno.Codigo == '04014') {
                                            $('#loading').html('')
                                            
                                            frete = retorno.Valor.replace(',', '.')
                                            prazoEntrega = retorno.PrazoEntrega
                                                                    
                                            $('#shippingCost').val("R$ " + retorno.Valor)
                                            $('#totalResumoCarrinho').html((parseFloat(subtotal) + parseFloat(frete)).toFixed(2).replace('.',','))
                                            $('#prazoFrete').html(prazoEntrega) 

                                            $.each(session.products, function(i, obj) {

                                                if(obj.id == 2 || obj.id == 5 || obj.id == 6) {
                        
                                                    //console.log('retorno de: '+ obj.total)
                        
                                                    freteAssinatura = retorno.Valor.replace(',', '.')
                                                    //console.log('frete da assinatura: '+ freteAssinatura)
                        
                                                    totalAssinatura = obj.total + parseFloat(retorno.Valor.replace(',', '.'))
                                                    //console.log('total da assinatura: '+ totalAssinatura)
                                                    //console.log(subtotal)
                        
                                                    $('#freteAssinatura'+obj.id).html(parseFloat(freteAssinatura).toFixed(2).replace('.', ','))
                                                    $('#tdTotalAssinatura'+obj.id).html(totalAssinatura.toFixed(2).replace('.', ',') + " / mês")
                                                }    
                                            })
                            
                                        } else if(shippingType == 3) {
                            
                                            frete = 8
                                            prazoEntrega = 3
                            
                                            $('#shippingCost').val("R$ " + parseFloat(frete).toFixed(2).replace('.',','))
                                            $('#totalResumoCarrinho').html((parseFloat(subtotal) + parseFloat(frete)).toFixed(2).replace('.',','))
                                            $('#prazoFrete').val(prazoEntrega) 
                                        }
                                    }
                                })
                            })
                        }
                    })  
                }                
            }
        })
    }    
})

$('#finalizarCompra').on('click', function(event){
    event.preventDefault()

    if(!$('#cepConsultaDados').val() && !$("#shippingCost").val()) {

        $("#cepConsultaDados").notify("Preencha o CEP", "error")
    } else {


        var endereco = jQuery('.endereco').attr('data-endereco')
        var dados = $('#formPagamentoCarrinho').serialize()
        //console.log(totalAssinatura)

        $.ajax({
            type: "POST",
            url: endereco + "login-validate",
            data: dados + "&freteAssinatura="+ freteAssinatura +"&totalAssinatura=" + totalAssinatura,
            dataType: 'json',
            beforeSend: function() {
                $('#loading').html("<img src='assets/loadmini.gif' />")
              },
            success: function(retorna) {
                //console.log(JSON.stringify(retorna))

                if(!retorna.user) {

                    window.location.href=endereco+'login'

                } else {

                    window.location.href=endereco+'payment'
                    
                }
            },
            error: function(retorna) {
                //console.log('erro: '+retorna)
            },
            complete: function(retorno) {                    
                $('#totalResumoCarrinho').html((amount).toFixed(2).replace('.', ','))
                //$('#amount').val(amount).toFixed(2)
            }
        })
    }    
})

/* $('#formLogin').on("submit", function(event) {
    event.preventDefault()

    if($('#emailLogin').val() == '') {
        
        $("#emailLogin").notify("Preencha um email valido", "error")

    } else if($('#senhaLogin').val() == '') {

        $("#senhaLogin").notify("Digite uma senha valida", "error")

    } else {

        var dados = $('#formLogin').serialize()
        console.log(dados)

        var endereco = jQuery('.endereco').attr('data-endereco')
        //console.log(endereco)
            
        $.ajax({
            type: "POST",
            url: endereco + "autenticar",
            data: dados,
            dataType: 'json',
            success: function(retorna) {
                console.log(retorna)

                /* if(retorna.user) {
                    window.location.href=endereco+'payment' 
                } else {
                    window.location.href=endereco+'login' 
                } 
            },
            error: function(retorna) {
                //console.log('erro '+retorna)

            },
            complete: function(retorno) {
                           
            }
        })
    }   
}) */

$('#confirmCart').on('click', function(e) {
    e.preventDefault()

    pagamento()
    $('.actionIcons').html('')

    $('#staticBackdrop').modal('toggle')
})

$('#formCadastro').on("submit", function(event) {
    event.preventDefault()

    if(!$('#checkPolicy').is(':checked')) {
        
        $("#checkPolicy").notify("Você precisa concordar com a política de privacidade", "error")

    } else {    

        var dados = $('#formCadastro').serialize()
        //console.log(dados)

        var endereco = jQuery('.endereco').attr('data-endereco')
        //console.log(endereco)
            
        $.ajax({
            type: "POST",
            url: endereco + "registrar",
            data: dados,
            dataType: 'json',
            success: function(retorna) {
                //console.log(retorna)

                if(retorna == 1) {
                    $("#enviaDadosCadastro").notify("Você foi cadastrado com sucesso.", "success")

                } else {
                    $("#enviaDadosCadastro").notify("Você já é cadastrado.", "error")
                }
            },
            error: function(retorna) {
                //console.log("erro ao enviar o form de cadastro")
            },
            complete: function(retorno) { 
                window.location.href=endereco+'login'
                $('#emailLogin').val($('#senderEmail').val())              
            }
        })
    }   
})

/**
 * Reacuperando o token e, em seguida, o hash do cartao
 */
$('#formPagamento').on("submit", function(event) {
    event.preventDefault()
    
    var paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value

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

                amount = amount - totalAssinatura
                checkout()  
                createPlanPreApproval()
            }
        })

    } else if (paymentMethod == 'boleto') {
        checkout();

    } else if (paymentMethod == 'eft') {
        //checkout();
    }    
})

/**
 * funcao que recupera o hash do cartao do comprador
 */
function checkout()
{ 
    $('#exampleModal').modal('show')   
    PagSeguroDirectPayment.onSenderHashReady(function(retorno){
        if(retorno.status == 'error') {
            //console.log(retorno.message);
            return false;
        } else {
            $extraAmount('#hashCartao').val(retorno.senderHash) //Hash estará disponível nesta variável.
            //$('#loadPayment').modal('toggle')
            
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
                    //console.log(JSON.stringify(retorna))
                    $('#exampleModal').modal('hide')

                    if(Object.keys(retorna) == "error") {
                        $('#naoAprovadoPayment').modal('show')

                    } else if(retorna.paymentMethod.type == 2) {
                        $('#obrigadoPayment').modal('show')
                        $('#paymentLink').html("Acessar o <a href='"+retorna.paymentLink+"' target='_blank' style='color: rgb(161, 138, 83);'>boleto</a>")
                    } else {
                        $('#obrigadoPayment').modal('show')
                    }
                },
                error: function(retorna) {
                    //console.log(JSON.stringify(retorna))
                    $('#exampleModal').modal('hide')
                    $('#erroPayment').modal('show')
                },
                complete: function(retorna) {
                    $('#loading').html("")
                }
            })
        }    
    })
}

function createPlanPreApproval()
{
    var dados = parseFloat(session.totalAssinatura.total).toFixed(2) /* $('#amount').serialize() */
    //console.log(dados)

    var endereco = jQuery('.endereco').attr('data-endereco')
    //console.log(endereco)
    
    $.ajax({
        url: endereco + "create-plan",
        type: 'POST',
        data: 'amount='+dados,
        dataType: 'json',
        success: function (retorna) {
            //console.log(retorna.dados.code)

            $('#codePlan').val(retorna.dados.code)

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

            //console.log(retorno.message);
            return false;

        } else {

            $('#hashCartao').val(retorno.senderHash) //Hash estará disponível nesta variável.

            var dados = $('#formPagamento').serialize()
            //console.log(dados)

            var endereco = jQuery('.endereco').attr('data-endereco')
            //console.log(endereco)
    
            $.ajax({
                url: endereco + "subscribe",
                type: 'POST',
                data: dados,
                dataType: 'json',
                success: function (retorna) {
                    //console.log("Sucesso " + JSON.stringify(retorna))  
                },
                complete: function(retorna) {
                    
                }
            })
        }
    })        
}

function tipoPagamento(paymentMethod)
{
    $("#amount").val(parseFloat(amount).toFixed(2))
    
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
       
        $('.actionIcons').html('')
        $('#loading').html("")
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
        
        $('.actionIcons').html('')
        $('.creditCard').hide()
        $('.bankName').show()
        $('#formCreditCardOption').hide()
    }
}

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
        "href": "https://www.cafemerula.com.br/private-policy/"
      }
    })
})