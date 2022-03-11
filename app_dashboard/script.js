$(document).ready(() => {
  
	$('#documentacao').on('click', ()=>{
    $('#pagina').load('documentacao.html')
  })

  $('#suporte').on('click', ()=>{
    $('#pagina').load('suporte.html')
  })

  //ajax
  $('#competencia').on('change', e =>{

    let competencia = $(e.target).val()
    

    $.ajax({
      type: 'GET',
      url: 'app.php',
      data: `competencia=${competencia}`,
      dataType: 'json',
      success: dados => {
        $('#numeroVendas').html(dados.num_vendas)
        $('#totalVendas').html(dados.total_vendas)
        $('#clientesAtivos').html(dados.total_ativos)
        $('#clientesInativos').html(dados.total_inativos)
        $('#despesa').html(dados.total_despesas)

        console.log(dados)
      },
      error: erro => {console.log(erro)},
    })
  })

})