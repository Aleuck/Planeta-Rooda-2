<?php
require_once("cfg.php");
require_once("bd.php");
require_once("usuarios.class.php");
class Comentario {
	// TABELA ONDE FICAM GUARDADOS OS COMENTÁRIOS -------------
	public static $tabelaBD = "";
	// --------------------------------------------------------
	protected $id = false;
	protected $idRef = false;
	protected $idUsuario = false;
	protected $mensagem = '';
	protected $data = 0;
	protected $usuario = null;
	private $salvo = false; // flag: se alguma mudança foi feita e não foi salva.
	private $consultaRef = false;

	public function getId() { return $this->id; }
	public function getIdRef() { return $this->idRef; }
	public function getIdUsuario() { return $this->idUsuario; }
	public function getMensagem() { return $this->mensagem; }
	public function getData() { return $this->data; }
	public function getUsuario() { return $this->usuario; }

	public function existe() {
		return (bool) ($this->id && $this->salvo);
	}

	public function __construct($id = false) {
		if (!(Comentario::$tabelaBD)) {
			throw new Exception('"Comentario::$tabelaBD" não está definida');
		}
		if ($id) {
			$this->abrir($id);
		}
	}
	public function abrir($id) {
		if (!is_integer($id))
			throw new Exception("Error Processing Request");
		$bd = new conexao();
		$bd->solicitar(
			"SELECT * FROM " . Comentario::$tabelaBD . " WHERE id = $id"
		);
		if ($bd->erro)
			throw new Exception("Erro na consulta: " . $bd->erro);
		if ($bd->registros !== 1)
			return;
		$this->carregaAssoc($bd->resultado);
	}
	public static function numeroComentarios($idRef) {
		$idRef = (int) $idRef;
		$bd = new conexao();
		$bd->solicitar(
			"SELECT count(1) AS num FROM " . Comentario::$tabelaBD . " WHERE idRef = $idRef"
		);
		return (int) $bd->resultado['num'];
	}
	public function abrirComentarios($idRef, $ultimoId = 0) {
		$idRef = (int) $idRef;
		$ultimoId = (int) $ultimoId;
		$bd = new conexao();
		$bd->solicitar(
			"SELECT * FROM " . Comentario::$tabelaBD . " WHERE idRef = $idRef and id > $ultimoId"
		);
		$this->salvo = false;
		if ($bd->erro)
			throw new Exception("Erro na consulta: " . $bd->erro);
		$this->consultaRef = $bd;
		if ($bd->resultado) {
			$this->carregaAssoc($bd->resultado);
		}
	}
	public function proximo() {
		if ($this->consultaRef === false)
			throw new Exception("Nenhuma referencia aberta");
		$this->consultaRef->proximo();
		$this->carregaAssoc($this->consultaRef->resultado);
	}
	public function setUsuario($idUsuario) {
		if ($this->id !== false)
			throw new Exception("Não pode mudar o usuário de uma mensagem existente.");
		if (is_object($idUsuario))
			if (get_class($idUsuario) === 'Usuario')
				$idUsuario = $idUsuario->getId();
		if (!is_integer($idUsuario)) 
			throw new Exception("Usuário inválido");
		$usuario = new Usuario();
		$usuario->openUsuario($idUsuario);
		if(!$usuario->getId()) {
			throw new Exception("Usuário Inválido");
		}
		$this->usuario = $usuario;
		$this->idUsuario = $usuario->getId();
	}
	public function setIdRef($idRef) {
		if ($this->id !== false)
			throw new Exception("Não pode mudar idRef de mensagem existente.");
		if (!is_integer($idRef))
			throw new Exception("Error Processing Request");
		$this->idRef = $idRef;
	}
	public function setMensagem($mensagem) {
		$this->mensagem = trim($mensagem);
		$this->salvo = false;
	}
	public function salvar() {
		if ($this->salvo)
			return;
		if ($this->id === false) {
			$this->data = time();
			$bd = new conexao();
			$mensagemSql = $bd->sanitizaString($this->mensagem);
			$bd->solicitar(
				"INSERT INTO " . Comentario::$tabelaBD . " (idRef, idUsuario, mensagem, data)
				VALUES ({$this->idRef}, {$this->idUsuario}, '{$mensagemSql}', {$this->data})"
			);
			if ($bd->erro)
				throw new Exception("Erro na consulta: " . $bd->erro, 1);
			$this->id = (int) $bd->ultimoId();
			$this->salvo = true;
		} else {
			$bd = new conexao();
			$mensagemSql = $bd->sanitizaString($this->mensagem);
			$bd->solicitar(
				"UPDATE " . Comentario::$tabelaBD . "
				SET mensagem = '{$mensagemSql}' WHERE id = {$this->id}"
			);
			if ($bd->erro)
				throw new Exception("Erro na consulta: " . $bd->erro, 1);
		}
	}
	private function carregaAssoc($assoc) {
		if (!is_array($assoc)) {
			$this->limpa();
		}
		$this->idRef     = isset($assoc['idRef'])     ? (int) $assoc['idRef']     : false;
		if (isset($assoc['idUsuario'])) {
			$this->setUsuario((int) $assoc['idUsuario']);
		} else {
			$this->idUsuario = false;
			$this->usuario = null;
		}
		$this->mensagem  = isset($assoc['mensagem'])  ?  trim($assoc['mensagem'])  : '';
		$this->data      = isset($assoc['data'])      ? (int) $assoc['data']      : 0;
		$this->id        = isset($assoc['id'])        ? (int) $assoc['id']        : false;
		$this->salvo     = true;
	}
	private function limpa() {
		$this->salvo = false;
		$this->id = false;
		$this->idRef = false;
		$this->idUsuario = false;
		$this->mensagem = '';
		$this->data = 0;
		$this->usuario = null;
		$this->salvo = false;
		$this->consultaRef = false;
	}
	public function getAssoc() {
		if (!$this->salvo) {
			return false;
		}
		$assoc = array();
		$assoc['id']       = $this->id;
		$assoc['mensagem'] = $this->mensagem;
		$assoc['data']     = $this->data;
		$assoc['usuario']  = $this->usuario->getSimpleAssoc();
		return $assoc;
	}
}
// Comentario::$tabelaBD = "BibliotecaComentarios";
// print_r(Comentario::$tabelaBD);