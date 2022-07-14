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
            $newMarketPrices[] = array_combine($headerName, $values);
        }

        return $newMarketPrices;
    }
}