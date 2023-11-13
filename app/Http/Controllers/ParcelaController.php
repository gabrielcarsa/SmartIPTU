<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Parcela;
use App\Models\Debito;
use App\Models\TitularConta;
use App\Http\Requests\ParcelaRequest;
use Carbon\Carbon;

class ParcelaController extends Controller
{
    //RETORNA VIEW PARA REAJUSTAR PARCELA
    function reajustar_view(Request $request){

        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
             // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

            //Verificar se há parcelas já recebidas
            foreach($checkboxesSelecionados as $parcelaId) {
                $parcela = Parcela::find($parcelaId);

                //Se houver parcelas já recebidas redireciona de volta
                if($parcela->situacao == 1){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto! Dica: para alterar parcelas já recebidas/pagas estornar o recebimento/pagamento!');
                }
            }
            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela as p')
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
            $parcela = Parcela::find($p);

            $valor_parcela = str_replace(',', '.', $request->input('valor_unico'));
            $parcela->valor_parcela = (double) number_format($valor_parcela, 2, '.', '');  

            $parcela->save();
        }
        $parcelaReferencia = Parcela::find($idParcelas[0]);
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
                $parcela = Parcela::find($parcelaId);

                //Se houver parcelas já recebidas redireciona de volta
                if($parcela->situacao == 1){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto! Dica: para alterar parcelas já recebidas/pagas estornar o recebimento/pagamento!');
                }
            }
    
            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela as p')
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
            $parcela = Parcela::find($p);
            if($i > 0){
                $parcela->data_vencimento = $dataCarbon->addMonth();
            }else{
                $parcela->data_vencimento = $data_vencimento;
            }
            $parcela->save();
            $i++;
        }
        $parcelaReferencia = Parcela::find($idParcelas[0]);
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
                $parcela = Parcela::find($parcelaId);

                //Se houver parcelas já recebidas redireciona de volta
                if($parcela->situacao == 1){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto! Dica: para alterar parcelas já recebidas/pagas estornar o recebimento/pagamento!');
                }
            }
            

            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela as p')
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
            $parcela = Parcela::find($id);
            $parcela->valor_pago = $valor[$i];
            $parcela->data_recebimento = $data[$i];
            $parcela->data_baixa = date('d-m-Y h:i:s a', time());
            $parcela->usuario_baixa_id = $user_id;
            $parcela->situacao = 1;
            $parcela->save();
            $i++;
        }
       
        $parcelaReferencia = Parcela::find($idParcelas[0]);
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