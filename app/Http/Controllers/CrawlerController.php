<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Crawler\Crawler;

class CrawlerController extends Controller
{
	private $crawler;

	public function __construct(Crawler $crawler)
	{
		$this->crawler = $crawler;
	}
	public function index(Request $request)
	{
		if ($request->isMethod('post')) {
			$input = $request->only(['searchbox', 'what', 'where']);
			$data = $this->crawler->getData($input, 1);
			$request->flashOnly(['what', 'where']);
			return redirect('crawler')
				->with('data', $data)
				->withInput();
		}
		return view('crawler.index');
	}

	public function camaraBauru(Request $request)
	{
		$data = $this->crawler->getCamara();
	}


	public function phantom()
	{
		$this->crawler->testPhanthom();
	}
}
