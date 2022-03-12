<?php

//classe dashboard
class Dashboard
{
  public $data_inicio;
  public $data_fim;
  public $num_vendas;
  public $total_vendas;
  public $clientes_ativos;
  public $clientes_inativos;


  public function __get($atributo)
  {
    return $this->$atributo;
  }

  public function __set($atributo, $valor)
  {
    $this->$atributo = $valor;
    return $this;
  }
}

//classe conexão db
class Conexao
{
  //dados do banco
  private $host = 'localhost';
  private $dbname = 'dashboard';
  private $user = 'root';
  private $pass = '';

  public function conectar()
  {
    try {

      $conexao = new PDO(
        "mysql:host=$this->host;dbname=$this->dbname",
        "$this->user",
        "$this->pass"
      );

      //conexão com banco
      $conexao->exec('set charset utf8');
      return $conexao;
    } catch (PDOException $e) {
      echo '<p>' . $e->getMessage() . '</p>';
    }
  }
}

//classe model(trabalhando com os dados)
class Bd
{
  private $conexao;
  private $dashboard;

  public function __construct(Conexao $conexao, Dashboard $dashboard)
  {
    $this->conexao = $conexao->conectar();
    $this->dashboard = $dashboard;
  }

  public function getNumeroVendas()
  {
    $query = '
			select 
				count(*) as numero_vendas 
			from 
				tb_vendas 
			where 
				data_venda between :data_inicio and :data_fim';

    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
    $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
  }

  public function getTotalVendas()
  {
    $query = '
			select 
				SUM(total) as total_vendas 
			from 
				tb_vendas 
			where 
				data_venda between :data_inicio and :data_fim';

    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
    $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
  }

  //Clientes ativos
  public function getClientesAtivos()
  {
    $query = '
    select COUNT(cliente_ativo) as total_ativos from tb_clientes WHERE cliente_ativo = 1;
    ';

    $stmt = $this->conexao->prepare($query);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_ativos;
  }

  //Clientes inativos
  public function getClientesInativos()
  {
    $query = '
    select COUNT(cliente_ativo) as total_inativos from tb_clientes WHERE cliente_ativo = 0;
    ';

    $stmt = $this->conexao->prepare($query);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_inativos;
  }

  //Despesas
  public function getDespesas()
  {
    $query = '
    SELECT SUM(total) AS total_despesas FROM tb_despesas where data_despesa between :data_inicio and :data_fim'
    ;

    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
    $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
  }

  //Reclamação
  public function getReclamacao()
  {
    $query = '
    select COUNT(tipo_contato) as total_reclamacao from tb_contatos WHERE tipo_contato = 3
    ';

    $stmt = $this->conexao->prepare($query);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_reclamacao;
  }
}


//lógica do script
$dashboard = new Dashboard();

$conexao = new Conexao();

$competencia = explode('-', $_GET['competencia']) ;
$ano = $competencia[0];
$mes = $competencia[1]; 

$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$dashboard->__set('data_inicio', $ano .'-'. $mes . '-01');
$dashboard->__set('data_fim', $ano .'-'. $mes . '-' . $dias_do_mes);


$bd = new Bd($conexao, $dashboard);

$dashboard->__set('num_vendas', $bd->getNumeroVendas());
$dashboard->__set('total_vendas', $bd->getTotalVendas());
$dashboard->__set('total_ativos', $bd->getClientesAtivos());
$dashboard->__set('total_inativos', $bd->getClientesInativos());
$dashboard->__set('total_despesas', $bd->getDespesas());
$dashboard->__set('total_reclamacao', $bd->getReclamacao());
//print_r($dashboard);
echo json_encode($dashboard);
