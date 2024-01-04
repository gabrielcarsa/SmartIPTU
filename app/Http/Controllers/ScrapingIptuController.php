<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use Carbon\Carbon;
use App\Models\Lote;
use App\Models\DescricaoDebito;
use App\Models\TipoDebito;
use App\Models\Debito;
use App\Models\TitularConta;
use App\Models\ParcelaContaReceber;
use App\Models\ParcelaContaPagar;

class ScrapingIptuController extends Controller
{
    //FAZ O SCRAPING DOS DADOS DO SITE DA PREFEITURA DE CG
    public function scrapingCampoGrande($inscricao_municipal){
        // Texto a ser inserido no campo de input (substitua pelo valor real)
        $textoInput = $inscricao_municipal;

        // URL da página com o formulário
        $urlFormulario = 'https://iptu.campogrande.ms.gov.br/';

        // URL da página de destino após a submissão
        $urlDestino = 'https://iptu.campogrande.ms.gov.br/Usuario/Debitos';

        // Instancia o cliente Goutte
        $client = new Client();

        try {
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

            $resultado = [
                'resultadoParcela' => $resultadoParcela,
                'resultadoLote' => $resultadoLote
            ];
             
            return $resultado;
     
         } catch (\Exception $e) {
             //dd('Erro durante a requisição GET: ' . $e->getMessage());
             return redirect()->back()->with('error', 'Erro ao tentar obter dados! Se o problema persistir entre em contato com o suporte');
 
         }
    }

    //RETORNA VIEW COM TODOS OS DÉBITOS TIRADOS DA PREFEITURA DE CG
    public function iptuCampoGrande($inscricao_municipal, $lote_id)
    {
        //instanciando controller
        $ScrapingController = new ScrapingIptuController();

        //atribuindo a variavel resultado da funcao scraping Campo Grande
        $resultadoScraping = $ScrapingController->scrapingCampoGrande($inscricao_municipal);

        //separando variaveis conforme resultado
        $resultadoParcela = $resultadoScraping['resultadoParcela'];
        $resultadoLote = $resultadoScraping['resultadoLote'];

        //retornando view
        return view('scraping/iptu_campo_grande_ms', compact('resultadoParcela', 'resultadoLote', 'lote_id'));
    
    }

