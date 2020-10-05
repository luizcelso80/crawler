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
		'step' => '150',
		'lst_status' => '',
		'txt_assunto' => ''	,
		'dt_public' => ''	,
		'dt_apres2' => '31/12/2020'	,
		'btn_materia_pesquisar' => 'Pesquisar' ,
		'dt_apres' => '01/01/2017'	,
		'txt_ano' => ''	,
		'incluir' => '0',
		'txt_relator' => '',
		'hdn_cod_autor' => '2' , // id vereador
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


	public function simpleRequest($data, $options = [])
    {
    	$ch = curl_init();
    	//$url = is_array($data) ? $data['url'] : $data;
    	//curl_setopt($ch, CURLOPT_URL,            $url);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 40000);
    	curl_setopt($ch, CURLOPT_HEADER,         1);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/6.0 (Windows; U; Windows NT 5.1; pt-BR; rv:1.9.5.6) Gecko/20054986 Firefox/2.0.7.6");

    	$url = $data . '?' . http_build_query($this->camaraParams);
		curl_setopt($ch, CURLOPT_URL, $data);
    	

    	if(is_array($data))
    	{
    		if(!empty($data['post']))
    		{
    			curl_setopt($ch, CURLOPT_POST,       1);
    			curl_setopt($ch, CURLOPT_POSTFIELDS, $data['post']);
    		}
    	}

    	if(!empty($options))
    	{
    		curl_setopt_array($ch, $options);
    	}

    	$result = curl_exec($ch);
    	//dd($result);
    	//$this->info = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    	curl_close($ch);
    	$dom = new \DOMDocument();
		@$dom->loadHTML($result);
		return $xpath = new \DOMXpath($dom);
    	//$dom = HtmlDomParser::str_get_html($result);
    	//return $dom;
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
		//$dom = $this->simpleRequest($url);

		//dd($dom);
		//$links = $dom->find('table tbody tr td[class=texto] a');

		//pega todos href
		//$links = $dom->find('table tr td[class=texto] a');


		$links = $dom->find('table tr td[class=texto] a'); //->find('text');

		//dd($links[0]->find('text')[16]->plaintext);
		//$links = $dom->query("//table/tr/td[@class='texto']/a");
		//dd($links);


		

		foreach ($links as $link) {
			$href = $link->getAttribute('href');
			//echo $href;
			$dom = $this->simpleRequest($href);

			$fieldset = $dom->query("//fieldset");
			//dd($fieldset);

			$texto = $fieldset->item(1)->getElementsByTagName('tr')->item(2)->getElementsByTagName('td')->item(0)->getElementsByTagName('b')->item(0)->textContent;

			//dd($texto);
			if($fieldset->length == 5){
				//echo $href;
				echo '<a href="'.$href.'" target="_blank">Matéria 5 fieldset</a>';
				echo '<br>';
				echo '<br>';
			}elseif ($fieldset->length == 4) {
				echo 'normal';
				echo '<br>';
				echo '<br>';
			}else{
				echo 'Verificar este:';
				echo '<br>';
				echo $href;
				foreach (range(0, 150) as $number) {
					echo '#';
				}
				echo '<br>';

			}
			//echo $fieldset->length;
			//echo '<br>';
			/*foreach (range(0, 150) as $number) {
			    echo '#';
			}
			echo '<br>';*/
			

				
			

			
			
		}

		//dd($links);
	}
}
