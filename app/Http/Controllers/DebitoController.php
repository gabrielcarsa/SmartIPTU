<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Debito;
use App\Models\ParcelaContaReceber;
use App\Models\ParcelaContaPagar;
use App\Models\DescricaoDebito;
use App\Models\Lote;
use App\Models\TitularConta;
use App\Models\SaldoDiario;
use App\Models\TipoDebito;
use App\Models\MovimentacaoFinanceira;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\DebitoRequest;


class DebitoController extends Controller
{
    //RETORNA VIEW PARA ADICIONAR DÉBITO
    function novo($lote_id){
        $tipo_debito = TipoDebito::all();
        $descricao_debito = DescricaoDebito::all();

        $data = [
            'tipo_debito' => $tipo_debito,
            'descricao_debito' => $descricao_debito,
            'lote_id' => $lote_id,
        ];

        return view('debito/debito_novo', compact('data'));
    }

    //CADASTRO DE DÉBITO
    function cadastrar($usuario, $lote_id, DebitoRequest $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $debito = new Debito();
        $debito->tipo_debito_id = $request->input('tipo_debito_id');
        $debito->lote_id = $lote_id;
        $debito->quantidade_parcela = $request->input('quantidade_parcela');
        $debito->titular_conta_id = 1;
        $debito->data_vencimento = $request->input('data_vencimento');
        $debito->descricao_debito_id = $request->input('descricao_debito_id');
        
        $valor_parcela = str_replace(',', '.', $request->input('valor_parcela'));
        $debito->valor_parcela = (double) $valor_parcela; // Converter a string diretamente para um número em ponto flutuante
      
        $valor_entrada = str_replace(',', '.', $request->input('valor_entrada'));
        $debito->valor_entrada = (double) $valor_entrada; // Converter a string diretamente para um número em ponto flutuante

        $debito->observacao = $request->input('observacao');
        $debito->data_cadastro = date('d-m-Y h:i:s a', time());
        $debito->cadastrado_usuario_id = $usuario;
        $debito->save();

        // Cadastrar Parcelas
        $qtd_parcelas = $request->input('quantidade_parcela');
        $debito_id = $debito->id;
        $data_vencimento = $debito->data_vencimento; 
        $dataCarbon = Carbon::createFromFormat('Y-m-d', $data_vencimento);
        $valor_entrada = $debito->valor_entrada;
        $empresa = TitularConta::find(1);
        $lote = Lote::find($debito->lote_id);

        for($i = 1; $i <= $qtd_parcelas; $i++){
            //Se a responsabilidade do lote for da EMPRESA então é um débito a pagar
            if($empresa->cliente_id == $lote->cliente_id){
                $parcela = new ParcelaContaPagar();
            }else{// Caso contrário é um débito a receber
                $parcela = new ParcelaContaReceber();
            }
            $parcela->debito_id = $debito_id;
            $parcela->numero_parcela = $i;
            $parcela->valor_parcela = $debito->valor_parcela;
            $parcela->cadastrado_usuario_id = $usuario;
            if($i > 1){
                $parcela->data_vencimento = $dataCarbon->addMonth();
            }else{
                if($valor_entrada != 0){
                    $parcela->valor_parcela = $valor_entrada;
                }
                $parcela->data_vencimento = $data_vencimento;
            }
            $parcela->save();
        }

        return redirect('lote/gestao/'.$lote_id)->with('success', 'Débito cadastrado com sucesso');
    }

