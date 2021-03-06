<?php

require_once(dirname(__FILE__)."/../model/Subjetividade/Subjetividade.php");
require_once(dirname(__FILE__)."/../../../usuarios.class.php");
require_once(dirname(__FILE__)."/../../../turma.class.php");
require_once(dirname(__FILE__)."/../model/Util/Data.php");

$nomeUsuario_post	 = $_POST['nomeUsuario'];
$dataInicio_post	 = $_POST['dataInicio'];
$dataFim_post		 = $_POST['dataFim'];
$turma_post			 = $_POST['turma'];

$usuario = Usuario::buscaPorNome($nomeUsuario_post);
$usuario = $usuario[0];

$turma = new Turma();
$turma->openTurma($turma_post);

$dataInicio = new Data($dataInicio_post);
$dataFim = new Data($dataFim_post);
$subjetividade = new Subjetividade($dataInicio, $dataFim, $usuario, $turma, Data::SEMANA);

$numeroIntervalosTempo = Data::getNumeroIntervalosTempoEntre($dataInicio, $dataFim, Data::SEMANA);
?>