    //ADICIONAR DEBITOS SCRAPING IPTU APENAS COM UM BOTÃO
    public function iptuCampoGrandeAdicionarDireto($inscricao_municipal, $lote_id, $usuario_id)
    {
        //instanciando controller
        $ScrapingController = new ScrapingIptuController();

        //atribuindo a variavel resultado da funcao scraping Campo Grande
        $resultadoScraping = $ScrapingController->scrapingCampoGrande($inscricao_municipal);

        //separando variaveis conforme resultado
        $resultadoParcela = $resultadoScraping['resultadoParcela'];
        $resultadoLote = $resultadoScraping['resultadoLote'];
       
        //definindo variaveis
        $qtd_debitos = count($resultadoParcela);
        $lote = Lote::find($lote_id);
        $aux_debito_receber = 0;
        $aux_debito_pagar = 0;    

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');  
        
        for($i = 1; $i <= $qtd_debitos; $i++){
            $aux_debito_receber = 0;
            $aux_debito_pagar = 0;    
            $qtd_parcelas = count($resultadoParcela[$i-1]['parcelas']);

            for($j = 1; $j <= $qtd_parcelas; $j++){
                $data_vencimento_aux = Carbon::createFromFormat('d/m/Y', $resultadoParcela[$i-1]['parcelas'][$j-1]['vencimento'])->format('Y-m-d');
   
                //Débito da EMPRESA (PAGAR)
                if($lote->data_venda > $data_vencimento_aux){
    
                    if($aux_debito_pagar == 0){
                        $debito = new Debito();
                        $aux_debito_pagar++;
                        $tipo_debito = TipoDebito::whereRaw("LOWER(`descricao`) LIKE ?", ['%' . strtolower($resultadoParcela[$i-1]['titulo']) . '%'])->first();
                       
                        //Verificando se existe o tipo de débito
                        if($tipo_debito == null){
                            $novo_tipo_debito = new TipoDebito();
                            $novo_tipo_debito->descricao = strtoupper($resultadoParcela[$i-1]['titulo']);
                            $novo_tipo_debito->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                            $novo_tipo_debito->cadastrado_usuario_id = $usuario_id;
                            $novo_tipo_debito->save();
                            $debito->tipo_debito_id = $novo_tipo_debito->id;
    
                        }else{
                            $debito->tipo_debito_id = $tipo_debito->id;
                        }
                        $debito->lote_id = $lote_id;
                        $debito->titular_conta_id = 1;
                        $debito->data_vencimento  = Carbon::createFromFormat('d/m/Y', $resultadoParcela[0]['parcelas'][0]['vencimento'])->format('Y-m-d');
                        $debito->quantidade_parcela = count($resultadoParcela[$i-1]['parcelas']);
                        
                        
                        $descricao_debito = DescricaoDebito::where('descricao', 'like', '%' . $resultadoParcela[$i-1]['parcelas'][$j-1]['descricao_debito'] . '%')->first();
                        
                        //Verificando se existe o descricao de débito
                        if($descricao_debito == null){
                            $novo_descricao_debito = new DescricaoDebito();
                            $novo_descricao_debito->descricao = strtolower($resultadoParcela[$i-1]['parcelas'][$j-1]['descricao_debito']);
                            $novo_descricao_debito->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                            $novo_descricao_debito->cadastrado_usuario_id = $usuario_id;
                            $novo_descricao_debito->save();
                            $debito->descricao_debito_id = $novo_descricao_debito->id;
    
                        }else{
                            $debito->descricao_debito_id = $descricao_debito->id;
                        }
                        
                        
                        if($resultadoParcela[$i-1]['parcelas'][$j-1]['valor_total_parcelamento'] == ""){
                            $valor_parcela = str_replace(',', '.', $resultadoParcela[$i-1]['parcelas'][$j-1]['valor_total_debitos']);
                        }else if($resultadoParcela[$i-1]['parcelas'][$j-1]['valor_total_debitos'] == "0,00"){
                            $valor_parcela = str_replace(',', '.', $resultadoParcela[$i-1]['parcelas'][$j-1]['valor_total_parcelamento']);
                        }
    
                        $valor_corrigido_parcela = str_replace(',', '.', $valor_parcela);
                        $debito->valor_parcela = (double) $valor_corrigido_parcela; 
    
                        $debito->observacao = null;
                        $debito->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                        $debito->cadastrado_usuario_id = $usuario_id;

                        $debito->save();
                    }
                    // Cadastrar Parcelas
                    $cadastroParcelas = $ScrapingController->cadastrarParcelas($debito, $resultadoParcela, $i, $j, $data_vencimento_aux, $lote, $usuario_id);
                    
                //Débito do CLIENTE (RECEBER)
                }else{
                    if($aux_debito_receber == 0){
    
                        $debito = new Debito();
                        $aux_debito_receber++;
                        $tipo_debito = TipoDebito::whereRaw("LOWER(`descricao`) LIKE ?", ['%' . strtolower($resultadoParcela[$i-1]['titulo']) . '%'])->first();
                       
                        //Verificando se existe o tipo de débito
                        if($tipo_debito == null){
                            $novo_tipo_debito = new TipoDebito();
                            $novo_tipo_debito->descricao = strtoupper($resultadoParcela[$i-1]['titulo']);
                            $novo_tipo_debito->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                            $novo_tipo_debito->cadastrado_usuario_id = $usuario_id;
                            $novo_tipo_debito->save();
                            $debito->tipo_debito_id = $novo_tipo_debito->id;
    
                        }else{
                            $debito->tipo_debito_id = $tipo_debito->id;
                        }
                        $debito->lote_id = $lote_id;
                        $debito->titular_conta_id = 1;
                        $debito->data_vencimento  = Carbon::createFromFormat('d/m/Y', $resultadoParcela[0]['parcelas'][0]['vencimento'])->format('Y-m-d');
                        $debito->quantidade_parcela = count($resultadoParcela[$i-1]['parcelas']);
                        
                        
                        $descricao_debito = DescricaoDebito::where('descricao', 'like', '%' . $resultadoParcela[$i-1]['parcelas'][$j-1]['descricao_debito'] . '%')->first();
                        
                        //Verificando se existe o descricao de débito
                        if($descricao_debito == null){
                            $novo_descricao_debito = new DescricaoDebito();
                            $novo_descricao_debito->descricao = strtolower($resultadoParcela[$i-1]['parcelas'][$j-1]['descricao_debito']);
                            $novo_descricao_debito->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                            $novo_descricao_debito->cadastrado_usuario_id = $usuario_id;
                            $novo_descricao_debito->save();
                            $debito->descricao_debito_id = $novo_descricao_debito->id;
    
                        }else{
                            $debito->descricao_debito_id = $descricao_debito->id;
                        }
                        
                        
                        if($resultadoParcela[$i-1]['parcelas'][$j-1]['valor_total_parcelamento'] == ""){
                            $valor_parcela = str_replace(',', '.', $resultadoParcela[$i-1]['parcelas'][$j-1]['valor_total_debitos']);
                        }else if($resultadoParcela[$i-1]['parcelas'][$j-1]['valor_total_debitos'] == "0,00"){
                            $valor_parcela = str_replace(',', '.', $resultadoParcela[$i-1]['parcelas'][$j-1]['valor_total_parcelamento']);
                        }
    
                        $valor_corrigido_parcela = str_replace(',', '.', $valor_parcela);
                        $debito->valor_parcela = (double) $valor_corrigido_parcela; 
    
                        $debito->observacao = null;
                        $debito->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                        $debito->cadastrado_usuario_id = $usuario_id;

                        $debito->save();
                    }
                    
                    //Cadastro de Parcelas
                    $cadastroParcelas = $ScrapingController->cadastrarParcelas($debito, $resultadoParcela, $i, $j, $data_vencimento_aux, $lote, $usuario_id);


                }
            }
            
        }
        return redirect('lote/gestao/'.$lote_id)->with('success', 'Débito cadastrado com sucesso');
    }

