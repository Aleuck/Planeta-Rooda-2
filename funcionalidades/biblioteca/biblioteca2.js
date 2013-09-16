var BIBLIOTECA = (function () {
	var ulDinamica;
	var btCarregar;
	var formEnvioMaterial;
	var formEdicaoMaterial;
	var mais_novo = 0;
	var mais_velho = 0;
	var materiais = [];
	var pode_aprovar = false;
	var pode_editar = false;
	var pode_excluir = false;
	var token_atualizador; // valor retornado por setInterval()
	var falhasSucessivas = 0; // numero de requisições que falharam
	var turma = (function () {
			var strParams = document.location.search.slice(1);
			var params = strParams.split("&");
			var param;
			var turma = 0;
			for (var i in params) {
				param = params[i].split("=");
				if (param[0] === 'turma') {
					param = parseInt(param[1], 10);
					if (param) {
						turma = param;
					}
				}
			}
			return turma;
	}());
	// definição da relaçao de ordem para chamar materiais.sort(organizaMateriais)
	function organizaMateriais(a, b) {
		return b.id - a.id;
	}
	//console.log(typeof turma + turma);
	function Material(obj) {
		this.id = obj.id;
		this.titulo = obj.titulo;
		this.tipo = obj.tipo;
		this.autor = obj.autor;
		this.usuario = obj.usuario;
		this.tags = obj.tags;
		this.data = new Date(1000 * obj.data); // javascript trabalha com milissegundos
		this.aprovado = obj.aprovado;
		this.HTMLElemento = document.createElement('li');
		if (this.tipo === 'arquivo') {
			var classes = obj.arquivo.tipo.split('/');
			classes = classes.map(function(e) { return e.split(".").join("-"); });
			this.arquivo = obj.arquivo;
			this.HTMLElemento.classList.add('arquivo');
			for (i in classes) {
				this.HTMLElemento.classList.add(classes[i]);
			}
		}
		else if (this.tipo === 'link') {
			this.link = obj.link;
			this.HTMLElemento.classList.add('link');
			// adiciona mimetype à classe do elemento (para icones de tipo de arquivo via css)
		}
		this.atualizarHTML();
	}
	Material.prototype.atualizarHTML = function() {
		if (!this.aprovado) {
			this.HTMLElemento.classList.add('nao_aprovado');
		} else {
			this.HTMLElemento.classList.remove('nao_aprovado');
		}
		this.HTMLElemento.innerHTML = '<h2>' + this.titulo + '</h2><small>Enviado por ' 
		+ this.usuario.nome + ' (' + this.data.toLocaleString()
		+ ')</small><p>Autor:' + this.autor + '</p><p><a href="abrirMaterial.php?id=' + this.id + '" target="_blank" class="abrir_material">Abrir material<span class="icon">&nbsp;</span></a></p>';
		if (pode_aprovar && !this.aprovado) {
			this.HTMLElemento.innerHTML += '<button type="button" name="aprovar" class="aprovar" value="'
			+ this.id + '">Aprovar</button>';
		}
		if (pode_editar) {
			this.HTMLElemento.innerHTML += '<button type="button" name="editar" class="editar" value="' 
			+ this.id + '">Editar</button> ';
		}
		if (pode_excluir) {
			this.HTMLElemento.innerHTML += '<button type="button" name="excluir" class="excluir" value="' 
			+ this.id + '">Excluir</button>';
		}
	};
	function ulDinamica_onclick(e) {
		e = e || event;
		var elem = e.target;
		switch (elem.name) {
			case 'aprovar':
				console.log('aprovar: ' + elem.value);
				ROODA.ui.confirm("Tem certeza que deseja aprovar este material?", function () { ajax.aproveMaterial(elem.value); });
				break;
			case 'excluir':
				console.log('excluir: ' + elem.value);
				ROODA.ui.confirm("Tem certeza que deseja excluir este material?", function () { ajax.deleteMaterial(parseInt(elem.value, 10)); });
				break;
			case 'editar':
				console.log('editar: ' + elem.value);
				break;
			default:
				break;
		}
	}
	// adicionar novo material à lista de materiais
	function addMaterial(obj) {
		// verifica se o material já está na lista
		if (materiais.filter(function (material) { return (obj.id === material.id); }).length !== 0) {
			// material ja foi adicionado
			console.log(material);
			return;
		}
		// verifica se o material herda de Material.prototype.
		if (!Material.prototype.isPrototypeOf(obj)) {
			obj = new Material(obj);
			console.log(obj);
		}
		// adiciona material à lista
		materiais.push(obj);
		// organiza lista
		materiais.sort(organizaMateriais);
		if (mais_novo < obj.id) {
			mais_novo = obj.id;
		}
		if (mais_velho === 0 || mais_velho > obj.id) {
			mais_velho = obj.id;
		}
	}
	// remove material da lista de materiais
	function removeMaterial(id) {
		materiais = materiais.filter(function (material) { return (material.id !== id); });
	}
	function aprovaMaterial(id) {
		var tmp = materiais.filter(function (material) { return (material.id === id); });
		console.log(tmp);
		if (tmp[0]) {
			tmp[0].aprovado = true;
			tmp[0].atualizarHTML();
		}
	}
	// atualiza lista de materiais (HTML) de acordo com a lista de materiais (JS)
	function atualizaLista() {
		var i;
		while (ulDinamica.firstElementChild) {
			console.log('removendo');
			ulDinamica.removeChild(ulDinamica.firstElementChild);
		}
		for (i in materiais) {
			console.log('add');
			ulDinamica.appendChild(materiais[i].HTMLElemento);
		}
	}
	// solicita novos materiais ao servidor.
	var ajax = (function () {
		var intervalToken;
		var failCount;
		// função executada quando falha a requisição de novos materiais
		var request_newer = (function () {
			function onSuccess() {
				var json;
				failCount = 0;
				try {
					json = JSON.parse(this.responseText);
				} catch (e) {
					ROODA.ui.alert("Erro no servidor.");
					console.log(e);
					console.log(this.responseText);
					return;
				}
				if (!json.session) {
					ROODA.ui.alert("Sua sessão expirou.");
					return;
				}
				pode_aprovar = json.pode_aprovar ? true : false;
				pode_editar  = json.pode_editar  ? true : false;
				pode_excluir = json.pode_excluir ? true : false;
				//console.log(json);
				if (json.materiais.length > 0) {
					json.materiais.forEach(addMaterial);
					atualizaLista();
				}
				setTimeout(request_newer, 60000);
			}
			// função que é executada quando a requisição de novos materiais é bem sucedida
			function onFail() {
				failCount += 1;
				if (failCount > 2) {
					ROODA.ui.alert("Servidor não está mais respondendo.<br>Verifique sua conexão com a internet.")
				} else {
					setTimeout(request_newer, 60000);
				}
			}
			return function () {
				AJAXGet("biblioteca.json.php?turma=" + turma + "&acao=listar&mais_novo=" + mais_novo, {
					'success': onSuccess,
					'fail': onFail
				});
			}
		}());
		var request_older = (function () {
			var waiting = false;
			function onSuccess() {
				var json;
				waiting = false;
				failCount = 0;
				try {
					json = JSON.parse(this.responseText);
				} catch (e) {
					ROODA.ui.alert("Erro no servidor.");
					console.log(e);
					console.log(this.responseText);
				}
				if (!json.session) {
					ROODA.ui.alert("Sua sessão expirou.");
					return;
				}
				//console.log(json);
				if (json.todos) {
					// sinal indicando que todos os posts mais antigos já foram carregados.
					btCarregar.disabled = true;
				}
				if (json.materiais.length > 0) {
					json.materiais.forEach(addMaterial);
					atualizaLista();
				}
			}
			function onFail_old() {
				waiting = false;
				ROODA.ui.alert("Servidor não está mais respondendo.<br>Verifique sua conexão com a internet.");
			}
			return function () {
				if (!waiting) {
					waiting = true;
					AJAXGet("biblioteca.json.php?turma=" + turma + "&acao=listar&mais_velho=" + mais_velho, {
						'success': onSuccess,
						'fail': onFail_old
					});
				}
			};
		}());
		// submitNewMaterial(formulario) : faz request de submissão de material
		var submitNewMaterial = (function() {
			function submit_success() {
				var json;
				try {
					json = JSON.parse(this.responseText);
				}
				catch (e) {
					ROODA.ui.alert("Erro na resposta do servidor.");
					console.log(e);
					console.log(this.responseText);
					return;
				}
				console.log(json);
				if (!json.session) {
					ROODA.ui.alert("Você não está logado.")
					return;
				}
				if (!json.success) {
					ROODA.ui.alert(json.errors.join("<br />\n"));
				}
				formEnvioMaterial.reset();
				toggleEnviar();
				request_newer();
			}
			return submitFormFunction(submit_success);
		}());
		var deleteMaterial= (function () {
			function req_success() {
				var res, json, id;
				res = this.responseText;
				if (!res) {
					return;
				}
				try {
					json = JSON.parse(res);
				}
				catch (e) {
					ROODA.ui.alert("Erro no servidor.");
					console.log(e);
					console.log(res);
					return;
				}
				if (json.success) {
					id = json.id;
					removeMaterial(id);
					atualizaLista();
				} else {
					if (json.errors) {
						ROODA.ui.alert("Não foi possivel excluir:<br>" + json.errors.join("<br>"));
					} else {
						ROODA.ui.alert("Não foi possivel excluir:<br>Motivo desconhecido.")
					}
				}
			}
			function req_fail() {
				ROODA.ui.alert("Não foi possivel excluir o material: o servidor não respondeu.");
			}
			return function (id) {
				AJAXGet("biblioteca.json.php?turma=" + turma + "&acao=excluir&id=" + id, {
					'success': req_success,
					'fail': req_fail
				});
			}
		}());
		var aproveMaterial= (function () {
			// handler a executar quando o request ao servidor é executado com sucesso
			function req_success() {
				var res, json, id;
				res = this.responseText;
				if (!res) {
					return;
				}
				try {
					json = JSON.parse(res);
				}
				catch (e) {
					ROODA.ui.alert("Erro no servidor.");
					console.log(e);
					console.log(res);
					return;
				}
				if (json.success) {
					id = json.id;
					aprovaMaterial(id);
				} else {
					if (json.errors) {
						ROODA.ui.alert("Não foi possivel aprovar:<br>" + json.errors.join("<br>"));
					} else {
						ROODA.ui.alert("Não foi possivel aprovar:<br>Motivo desconhecido.")
					}
				}
			}
			// handler para executar quando a requisição ao servidor falha
			function req_fail() {
				ROODA.ui.alert("Não foi possivel excluir o material: o servidor não respondeu.");
			}
			return function (id) {
				AJAXGet("biblioteca.json.php?turma=" + turma + "&acao=aprovar&id=" + id, {
					'success': req_success,
					'fail': req_fail,
				});
			}
		}());

		var scrollHandler = function () {
			if ((window.document.body.scrollHeight - document.documentElement.clientHeight - window.pageYOffset) < 20) {
				request_older();
			}
		};
		function init()
		{
			mais_novo = 0;
			mais_velho = 0;
			materiais = [];
			request_newer();
			window.addEventListener("scroll", scrollHandler);
		}
		// submitEditMaterial(formulario)
		return {
			'init' : init,
			'request_older' : request_older,
			'submitNewMaterial' : submitNewMaterial,
			'deleteMaterial' : deleteMaterial,
			'aproveMaterial' : aproveMaterial
		};
	}());
	function init() { 
		ulDinamica = document.getElementById("ul_materiais");
		btCarregar = document.getElementById("bt_carregar_mais");
		formEnvioMaterial = document.getElementById("form_envio_material");
		formEdicaoMaterial = document.getElementById("form_");
		btCarregar.addEventListener('click', ajax.request_older);
		ulDinamica.addEventListener('click', ulDinamica_onclick);
		formEnvioMaterial.onsubmit = function () {
			var that = this;
			setTimeout(function () { ajax.submitNewMaterial(that); }, 5);
			return false;
		};
		ajax.init();
	}
	return { 'init' : init, form: formEnvioMaterial };
}());