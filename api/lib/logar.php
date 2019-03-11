<?php

function okLogar($proxy) {
	$key       = '0c5f2d4da16b6eb4560c96fe76d4b7b2';
	$googlekey = '6Lc_7RITAAAAACuonVvZObGdSZX_w5STx8jP2l4M';
	$urlCap1   = "http://2captcha.com/in.php?key={$key}&method=userrecaptcha&googlekey={$googlekey}&pageurl=http://buscasix.com/entrar/&here=now&proxy={$proxy}";

	$headers = array();
	$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0";

	$rescap1 = curlNew($urlCap1, null, false, $headers, $proxy);

	if(stristr($rescap1, 'OK|')){
		$idCap = explode('OK|', $rescap1);
		$idCap = $idCap[1];

		if(strlen($idCap) > 4){

			for($x=0; $x <= 50; $x++){
				$urlCap2 = "http://2captcha.com/res.php?key={$key}&id={$idCap}&&action=get";
				$rescap2 = curlNew($urlCap2, null, false, $headers, $proxy);
				if($rescap2 != 'CAPCHA_NOT_READY'){
					if(stristr($rescap2, 'OK|')){
						$keycap = explode('OK|', $rescap2);
						$keycap = $keycap[1];
						return $keycap;
	                    break;
	                }
	            }else{
	                sleep(3);
	            }
	        }
    	}
    }
}

function logar($usuario, $senha, $proxy) {
	$headers = array();
	$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0";
	$url    = 'http://buscasix.com/entrar/';
	$res1   = curlNew($url, null, true, $headers, $proxy);
	$cookie = getCookies($res1);

	if(strlen($cookie) > 10) {
		$token = okLogar($proxy);
	}else{
		return "rede";
	}
	$post = "login={$usuario}&senha={$senha}&g-recaptcha-response={$token}&logar=";

	$headers = array();
	$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0";
	$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
	$headers[] = 'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3';
	$headers[] = 'Referer: http://buscasix.com/entrar/';
	$headers[] = 'Content-Type: application/x-www-form-urlencoded';
	$headers[] = "Cookie: {$cookie}";
	$headers[] = 'Dnt: 1';
	$headers[] = 'Connection: keep-alive';
	$headers[] = 'Upgrade-Insecure-Requests: 1';
	$headers[] = 'Pragma: no-cache';
	$headers[] = 'Cache-Control: no-cache';

	$res = curlNew($url, $post, true, $headers, $proxy);
	if(stristr($res, 'resolva o captcha corretamente.')) {
		return 'relogar';
	}elseif(stristr($res, 'location.href="http://buscasix.com/entrar/privadas')) {
		return $cookie;
	}else{
		return false;
		// echo "linha 68\n";
		// echo "\n\n\n\n\n";
		// echo $post;
		// echo "\n\n";
		// echo $res;
		// die;
		// return $res;
	}
}


function check($cookie, $proxy) {
	$headers   = array();
	$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0";
	$headers[] = "Cookie: {$cookie}";
	$url    = 'http://buscasix.com/entrar/privadas/';
	$res    = curlNew($url, null, true, $headers, $proxy);

	if(stristr($res, 'Privadas - BuscaSix</title>')){
		return true;
	}else{
		return false;
	}
}

function findPontosCon($res) {
	$dados = corta($res, 'transition cep detran endereco mae nome rg', '</div>');
	$dados = explode("\n", $dados);
	foreach($dados as $d){
		if(stristr($d, '/')) {
			$pontos = $d;
			break;
		}
		continue;
	}

	if(stristr($pontos, '/')) {
		$pontos = explode('/', $pontos);
		$limite = (int)$pontos[0];
		$usado  = (int)$pontos[1];
		return array(
			'limite_con' => $limite,
			'usado_con'  => $usado
		);
	}else{
		return false;
	}
}

function findPontosVec($res) {
	$dados = corta($res, 'transition cep detran endereco mae nome veiculos"', '</div>');
	$dados = explode("\n", $dados);
	foreach($dados as $d){
		if(stristr($d, '/')) {
			$pontos = $d;
			break;
		}
		continue;
	}

	if(stristr($pontos, '/')) {
		$pontos = explode('/', $pontos);
		$limite = (int)$pontos[0];
		$usado  = (int)$pontos[1];
		return array(
			'limite_vec' => $limite,
			'usado_vec'  => $usado
		);
	}else{
		return false;
	}
}

function checkPontos($cookie, $proxy) {
	$headers   = array();
	$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0";
	$headers[] = "Cookie: {$cookie}";
	$url    = 'http://buscasix.com/entrar/privadas/';
	$res    = curlNew($url, null, true, $headers, $proxy);

	if(stristr($res, 'Login - BuscaSix</title>')) {
		return 'relogar';
	}elseif(stristr($res, 'Privadas - BuscaSix</title>')) {
		$vencimento = strip_tags(corta($res, 'VENCIMENTO</small>', '</font>'));
		$vencimento = str_replace(array(' ', "\n", "\r", "\t", "	"), '', $vencimento);

		$pontos_con = findPontosCon($res);
		if($pontos_con == false){ return 'rede'; }
		$pontos_vec = findPontosVec($res);
		return array($pontos_con, $pontos_vec,'vencimento' => $vencimento );
	}else{
		return 'rede';
	}

}