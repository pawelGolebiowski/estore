<?php

declare(strict_types=1);

namespace App\Utils;

require('simple_html_dom.php');

class ProductDetails
{
    public function get_product_info(object $html): void
    {
        $this->get_name($html);
        $this->get_price($html);
        $this->get_old_price($html);
        $this->get_photo_url($html);
        $this->get_reviews($html);
        $this->get_code_and_variants($html);
    }

    private function get_name(object $html): void
    {
        $name = $html->find('div[class=row mb-0 pl-3], h3', 0)->plaintext;
        if (isset($name)) {
            echo <<<HTML
            <div>Nazwa produktu: $name</div>
            HTML;
        }
    }

    private function get_price(object $html): void
    {
        $price = $html->find('h5 span', 0)->plaintext;
        if (isset($price)) {
            echo <<<HTML
            <div>Cena produktu: $price</div>
            HTML;
        }
    }

    private function get_old_price(object $html): void
    {
        @$oldPrice = $html->find('h5 del', 0)->plaintext;
        if (isset($oldPrice)) {
            echo <<<HTML
            <div>Stara cena produktu: $oldPrice</div>
            HTML;
        }
    }

    private function get_photo_url(object $html): void
    {
        $photoUrl = $html->find('div[class=card h-100] img[src]', 0)->src;
        if (isset($photoUrl)) {
            echo <<<HTML
            <div>Link do zdjęcia produktu: $photoUrl</div>
            HTML;
        }
    }

    private function get_reviews(object $html): void
    {
        $evaluations = preg_replace('/[^0-9]/', '', trim(str_replace(['(', ')', '&#9733', '&#9734'], '', $html->find('small.text-muted', 0)->plaintext)));
        $stars = substr_count($html->find('small[class=text-muted]', 0)->plaintext, '&#9733');
        if (isset($evaluations) || isset($stars)) {
            echo <<<HTML
            <div>Liczba ocen: $evaluations, liczba gwiazdek: $stars</div>
            HTML;
        }
    }

    private function get_code_and_variants(object $html): void
    {
        $json = json_decode($html->find('script[type="application/json"]', 0)->innertext, true);
        $name = $html->find('div[class=row mb-0 pl-3], h3', 0)->plaintext;
        $code = $json['products']['code'];
        if (isset($name)) {
            echo <<<HTML
            <div>Kod produktu: $code</div>
            HTML;
        }

        foreach ($json['products']['variants'] as $key => $variant) {
            if (isset($key)) {
                echo '<div>Aktualna cena wariantu: ' . $name . ':#' . $key . '</div>';
            }
            if (isset($variant['price'])) {
                echo '<div>Aktualna cena wariantu: ' . $variant['price'] . '</div>';
            }
            if (isset($variant['price_old'])) {
                echo '<div>Stara cena wariantu: ' . $variant['price_old'] . '</div>';
            }
        }
    }
}