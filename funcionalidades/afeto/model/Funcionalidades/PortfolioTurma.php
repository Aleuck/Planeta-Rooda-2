<?php

require_once(dirname(__FILE__)."/FuncionalidadeTurma.php");
require_once(dirname(__FILE__)."/../Util/Data.php");
require_once(dirname(__FILE__)."/../../../../bd.php");
require_once(dirname(__FILE__)."/../../../../cfg.php");

class PortfolioTurma extends FuncionalidadeTurma{
//dados
	//Nome desta funcionalidade, como deve aparecer em 'funcionalidade' de acessos_planeta.
	/*String*/ protected static $NOME_FUNCIONALIDADE = "portfolio";
	
//m�todos
	/**
	* @param Usuario usuario 		Usu�rio ao qual refere-se este grupo de fatores motivacionais.
	* @param Data dataInicio		Data � partir da qual faz-se a busca.
	* @param Data dataFim			Data at� a qual faz-se a busca.
	* @param String divisaoTempo	Se deve-se retornar os dados em semanas, meses, semestres, anos... Espera uma constante definida em Data.php.
	* @return Conex�o que cont�m os dados das tabelas n�o processados.
	*/
	public function buscaModoParticipacaoUsuario($usuario, $dataInicio, $dataFim, $divisaoTempo){
		global $nivelAluno;
		global $nivelMonitor;
		global $nivelProfessor;
		$classeAtual = get_called_class();
		
		$conexao = new conexao();
		$conexao->solicitar("SELECT 0<T2.respostasColega AS respondeColega, 0<T1.respostasFormador AS respondeFormador, T1.ordem AS ordem, 
								".Data::getFuncaoBancoDeDadosPeriodo($divisaoTempo)."('".$dataInicio->paraString()."') AS ordem_inicial
							FROM (	SELECT COUNT(*) AS respostasFormador, ".Data::getFuncaoBancoDeDadosPeriodo($divisaoTempo)."(PortfolioPosts.dataCriacao) AS ordem
									FROM PortfolioProjetos JOIN PortfolioPosts ON PortfolioProjetos.owner_id = PortfolioPosts.projeto_id
									WHERE PortfolioProjetos.owner_id IN (
											SELECT codUsuario
											FROM TurmasUsuario
											WHERE codTurma = ".$this->idTurma." 
												AND (associacao = ".$nivelMonitor." OR associacao = ".$nivelProfessor.")
										)
									GROUP BY ".Data::getFuncaoBancoDeDadosPeriodo($divisaoTempo)."(PortfolioPosts.dataCriacao)
								) AS T1
								JOIN
								(	SELECT COUNT(*) AS respostasColega, ".Data::getFuncaoBancoDeDadosPeriodo($divisaoTempo)."(PortfolioPosts.dataCriacao) AS ordem
									FROM PortfolioProjetos JOIN PortfolioPosts ON PortfolioProjetos.owner_id = PortfolioPosts.projeto_id
									WHERE PortfolioProjetos.owner_id IN (
											SELECT codUsuario
											FROM TurmasUsuario
											WHERE codTurma = ".$this->idTurma." 
												AND (associacao = ".$nivelAluno.")
										)
									GROUP BY ".Data::getFuncaoBancoDeDadosPeriodo($divisaoTempo)."(PortfolioPosts.dataCriacao)
								) AS T2
								ON T1.ordem = T2.ordem
								RIGHT JOIN (SELECT numero 
											FROM Numeros 
											WHERE ".Data::getFuncaoBancoDeDadosPeriodo($divisaoTempo)."('".$dataInicio->paraString()."') <= numero 
												AND numero <= ".Data::getFuncaoBancoDeDadosPeriodo($divisaoTempo)."('".$dataFim->paraString()."')) AS T3 ON T1.ordem = T3.numero");
		return $conexao;
	}
}




?>