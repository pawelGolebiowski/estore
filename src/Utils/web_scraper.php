<?php

declare(strict_types=1);

namespace App\Utils;

class WebScraper
{
    private static array $productNamesList = [];
    private static array $cardsUrlList = [];
    private static array $photosUrlList = [];
    private static array $pricesList = [];
    private static array $numberOfEvaluationsList = [];
    private static array $numberOfStarsList = [];
    private static array $finalDataList = [];

    public static function HTTPRequest(string $url): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if ($response === false) {
            die(curl_error($curl));
        }

        curl_close($curl);
        return $response;
    }

    public static function getDataFromHTML($xpath): void
    {
        $names = $xpath->query('//div[@class="row"]//div[@class="row"]//div//div//div[@class="card-body"]//a');
        $cardsUrl = $xpath->query('//div[@class="row"]//div[@class="row"]//div//div//div[@class="card-body"]//a/@href');
        $photosUrl = $xpath->query('//div[@class="row"]//div[@class="row"]//div//div//img/@src');
        $prices = $xpath->query('//div[@class="row"]//div[@class="row"]//div//div//div[@class="card-body"]//h5');
        $reviews = $xpath->query('//div[@class="row"]//div[@class="row"]//div//div//div[@class="card-footer"]//small');

        foreach ($reviews as $review) {
            self::$numberOfEvaluationsList[] = trim(str_replace(['(', ')', '★', '☆', '-'], '', $review->nodeValue));
            self::$numberOfStarsList[] = substr_count($review->nodeValue, '★');
        }

        for ($i = 0; $i < sizeof($names); $i++) {
            array_push(self::$productNamesList, $names[$i]->nodeValue);
            array_push(self::$cardsUrlList, $cardsUrl[$i]->nodeValue);
            array_push(self::$photosUrlList, $photosUrl[$i]->nodeValue);
            array_push(self::$pricesList, $prices[$i]->nodeValue);
        }
    }

    public static function getFinalData(): array
    {
        for ($i = 0; $i < sizeof(self::$productNamesList); $i++) {
            $dataArray = array(
                'name' => self::$productNamesList[$i],
                'cardURL' => self::$cardsUrlList[$i],
                'photoURL' => self::$photosUrlList[$i],
                'price' => self::$pricesList[$i],
                'numberOfEvaluations' => self::$numberOfEvaluationsList[$i],
                'numberOfStars' => self::$numberOfStarsList[$i]
            );

            array_push(self::$finalDataList, $dataArray);
        }

        return self::$finalDataList;
    }

    public static function check_file(string $filename): void
    {
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    public static function convert_to_csv(string $filename, array $data): void
    {
        $file = fopen($filename, 'w') or die("Nie można otworzyc pliku!");
        fputcsv($file, array_keys(current($data)));
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
        if (file_exists($filename)) {
            echo "Plik został utworzony.";
        }
    }

    public static function read_csv($filename, $header = false): void
    {
        $handle = fopen($filename, "r");
        echo '<div><table border="1">';
        if ($header) {
            $csvcontents = fgetcsv($handle);
            echo '<tr>';
            foreach ($csvcontents as $headercolumn) {
                echo "<th>$headercolumn</th>";
            }
            echo '</tr>';
        }
        while ($csvcontents = fgetcsv($handle)) {
            echo '<tr>';
            foreach ($csvcontents as $key => $value) {
                if ($key == 0) {
                    echo "<td style='width:1px; white-space:nowrap;'><a href='product_info.php?value=$csvcontents[1]'>$value</a></td>";
                } else {
                    echo "<td style='width:1px; white-space:nowrap;'>$value</td>";
                }
            }
            echo '</tr>';
        }
        echo '</table></div>';
        fclose($handle);
    }
}