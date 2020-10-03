<?php

namespace App\Crawler;

ini_set('max_execution_time', 5000);

use GuzzleHttp\Client;
use KubAT\PhpSimple\HtmlDomParser;
//use JonnyW\PhantomJs\Client;

class Crawler
{
	private $baseUrl = 'https://www.guiamais.com.br/encontre';
	private $data = [];
	private $cliente;

	private $camaraParams = [
		'page' => '1',
		'step' => '211',
		'lst_status' => '',
		'txt_assunto' => ''	,
		'dt_public' => ''	,
		'dt_apres2' => ''	,
		'btn_materia_pesquisar' => 'Pesquisar' ,
		'dt_apres' => ''	,
		'txt_ano' => ''	,
		'incluir' => '0',
		'txt_relator' => '',
		'hdn_cod_autor' => '472' ,
		'txt_numero' => '',
		'rd_ordenacao' => '2',
		'rad_tramitando' => '',
		'dt_public2' => '',
		'existe_ocorrencia' => '0',
		'lst_cod_partido' => '',
		'lst_localizacao' => '',
		'lst_tip_materia' => '',
		'chk_coautor' => '',
		'txt_num_protocolo' => '',
		'txt_npc' => ''
	
	];

	public function __construct(Client $cliente)
	{
		$this->cliente = $cliente;
	}


	public function getData($query, $page)
	{
		do {
			$this->setQuery($query, $page);

			$dom = $this->getDom();

			$this->getLinks($dom);

			$page++;
		} while ($this->hasNextPage($dom));

		return $this->data;
	}

	private function getLinks($dom)
	{
		$divs = $dom->find('div[itemprop] meta[itemprop=url]');

		foreach ($divs as $key => $value) {
			$this->data[] = trim($value->content);
		}
	}

	private function hasNextPage($dom)
	{
		$nextPage = $dom->find('nav.pagination a.nextPage');
		if (count($nextPage) > 0)
			return true;
		else
			return false;
	}

	private function getDom($url)
	{
		
		$response = $this->cliente->request('GET', $url,[
			'query' => $this->camaraParams
		]);

		$body = $response->getBody();

		$dom = HtmlDomParser::str_get_html($body);
		//dd($dom);
		return $dom;
	}

	private function setQuery($query, $page)
	{
		$query['page'] = $page;
		$this->query['query'] = $query;
	}

	public function testPhanthom()
	{
		$client = Client::getInstance();
		$client->getEngine()->setPath('H:\\xampp\\htdocs\\crawler\\bin');
		$request  = $client->getMessageFactory()->createRequest();
		$response = $client->getMessageFactory()->createResponse();

		$request->setMethod('GET');
		$request->setUrl('http://jonnyw.me');

		$client->send($request, $response);

		if ($response->getStatus() === 200) {
			echo $response->getContent();
		}
	}

	public function getCamara()
	{
		$url = 'https://sapl.bauru.sp.leg.br/generico/materia_pesquisar_proc';
		//$url = 'https://sapl.bauru.sp.leg.br/generico/materia_pesquisar_proc?incluir=0&existe_ocorrencia=0&txt_numero=&txt_ano=&txt_npc=&txt_num_protocolo=&dt_apres=01%2F01%2F2017&dt_apres2=31%2F01%2F2017&dt_public=&dt_public2=&hdn_cod_autor=472&hdn_cod_autor_new_value=false&txt_assunto=&rad_tramitando=&lst_localizacao=&lst_status=&rd_ordenacao=1&txt_relator=&lst_cod_partido=&chk_coautor=&btn_materia_pesquisar=Pesquisar';
		$dom = $this->getDom($url);


		//$links = $dom->find('table tbody tr td[class=texto] a');
		$links = $dom->find('table tr td[class=texto] a');

		foreach ($links as $link) {
			echo $link->getAttribute('href');
			echo '<br>';
		}

		//dd($links);
	}
}
