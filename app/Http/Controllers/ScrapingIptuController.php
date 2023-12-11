<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;

class ScrapingIptuController extends Controller
{
    public function iptuCampoGrande($inscricao_municipal, $lote_id)
    {
        // Texto a ser inserido no campo de input (substitua pelo valor real)
        $textoInput = $inscricao_municipal;

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
        //echo 'Valor do campo InscricaoMunicipal: ' . $form['InscricaoMunicipal']->getValue() . '<br>';

        // Submete o formulário
        $client->submit($form);

        // Log: Verifica a URL após a submissão
        //echo 'URL após submissão: ' . $client->getHistory()->current()->getUri() . '<br>';

        // Agora, $client contém a página de destino após a submissão
        // scraping na página de destino conforme necessário
        $crawler = $client->request('GET', $urlDestino);

        $resultadoLote = [];

        $crawler->filter('section.dados')->each(function ($section) use (&$resultadoLote) {
            // Extrai informações da coluna 1
            $col1 = $section->filter('.col-lg-4')->eq(0);
            $responsabilidade = $col1->filter('.dados-dados')->eq(0)->text();
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

            $resultadoLote = [
                'responsabilidade' => $responsabilidade,
                'endereco' => $endereco,
                'bairro' => $bairro,
                'quadra' => $quadra,
                'lote' => $lote,
                'inscricaoMunicipal' => $inscricaoMunicipal,
            ];
        });

       

        // Encontrar todas as tabelas com o id começando por "list-table-"
        $tables = $crawler->filter('table[id^="list-table-"]');

        $resultadoParcela = [];
        $parcelas = [];
        $i = 0;
        $titulo = [];

        // Iterar sobre todas as tabelas encontradas
        $tables->each(function ($table) use (&$resultadoParcela, &$parcelas, &$i, &$titulo) {
            // Encontrar todas as linhas no corpo da tabela
            $rows = $table->filter('tbody tr');

            // Iterar sobre todas as linhas encontradas
            $rows->each(function ($row) use (&$resultadoParcela, &$parcelas, &$i, &$titulo) {
                $auxTitulo = $row->filter('tr')->eq(0)->text();

                //Preencher tipo de Débito conforme o nome
                if (
                    $auxTitulo == "Pagamento Parcelado (2023)" ||
                    $auxTitulo == "Débitos Protestados" ||
                    $auxTitulo == "Débitos Negativados" ||
                    $auxTitulo == "Débitos Inscritos em Dívida Ativa" ||
                    $auxTitulo == "Débitos Ajuizados" ||
                    $auxTitulo == "Pagamento Parcelado (2024)" ||
                    $auxTitulo == "Débitos Anteriores" ||
                    $auxTitulo == "Pagamento à Vista (2024)" 
                ) {
                    $titulo[$i] = $auxTitulo;
                }
                
                //Deixar uma string vazia por padrão
                $vencimento = "";
                $descricao_debito = "";
                $valor_total_parcelamento = "";
                $valor_total_debitos = "";

                if($row->filter('tr')->eq(0)->filter('td')->eq(3)->count() > 0){
                    $vencimento = $row->filter('tr')->eq(0)->filter('td')->eq(3)->text(); 
                }
                if($row->filter('tr')->eq(0)->filter('td')->eq(10)->count() > 0){
                    $valor_total_parcelamento = $row->filter('tr')->eq(0)->filter('td')->eq(10)->text();
                }
                if($row->filter('tr')->eq(0)->filter('td')->eq(9)->count() > 0){
                    $valor_total_debitos = $row->filter('tr')->eq(0)->filter('td')->eq(9)->text();
                }
                if($row->filter('tr')->eq(0)->filter('td')->eq(1)->count() > 0){
                    $content = trim($row->filter('tr')->eq(0)->filter('td')->eq(1)->text());
                  
                    if (preg_match('/[a-zA-Z]/', $content)) {
                        $descricao_debito = $content;
                    }else{
                        $resultadoParcela[$i] = [
                            'titulo' => isset($titulo[$i]) ? $titulo[$i] : 'Valor Padrão',
                            'parcelas' => $parcelas,
                        ];
                        $i++;
                        $parcelas = [];
                    }
                    
                   
                }
               
                //Preencher parcelas se houver
                if(!empty($vencimento)){
                    $parcelas[] = [
                        'vencimento' => $vencimento,
                        'descricao_debito' => $descricao_debito,
                        'valor_total_parcelamento' => $valor_total_parcelamento,
                        'valor_total_debitos' => $valor_total_debitos,
                    ];
                }

                //Pegar Pagamento à Vista se houver
                if(!empty($vencimento) && $i == 0){
                    if($titulo[$i] == "Pagamento à Vista (2024)"){
                        $resultadoParcela[$i] = [
                            'titulo' => isset($titulo[$i]) ? $titulo[$i] : 'Valor Padrão',
                            'parcelas' => $parcelas,
                        ];
                        $i++;
                        $parcelas = [];
                    }
                  
                }
               
            });
        });


        return view('scraping/iptu_campo_grande_ms', compact('resultadoParcela', 'resultadoLote', 'lote_id'));
    
    }
}
