<?php

declare(strict_types=1);

require(__DIR__ . '/src/Utils/web_scraper.php');

use App\Utils\WebScraper;

$dom = new DOMDocument();

for ($i = 1; $i < 5; $i++) {
    @$dom->loadHTML(WebScraper::HTTPRequest("http://estoremedia.space/DataIT/index.php?page=$i"));
    $xpath = new DOMXpath($dom);
    WebScraper::getDataFromHTML($xpath);
}

$data = WebScraper::getFinalData();
WebScraper::check_file('plik.csv');
WebScraper::convert_to_csv('plik.csv', $data);