<?php
	if($mensagens['message']['chat']['type'] == 'group' OR $mensagens['message']['chat']['type'] == 'supergroup'){
		$chatID = (string)$mensagens['message']['chat']['id'];

		if(isset($dadosRanking[$chatID])){
			if(empty($dadosRanking[$chatID][$mensagens['message']['from']['id']]['qntd_mensagem'])){
							 $dadosRanking[$chatID][$mensagens['message']['from']['id']]['qntd_mensagem'] = 0;
			}

			$dadosRanking[$chatID][$mensagens['message']['from']['id']]['primeiro_nome'] = $mensagens['message']['from']['first_name'];
			$dadosRanking[$chatID][$mensagens['message']['from']['id']]['qntd_mensagem'] = $dadosRanking[$chatID][$mensagens['message']['from']['id']]['qntd_mensagem'] + 1;
		}
		else{
			$dadosRanking[$chatID][$mensagens['message']['from']['id']]['primeiro_nome'] = $mensagens['message']['from']['first_name'];
			$dadosRanking[$chatID][$mensagens['message']['from']['id']]['qntd_mensagem'] = 1;
		}
	}

	if($mensagens['message']['chat']['type'] == 'supergroup'){
		$dadosRanking['SG'] = $mensagens['message']['message_id'];
	}
	else{
		$dadosRanking['PG'] = $mensagens['message']['message_id'];
	}

	if(	strcasecmp($mensagens['message']['text'], '/rkgdel')																					== 0	OR
			strcasecmp($mensagens['message']['text'], '/rkgdel' . '@' . $dadosBot['result']['username'])	== 0	){
		$dadosIdioma = carregarDados(RAIZ . 'dados/idioma.json');

		if(isset($dadosIdioma[$mensagens['message']['chat']['id']]['idioma'])){
			$idioma = $dadosIdioma[$mensagens['message']['chat']['id']]['idioma'];

			if($mensagens['message']['chat']['type'] == 'group' OR $mensagens['message']['chat']['type'] == 'supergroup'){
					 $rkgdel = false;
				$resultado = getChatAdministrators($chatID);

				if($resultado['ok'] == true){
					foreach($resultado['result'] as $admins){
						if($admins['user']['id'] == $mensagens['message']['from']['id'] AND $admins['status'] == 'creator'){
							unset($dadosRanking[$chatID]);

								$rkgdel = true;
							$mensagem	= '<b>O.K!</b>';

							break;
						}
					}
				}

				if($rkgdel == false){
					$mensagem = RANKING[$idioma]['SMT_CRIADOR'];
				}
			}
			else if($mensagens['message']['chat']['type'] == 'private'){
				$mensagem = ERROS[$idioma]['SMT_GRUPO'];
			}

			sendMessage($mensagens['message']['chat']['id'], $mensagem, $mensagens['message']['message_id'], null, true);
		}
	}

	++$mensagensMinuto;

	$total = microtime(true) - $inicio;

	if($total > 59){
		$dadosRanking['MM'] = $mensagensMinuto;

		$mensagensMinuto = 0;
						 $inicio = microtime(true);
	}

	salvarDados(RAIZ . 'dados/ranking.json', $dadosRanking);