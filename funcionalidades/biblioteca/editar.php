<?
	session_start();

	require_once("biblioteca.inc.php");
	require_once("../cfg.php");		
	require_once("../db.inc.php");

	$codUsuario   = $_SESSION['SS_usuario_id'];
	$codTurma     = $_SESSION['SS_terreno_id'];
	$associacao   = "A";
	
	$images_path  = "../imagens/biblioteca/";	
	$autoriza = 1;
	$fundo    = "../imagens/figuras_fundo/naves.png";
	$corFundo = "#3366FF";
	$voltar = "../../planeta2_edicao/planeta2_guto/desenvolvimento/";
	
	$buscaTitulo   = $_POST['titulo'];
	$buscaQuem     = $_POST['quem'];
	$buscaPalavras = $_POST['palavras'];
	
	$codMaterial  = $_GET["c"];
	$tipoMaterial = $_GET["a"];
	


		?>
		<html>
<head>
<title>Biblioteca0002.png</title>
<meta http-equiv="Content-Type" content="text/html;">
<!--Fireworks MX 2004 Dreamweaver MX 2004 target.  Created Wed Apr 26 10:15:31 GMT-0300 (Hora oficial do Brasil) 2006-->
<!--Fireworks MX 2004 Dreamweaver MX 2004 target.  Created Wed Apr 26 09:53:25 GMT-0300 (Hora oficial do Brasil) 2006-->
<link href="<?=$base_loc; ?>/pngfix.css" rel="stylesheet" type="text/css">
<link href="biblioteca.css" rel="stylesheet" type="text/css">
<script src="<?=$base_loc; ?>/config/corFundo.js"></script>
<script type="text/javascript" src="../lightbox.js"></script>
</head>
<body bgcolor="<?=$corFundo; ?>">
<script>corDeFundo("<?=$cor; ?>");</script>
<div style="position:absolute; width:760; height:335; z-index:1;">
	<table width="100%" height="100%" bgcolor="<?=$corFundo; ?>">
		<TR><TD><center><img src="<?=$fundo; ?>" border="0"></center></TD></TR>
	</table>
