<?php

declare(strict_types=1);

require(__DIR__ . '/src/Utils/web_scraper.php');

use App\Utils\WebScraper;

WebScraper::read_csv('plik.csv', true);