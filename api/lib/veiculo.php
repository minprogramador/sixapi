<?php

function consultaPlaca($placa, $cookie, $proxy) {

	$headers = array();
	$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0";
	$headers[] = 'Accept: */*';
	$headers[] = 'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3';
	$headers[] = 'Referer: http://buscasix.com/entrar/modulo/buscar/';
	$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
	$headers[] = 'X-Requested-With: XMLHttpRequest';
	$headers[] = 'Dnt: 1';
	$headers[] = 'Connection: keep-alive';
	$headers[] = 'Cookie: '.$cookie;
	$headers[] = 'Pragma: no-cache';
	$headers[] = 'Cache-Control: no-cache';
	$url    = 'http://buscasix.com/entrar/modulo/buscar/';
	$post = "chassiplaca={$placa}&consultar=consultar";
	$res    = curlNew($url, $post, true, $headers, $proxy);

	if(stristr($res, 'location.href="http://buscasix.com/entrar/";')) {
		return 'relogar';
	}elseif(stristr($res, 'uve um problema em obter o resultado para esta consult')){
		return 'nada_encontrado';
	}elseif(strlen($res) < 50) {
		return 'reload';
	}elseif(stristr($res, '200 OK')) {
		if(stristr($res, 'tro do Veiculo</td>')) {
			return $res;
		}else{
			return false;
		}
	}else{
		return false;
	}
}