</div>
<table border="0" cellpadding="0" cellspacing="0" width="759">
<!-- fwtable fwsrc="Biblioteca_editar.png" fwbase="Biblioteca0002.png" fwstyle="Dreamweaver" fwdocid = "1833895096" fwnested="0" -->
  <tr>
   <td><img src="<?=$images_path?>spacer.gif" width="44" height="1" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="14" height="1" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="62" height="1" border="0" alt=""></td>

   <td><img src="<?=$images_path?>spacer.gif" width="229" height="1" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="65" height="1" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="69" height="1" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="26" height="1" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="16" height="1" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="104" height="1" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="14" height="1" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="102" height="1" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="3" height="1" border="0" alt=""></td>

   <td><img src="<?=$images_path?>spacer.gif" width="11" height="1" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="1" height="1" border="0" alt=""></td>
  </tr>

  <tr>
   <td colspan="13"><img name="Biblioteca0002_r1_c1" src="<?=$images_path?>Biblioteca0002_r1_c1.png" width="759" height="13" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="1" height="13" border="0" alt=""></td>
  </tr>
  <tr>

   <td rowspan="9"><img name="Biblioteca0002_r2_c1" src="<?=$images_path?>Biblioteca0002_r2_c1.png" width="44" height="242" border="0" alt=""></td>
   <td colspan="3" rowspan="4" bgcolor="#00CCFF">
  	<form name='busca' method='post' action='index.php'>
   <?parteCima($buscaTitulo,$buscaQuem,$buscaPalavras);?>
   </form>
   </td>
   <td colspan="2"><img name="Biblioteca0002_r2_c5" src="<?=$images_path?>Biblioteca0002_r2_c5.png" width="134" height="17" border="0" alt=""></td>
   <td rowspan="3" colspan="7"><img name="Biblioteca0002_r2_c7" src="<?=$images_path?>Biblioteca0002_r2_c7.png" width="276" height="55" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="1" height="17" border="0" alt=""></td>
  </tr>
  <tr>
   <td colspan="2"><a href="#" onClick='document.busca.submit();'><img name="Biblioteca0002_r3_c5" src="<?=$images_path?>Biblioteca0002_r3_c5.png" width="134" height="27" border="0" alt=""></a></td>
   <td><img src="<?=$images_path?>spacer.gif" width="1" height="27" border="0" alt=""></td>

  </tr>
  <tr>
   <td colspan="2"><img name="Biblioteca0002_r4_c5" src="<?=$images_path?>Biblioteca0002_r4_c5.png" width="134" height="11" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="1" height="11" border="0" alt=""></td>
  </tr>
  <tr>
   <td rowspan="2" colspan="3"><img name="Biblioteca0002_r5_c5" src="<?=$images_path?>Biblioteca0002_r5_c5.png" width="160" height="40" border="0" alt=""></td>
   <td colspan="4" rowspan="3" bgcolor="#CCCCCC">
   <form name='editar' method='post' action='salvaEditar.php?t=<?=$codMaterial?>&a=<?=$tipoMaterial?>'>
   <?editar($codMaterial);?>
   </form>
   </td>
   <td rowspan="5" colspan="2"><img name="Biblioteca0002_r5_c12" src="<?=$images_path?>Biblioteca0002_r5_c12.png" width="14" height="162" border="0" alt=""></td>

   <td><img src="<?=$images_path?>spacer.gif" width="1" height="32" border="0" alt=""></td>
  </tr>
  <tr>
   <td colspan="3"><img name="Biblioteca0002_r6_c2" src="<?=$images_path?>Biblioteca0002_r6_c2.png" width="305" height="8" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="1" height="8" border="0" alt=""></td>
  </tr>
  <tr>
   <td rowspan="4"><img name="Biblioteca0002_r7_c2" src="<?=$images_path?>Biblioteca0002_r7_c2.png" width="14" height="147" border="0" alt=""></td>
   <td colspan="3" rowspan="2" bgcolor="#999999">
   <?
   listaMateriais($codTurma,$codUsuario,$buscaTitulo,$buscaQuem,$buscaPalavras,1,1,$associacao);
   ?>  
   </td>

   <td rowspan="6" colspan="2"><img name="Biblioteca0002_r7_c6" src="<?=$images_path?>Biblioteca0002_r7_c6.png" width="95" height="191" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="1" height="96" border="0" alt=""></td>
  </tr>
  <tr>
   <td rowspan="2" colspan="4"><img name="Biblioteca0002_r8_c8" src="<?=$images_path?>Biblioteca0002_r8_c8.png" width="236" height="26" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="1" height="22" border="0" alt=""></td>
  </tr>
  <tr>
   <td rowspan="2" colspan="3"><img name="Biblioteca0002_r9_c3" src="<?=$images_path?>Biblioteca0002_r9_c3.png" width="356" height="29" border="0" alt=""></td>

   <td><img src="<?=$images_path?>spacer.gif" width="1" height="4" border="0" alt=""></td>
  </tr>
  <tr>
   <td rowspan="3"><img name="Biblioteca0002_r10_c8" src="<?=$images_path?>Biblioteca0002_r10_c8.png" width="16" height="69" border="0" alt=""></td>
   <td><a href="#" onClick='document.editar.submit();'><img name="Biblioteca0002_r10_c9" src="<?=$images_path?>Biblioteca0002_r10_c9.png" width="104" height="25" border="0" alt=""></td>
   <td rowspan="3"><img name="Biblioteca0002_r10_c10" src="<?=$images_path?>Biblioteca0002_r10_c10.png" width="14" height="69" border="0" alt=""></td>
   <td rowspan="2" colspan="2"><a href='index.php'><img name="Biblioteca0002_r10_c11" src="<?=$images_path?>Biblioteca0002_r10_c11.png" width="105" height="30" border="0" alt=""></a></td>
   <td rowspan="3"><img name="Biblioteca0002_r10_c13" src="<?=$images_path?>Biblioteca0002_r10_c13.png" width="11" height="69" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="1" height="25" border="0" alt=""></td>

  </tr>
  <tr>
   <td rowspan="2" colspan="3"><a href='index.php'><img name="Biblioteca0002_r11_c1" src="<?=$images_path?>Biblioteca0002_r11_c1.png" width="120" height="44" border="0" alt=""></a></td>
   <td rowspan="2" colspan="2"><img name="Biblioteca0002_r11_c4" src="<?=$images_path?>Biblioteca0002_r11_c4.png" width="294" height="44" border="0" alt=""></td>
   <td rowspan="2"><img name="Biblioteca0002_r11_c9" src="<?=$images_path?>Biblioteca0002_r11_c9.png" width="104" height="44" border="0" alt=""></td>
   <td><img src="<?=$images_path?>spacer.gif" width="1" height="5" border="0" alt=""></td>
  </tr>
  <tr>
   <td colspan="2"><img name="Biblioteca0002_r12_c11" src="<?=$images_path?>Biblioteca0002_r12_c11.png" width="105" height="39" border="0" alt=""></td>

   <td><img src="<?=$images_path?>spacer.gif" width="1" height="39" border="0" alt=""></td>
  </tr>
</table>
</body>
</html>