     //RETORNA VIEW PARA REAJUSTAR PARCELA
     function reajustar_view(Request $request){

        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
             // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

            //Verificar se é parcela a pagar ou receber
            $empresa = TitularConta::find(1);
            $lote = Lote::find($request->input('lote_id'));

            //Verificar se há parcelas já recebidas
            foreach($checkboxesSelecionados as $parcelaId) {
                //Se a responsabilidade do lote for da EMPRESA então é um débito a pagar
                if($empresa->cliente_id == $lote->cliente_id){
                    $parcela = ParcelaContaPagar::find($parcelaId);
                }else{// Caso contrário é um débito a receber
                    $parcela = ParcelaContaReceber::find($parcelaId);
                }

                //Se houver parcelas já recebidas redireciona de volta
                if($parcela->situacao == 1){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto! Dica: para alterar parcelas já recebidas/pagas estornar o recebimento/pagamento!');
                }
            }
            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                //Se a responsabilidade do lote for da EMPRESA então é um débito a pagar
                if($empresa->cliente_id == $lote->cliente_id){
                    $parcelas[] = DB::table('parcela_conta_pagar as p')
                    ->select(
                        'p.id as id',
                        'p.numero_parcela as numero_parcela',
                        'p.data_vencimento as data_vencimento',
                        'p.valor_parcela as valor_parcela',
                        'p.situacao as situacao_parcela',
                        'd.id as debito_id',
                        'd.quantidade_parcela as debito_quantidade_parcela',
                        'd.descricao_debito_id as debito_descricao_debito_id',  
                        'dd.descricao as descricao',       
                    )
                    ->leftJoin('debito AS d', 'p.debito_id', '=', 'd.id')
                    ->leftJoin('descricao_debito AS dd', 'd.descricao_debito_id', '=', 'dd.id')
                    ->where('p.id', $parcelaId)
                    ->get();
                }else{// Caso contrário é um débito a receber
                    $parcelas[] = DB::table('parcela_conta_receber as p')
                    ->select(
                        'p.id as id',
                        'p.numero_parcela as numero_parcela',
                        'p.data_vencimento as data_vencimento',
                        'p.valor_parcela as valor_parcela',
                        'p.situacao as situacao_parcela',
                        'd.id as debito_id',
                        'd.quantidade_parcela as debito_quantidade_parcela',
                        'd.descricao_debito_id as debito_descricao_debito_id',  
                        'dd.descricao as descricao',       
                    )
                    ->leftJoin('debito AS d', 'p.debito_id', '=', 'd.id')
                    ->leftJoin('descricao_debito AS dd', 'd.descricao_debito_id', '=', 'dd.id')
                    ->where('p.id', $parcelaId)
                    ->get();
                }
               
            }
            return view('parcela/parcela_reajustar', compact('parcelas'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    //REAJUSTAR PARCELAS
    function reajustar($user_id, Request $request){
        $origem = $request->input('origem'); //controle para redirecionar para lugar correto

        $valor_unico = str_replace(',', '.', $request->input('valor_unico'));
        $request->merge(['valor_unico' => $valor_unico]);

        $validated = $request->validate([
            'valor_unico' => 'required|numeric|min:0.1',
        ]);

        $idParcelas = $request->get('id_parcela', []);

        foreach($idParcelas as $p){
            $parcela = ParcelaContaReceber::find($p);

            $valor_parcela = str_replace(',', '.', $request->input('valor_unico'));
            $parcela->valor_parcela = (double) number_format($valor_parcela, 2, '.', '');  

            $parcela->save();
        }
        $parcelaReferencia = ParcelaContaReceber::find($idParcelas[0]);
        $debito = Debito::find($parcelaReferencia->debito_id);
        $lote_id = $debito->lote_id;
        
        if($origem == "lote_gestao"){
            return redirect("lote/gestao/".$lote_id)->with('success', 'Parcelas reajustadas com sucesso');   
        } else if($origem == "contas_receber"){
            return redirect("contas_receber")->with('success', 'Parcelas reajustadas com sucesso');   
        }else if($origem == "contas_pagar"){
            return redirect("contas_pagar")->with('success', 'Parcelas reajustadas com sucesso');   
        }
    }

      //RETORNA VIEW PARA ALTERAR DATA DE VENCIMENTO
      function alterar_vencimento(Request $request){
       
        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
             // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

            //Verificar se há parcelas já recebidas
            foreach($checkboxesSelecionados as $parcelaId) {
                $parcela = ParcelaContaReceber::find($parcelaId);

                //Se houver parcelas já recebidas redireciona de volta
                if($parcela->situacao == 1){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto! Dica: para alterar parcelas já recebidas/pagas estornar o recebimento/pagamento!');
                }
            }
    
            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela_conta_receber as p')
                ->select(
                    'p.id as id',
                    'p.numero_parcela as numero_parcela',
                    'p.data_vencimento as data_vencimento',
                    'p.valor_parcela as valor_parcela',
                    'p.situacao as situacao_parcela',
                    'd.id as debito_id',
                    'd.quantidade_parcela as debito_quantidade_parcela',
                    'd.descricao_debito_id as debito_descricao_debito_id',  
                    'dd.descricao as descricao',       
                )
                ->leftJoin('debito AS d', 'p.debito_id', '=', 'd.id')
                ->leftJoin('descricao_debito AS dd', 'd.descricao_debito_id', '=', 'dd.id')
                ->where('p.id', $parcelaId)
                ->get();
            }
            return view('parcela/parcela_alterar_vencimento', compact('parcelas'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    //ALTERAR DATA DE VENCIMENTO
    function definir_alteracao_data($user_id, Request $request){
        $origem = $request->input('origem'); //controle para redirecionar para lugar correto
        
        $validated = $request->validate([
            'data_vencimento' => 'required|date',
        ]);

        $idParcelas = $request->get('id_parcela', []);

        $data_vencimento = $request->input('data_vencimento'); 
        $dataCarbon = Carbon::createFromFormat('Y-m-d', $data_vencimento);
        $i = 0;
        foreach($idParcelas as $p){
            $parcela = ParcelaContaReceber::find($p);
            if($i > 0){
                $parcela->data_vencimento = $dataCarbon->addMonth();
            }else{
                $parcela->data_vencimento = $data_vencimento;
            }
            $parcela->save();
            $i++;
        }
        $parcelaReferencia = ParcelaContaReceber::find($idParcelas[0]);
        $debito = Debito::find($parcelaReferencia->debito_id);
        $lote_id = $debito->lote_id;

        if($origem == "lote_gestao"){
            return redirect("lote/gestao/".$lote_id)->with('success', 'Data(s) de vencimento alteradas com sucesso');   
        } else if($origem == "contas_receber"){
            return redirect("contas_receber")->with('success', 'Data(s) de vencimento alteradas com sucesso');   
        } else if($origem == "contas_pagar"){
            return redirect("contas_pagar")->with('success', 'Data(s) de vencimento alteradas com sucesso');   
        }
    }

     //RETORNA VIEW PARA BAIXAR PARCELA
     function baixar_parcela_view(Request $request){
       
        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
             // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

            //Verificar se há parcelas já recebidas
            foreach($checkboxesSelecionados as $parcelaId) {
                $parcela = ParcelaContaReceber::find($parcelaId);

                //Se houver parcelas já recebidas redireciona de volta
                if($parcela->situacao == 1){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto! Dica: para alterar parcelas já recebidas/pagas estornar o recebimento/pagamento!');
                }
            }
            

            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela_conta_receber as p')
                ->select(
                    'p.id as id',
                    'p.numero_parcela as numero_parcela',
                    'p.data_vencimento as data_vencimento',
                    'p.valor_parcela as valor_parcela',
                    'p.situacao as situacao_parcela',
                    'd.id as debito_id',
                    'd.quantidade_parcela as debito_quantidade_parcela',
                    'd.descricao_debito_id as debito_descricao_debito_id',  
                    'dd.descricao as descricao',       
                )
                ->leftJoin('debito AS d', 'p.debito_id', '=', 'd.id')
                ->leftJoin('descricao_debito AS dd', 'd.descricao_debito_id', '=', 'dd.id')
                ->where('p.id', $parcelaId)
                ->get();
            }
            return view('parcela/parcela_baixar', compact('parcelas'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    //BAIXAR PARCELAS
    function definir_baixar_parcela($user_id, Request $request){
        $origem = $request->input('origem'); //controle para redirecionar para lugar correto

         //Transformar em formato correto para salvar no BD e validação
         $request->merge([
            'valor' => str_replace(['.', ','], ['', '.'], $request->get('valor', [])),
        ]);

        $validated = $request->validate([
            'data.*' => 'required|date',
            'valor.*' => 'required|numeric|min:0.1',
        ]);

        $idParcelas = $request->get('id_parcela', []);
        $valor = $request->get('valor', []);
        $data = $request->get('data', []);
    
        $i = 0;
        foreach ($idParcelas as $id) {
            // Process each $id here
            $parcela = ParcelaContaReceber::find($id);
            $parcela->valor_recebido = $valor[$i];
            $parcela->data_recebimento = $data[$i];
            $parcela->data_baixa = date('d-m-Y h:i:s a', time());
            $parcela->usuario_baixa_id = $user_id;
            $parcela->situacao = 1;
            $parcela->save();

            //Selecionar ID do contas a receber
            $debito_id = $parcela->debito_id;
            //Verificar vinculo com Movimentação
            $movimentacoes = MovimentacaoFinanceira::where('debito_id', $debito_id)->get();

            //Obter debito
            $debitoReceber = Debito::find($debito_id);

            //Obter descricao debito para adicionar em movimentações
            $descricao_debito = DescricaoDebito::find($debitoReceber->descricao_debito_id);

            //Se debito está relacionado a uma movimentação
            if ($movimentacoes->count() > 0) {
                
            }else{ //Se não estiver relacionado

                $movimentacao_financeira = new MovimentacaoFinanceira();

                //Pegar lote referente ao debito
                $lote = Lote::find($debitoReceber->lote_id);
                $movimentacao_financeira->cliente_fornecedor_id = $lote->cliente_id;
                $movimentacao_financeira->descricao = $descricao_debito->descricao;
                $movimentacao_financeira->data_movimentacao = $data[$i];
                $movimentacao_financeira->titular_conta_id = $debitoReceber->titular_conta_id;
                $movimentacao_financeira->conta_corrente_id = 1; //Alterar
                
                // No Banco de Dados o 'tipo_movimentacao' é boolean = False (Entrada 0) e True(Saida 1)
                // Porém no input 0 (Selecione), 1 (Entrada) e 2 (Saída)
                $movimentacao_financeira->tipo_movimentacao = 0; //Contas a Receber é Entrada
        
                $valor_corrigido = str_replace(',', '.', $valor[$i]);
                $movimentacao_financeira->valor = (double) $valor_corrigido; // Converter a string diretamente para um número em ponto flutuante
                $valor_movimentacao = (double) $valor_corrigido; //Armazenar em uma variavel o valor da movimentação
            
                $movimentacao_financeira->data_cadastro = date('d-m-Y h:i:s a', time());
                $movimentacao_financeira->cadastrado_usuario_id = $user_id;
        
                //Variavel de saldo para manipulacao e verificacao do saldo
                $saldo = SaldoDiario::where('data', $data[$i])->get(); // Saldo do dia
        
                //Se não houver saldo para aquele dia
                if(!isset($saldo[0]->saldo)){
                    //Último saldo cadastrado
                    $ultimo_saldo = SaldoDiario::orderBy('data', 'desc')->where('data', '<', $data[$i])->first();
                    
                    //Cadastrar saldo daquela data com o último saldo para depois fazer a movimentação
                    $addSaldo = new SaldoDiario();

                    //Se saldo for null
                    if($ultimo_saldo == null){
                        $addSaldo->saldo = 0;
                    }else{
                        $addSaldo->saldo = $ultimo_saldo->saldo;
                    }
                    $addSaldo->data = $data[$i];
                    $addSaldo->data_cadastro = date('d-m-Y h:i:s a', time());
                    $addSaldo->save();
        
                    $saldo = $addSaldo;
                    $valor_desatualizado_saldo =  $saldo->saldo; //Armazenar o ultimo saldo
        
                }else{//Caso houver saldo para aquele dia
                    $valor_desatualizado_saldo =  $saldo[0]->saldo; //Armazenar o ultimo saldo
                }
        
                //variavel que será responsavel por alterar-lo
                $saldo_model = SaldoDiario::where('data', $data[$i])->first();

                //Atualizando o saldo
                $saldo_model->saldo = $valor_desatualizado_saldo + $valor_movimentacao; 
                $saldo_model->save();

                //Vincular Conta com Movimentacao
                $movimentacao_financeira->debito_id = $debitoReceber->id;
    
                //salvar movimentação
                $movimentacao_financeira->save();
            }

            $i++;
        }
       
        $parcelaReferencia = ParcelaContaReceber::find($idParcelas[0]);
        $debito = Debito::find($parcelaReferencia->debito_id);
        $lote_id = $debito->lote_id;
        if($origem == "lote_gestao"){
            return redirect("lote/gestao/".$lote_id)->with('success', 'Parcelas baixadas com sucesso'); 
        } else if($origem == "contas_receber"){
            return redirect("contas_receber")->with('success', 'Parcelas baixadas com sucesso');   
        }else if($origem == "contas_pagar"){
            return redirect("contas_pagar")->with('success', 'Parcelas baixadas com sucesso');   
        }
   
    }

}