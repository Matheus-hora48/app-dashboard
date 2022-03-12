$(document).ready(() => {
  
  //reload da pagina inicial
  $('#principal').on('click', () => {
    $('#pagina').load('pr.html')
  })


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
        $('#reclamacao').html(dados.total_reclamacao)
        $('#elogios').html(dados.total_elogios)
        $('#sugestao').html(dados.total_sugestoes)
        

        console.log(dados)
      },
      error: erro => {console.log(erro)},
    })
  })

})