    //FUNÇÃO PARA CADASTRAR PARCELAS
    public function cadastrarParcelas($debito, $resultadoParcela, $i, $j, $data_vencimento_aux, $lote, $usuario_id){
        $debito_id = $debito->id;
        $data_vencimento = $debito->data_vencimento; 
        $valor_entrada = $debito->valor_entrada;
        $empresa = TitularConta::find(1);

            if($lote->data_venda > $data_vencimento_aux){
                $parcela = new ParcelaContaPagar();
            }else{
                $parcela = new ParcelaContaReceber();
            }
            
            $parcela->debito_id = $debito_id;
            $parcela->numero_parcela = $i;
            if($resultadoParcela[$i-1]['parcelas'][0]['valor_total_parcelamento'] == "" || $resultadoParcela[$i-1]['parcelas'][0]['valor_total_parcelamento'] == "0,00"){
                $valorAux = str_replace('.', '', $resultadoParcela[$i-1]['parcelas'][$j-1]['valor_total_debitos']);
                $parcela->valor_parcela = str_replace(',', '.', $valorAux);
                $parcela->numero_parcela = 1;
            }else if($resultadoParcela[$i-1]['parcelas'][0]['valor_total_debitos'] == "" || $resultadoParcela[$i-1]['parcelas'][0]['valor_total_debitos'] == "0,00"){
                $valorAux = str_replace('.', '', $resultadoParcela[$i-1]['parcelas'][$j-1]['valor_total_parcelamento']);
                $parcela->valor_parcela = str_replace(',', '.', $valorAux);
                $parcela->numero_parcela = $j;
            }
            $parcela->cadastrado_usuario_id = $usuario_id;
            $parcela->situacao = 0;
            $parcela->data_vencimento = Carbon::createFromFormat('d/m/Y', $resultadoParcela[$i-1]['parcelas'][$j-1]['vencimento'])->format('Y-m-d');

            $parcela->save();
    }
}
