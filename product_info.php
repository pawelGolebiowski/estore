<?php

declare(strict_types=1);

include('src\Utils\product_details.php');

use App\Utils\ProductDetails;

$value = $_GET["value"];
$html = file_get_html("http://estoremedia.space/DataIT/$value");

$productInfo = new ProductDetails;

$productInfo->get_product_info($html);