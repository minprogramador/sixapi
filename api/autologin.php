<?php

if(date('H') >= 23) {
	if(date('H') == 23) {
		if(date('i') >= 59) {
			die;
		}
	}else{
		die;
	}
}elseif(date('H') < 8) {
	die;
}


require('config/contas.php');
require('lib/util.php');
require('lib/logar.php');
require('lib/veiculo.php');
require('lib/veiculoCpf.php');
require('lib/veiculoCnpj.php');

foreach($contas as $cc) {
	//echo "iniciando...\n\n";

	$usuario = $cc['usuario'];
	$senha   = $cc['senha'];
	$proxy   = $cc['proxy'];
	$dados   = ler($usuario);

	if(is_array($dados)) {

		if($dados['usado_con'] >= $dados['limite_con']) {
			$err1 = true;
		}

		if($dados['usado_vec'] >= $dados['limite_vec']) {
			$err2 = true;
		}

		if(isset($err1) && isset($err2)) {

			$dia = $dados['update'];
			$dia = explode("-", $dia);
			$dia = $dia[2];
			$dia = explode(" ", $dia);
			$dia = $dia[0];
			$hoje = date('d');
			if($hoje > $dia) {
				saveCookie('', $usuario);
				saveStatus(1, $usuario);
				//echo 'verificar';				
			} else {
				//echo "salta, pontos esgotados\n\n";
				unset($dia);
				unset($hoje);
				unset($err1);
				unset($err2);
				continue;
			}
		}
	}
	//echo 'na 48........';
	if($dados === false) {

		save(
			array(
				"$usuario" => array(
					'usuario' => $usuario,
					'senha'   => $senha,
					'proxy'   => $proxy,
					'cookie'  => '',
					'limite_con' => 30,
					'usado_con'  => 0,
					'limite_vec' => 30,
					'usado_vec'  => 0,
					'status'  => true
				)
			));
		$dados = ler($usuario);
	}

	if(isset($dados[$usuario]['cookie'])) {
		$cookie  = $dados['cookie'];
		$status  = $dados['status'];
		$update  = $dados['update'];
		$limite_vec = $dados['limite_vec'];
		$usado_vec  = $dados['usado_vec'];

		$limite_con = $dados['limite_con'];
		$usado_con  = $dados['usado_con'];

		if(strlen($dados['senha']) < 1) {
			$dados[$usuario]['usuario'] = $usuario;
			$dados[$usuario]['senha']   = $senha;
			$dados[$usuario]['proxy']   = $proxy;
			save($dados, $usuario);
		}
	}else{
		$cookie = '';
	}

	if(strlen($cookie) < 10) {
		saveStatus(3, $usuario);
		$cookie = logar($usuario, $senha, $proxy);
		if($cookie == 'relogar') {
			//echo "{$usuario} - nao resolveu..\n";
			$cookie = logar($usuario, $senha, $proxy);		
		}

		if($cookie == 'rede') {
			//echo "{$usuario} - trocar proxy\n";
		}elseif($cookie == 'relogar') {
			//echo "{$usuario} - relogar";
		}elseif(strlen($cookie) > 15) {
			//echo "{$usuario} - okkk\n";
			saveCookie($cookie, $usuario);
		}else{
			//echo "{$usuario} - nao deu em nada..\n";
		}
		saveStatus(1, $usuario);
	}

	$v = checkPontos($cookie, $proxy);

	if($v == 'relogar') {
		$tudook = 'relogar';
	}elseif($v == 'rede') {
		$tudook = 'rede';
	}elseif(is_array($v)) {

		$usado_con  = (int)$v[0]['usado_con'];
		$limite_con = (int)$v[0]['limite_con'];
		$usado_vec  = (int)$v[1]['usado_vec'];
		$limite_vec = (int)$v[1]['limite_vec'];

		saveLimites($limite_con, $usado_con, $limite_vec, $usado_vec, $usuario);
		
		if($usado_con >= $limite_con) {
			$er = true;
			$tudook = 'limite_con';
			saveStatus(5, $usuario, 'con');
		}

		if($usado_vec >= $limite_vec) {
			$er = true;
			$tudook = 'limite_vec';
			saveStatus(5, $usuario, 'vec');
		}

		if(!isset($er)) {
			$tudook = true;
		}
	}else{
		$tudook = 'relogar';
	}

	if($tudook === true){
		saveStatus(1, $usuario);
		//echo "{$usuario} - tudo okkkkk";
	}elseif($tudook == 'rede') {

		//echo "{$usuario} - rede ruim.";

	}elseif($tudook == 'relogar') {

		saveStatus(3, $usuario);
		$cookie = logar($usuario, $senha, $proxy);
		if($cookie == 'relogar') {
		//	echo "{$usuario} - nao resolveu..\n";
			$cookie = logar($usuario, $senha, $proxy);		
		}

		if($cookie == 'rede') {
		//	echo "{$usuario} - trocar proxy\n";
		}elseif($cookie == 'relogar') {
		//	echo "{$usuario} - relogar";
			saveCookie('', $usuario);
		}elseif(strlen($cookie) > 15) {
		//	echo "{$usuario} - okkk\n";
			saveCookie($cookie, $usuario);
		}else{
		//	echo "{$usuario} - nao deu em nada..\n";
		}
		saveStatus(1, $usuario);
	}else{

		//echo "{$usuario} - erro indefinido";

	}

}

