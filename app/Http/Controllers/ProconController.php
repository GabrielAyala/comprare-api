<?php

namespace App\Http\Controllers;

class ProconController extends Controller
{
    public function getDataFromProcon()
    {
        $url = "https://www.jaraguadosul.sc.gov.br/procon/pesquisas.php";

        $htmlContent = file_get_contents($url);

        preg_match_all('/<table class="table table-condensed table-striped table-text table-bordered" style="font-size: 9px;">(.*?)<\/table>/s', $htmlContent, $matches);

        $tableContent = $matches[0][0];

        $document = new \DOMDocument();
        $document->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . $tableContent);

        $header = $document->getElementsByTagName('th');
        $detail = $document->getElementsByTagName('td');

        foreach ($header as $values) {
            $headerName[] = $values->textContent;
        }

        for ($i = 14; $i <= 27; $i++) {
            unset($headerName[$i]);
        }

        foreach ($detail as $values) {
            $tableDetail[] = $values->textContent;
        }

        $newTableDetail = array_chunk($tableDetail, 14, true);

        foreach ($newTableDetail as $values) {
            $newMarketPrices[] = array_combine(str_replace(' ', '', $headerName), $values);
        }

        for ($i = 1; $i <= count($newMarketPrices); $i++) {
            $ids[] = [
                "id" => $i
            ];
        }

        for ($i = 0; $i < count($newMarketPrices); $i++) {
            $finalMarketPrices[] = array_merge($ids[$i], $newMarketPrices[$i]);
        }

        foreach ($finalMarketPrices as $values) {

            foreach ($values as $key => $value) {

                if ($key == 'Carne(coxãomole)1kg') {

                    $new_key = 'Carne';

                    $new_content = [
                        $new_key => $value
                    ];

                    unset($values[$key]);

                    $fixedMarketPrices[] = array_merge($values, $new_content);
                }
            }
        }

        return $fixedMarketPrices;
    }
}
