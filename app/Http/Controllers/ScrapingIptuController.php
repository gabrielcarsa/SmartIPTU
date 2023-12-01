<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;

class ScrapingIptuController extends Controller
{
    public function iptuCampoGrande(Request $request)
    {
        // Texto a ser inserido no campo de input (substitua pelo valor real)
        $textoInput = "7610210320";

        // URL da página com o formulário
        $urlFormulario = 'https://iptu.campogrande.ms.gov.br/';

        // URL da página de destino após a submissão
        $urlDestino = 'https://iptu.campogrande.ms.gov.br/Usuario/Debitos';

        // Instancia o cliente Goutte
        $client = new Client();

        // Faz a requisição GET para a página com o formulário
        $crawler = $client->request('GET', $urlFormulario);

        // Preenche o campo de input com o texto desejado
        $form = $crawler->filter('button.btn-primary')->form();
        $form['InscricaoMunicipal'] = $textoInput;

        // Log: Verifica se o campo foi preenchido corretamente
        echo 'Valor do campo InscricaoMunicipal: ' . $form['InscricaoMunicipal']->getValue() . '<br>';

        // Submete o formulário
        $client->submit($form);

        // Log: Verifica a URL após a submissão
        echo 'URL após submissão: ' . $client->getHistory()->current()->getUri() . '<br>';


        // Agora, $client contém a página de destino após a submissão
        // Faça o scraping na página de destino conforme necessário
        $crawler = $client->request('GET', $urlDestino);

        $crawler->filter('section.dados')->each(function ($section) {
            // Extrai informações da coluna 1
            $col1 = $section->filter('.col-lg-4')->eq(0);
            $contribuinte = $col1->filter('.dados-dados')->eq(0)->text();
            $endereco = $col1->filter('.dados-dados')->eq(2)->text();
        
            // Extrai informações da coluna 2
            $col2 = $section->filter('.col-lg-4')->eq(1);
            $bairro = $col2->filter('.dados-dados')->eq(0)->text();
            $quadra = $col2->filter('.dados-dados')->eq(1)->text();
            $lote = $col2->filter('.dados-dados')->eq(2)->text();
        
            // Extrai informações da coluna 3
            $col3 = $section->filter('.col-lg-4')->eq(2);
            $inscricaoMunicipal = $col3->filter('.dados-dados')->eq(0)->text();
            $situacao = $col3->filter('.dados-dados')->eq(1)->text();
        
            // Exibe as informações extraídas
            echo "Contribuinte: $contribuinte<br>";
            echo "Endereço: $endereco<br>";

            echo "Bairro: $bairro<br>";
            echo "Quadra: $quadra<br>";
            echo "Lote: $lote<br>";
            echo "Inscrição Municipal: $inscricaoMunicipal<br>";
            echo "Situação: $situacao<br>";
        });

         // Encontrar todas as tabelas com o id começando por "list-table-"
         $tables = $crawler->filter('table[id^="list-table-"]');

         $result = [];
 
         // Iterar sobre todas as tabelas encontradas
         $tables->each(function ($table) use (&$result) {
             // Encontrar todas as linhas no corpo da tabela
             $rows = $table->filter('tbody tr');
 
             // Iterar sobre todas as linhas encontradas
             $rows->each(function ($row) use (&$result) {
                 // Extrair dados de cada coluna na linha
                 $tributo = $row->filter('td')->eq(0)->text();
                 $vencimento = $row->filter('td')->eq(1)->text();
                 $valorTotal = $row->filter('td')->eq(2)->text();
 
                 // Armazenar os dados em um array ou em um banco de dados, conforme necessário
                 $result[] = [
                     'tributo' => $tributo,
                     'vencimento' => $vencimento,
                     'valor_total' => $valorTotal,
                 ];
             });
         });
 
         // Agora $result contém os dados de todas as tabelas
         dd($result);
    }
}
