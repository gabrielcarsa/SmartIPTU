<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TitularConta;
use App\Models\ContaReceber;
use App\Models\ParcelaContaReceber;
use App\Models\Cliente;
use App\Models\Parcela;
use Carbon\Carbon;
use App\Models\CategoriaReceber;
use App\Http\Requests\ContaReceberRequest;
use Illuminate\Support\Facades\DB;


class ContaReceberController extends Controller
{
    //RETORNA VIEW PARA CADASTRO DE NOVA CONTA A RECEBER
    function conta_receber_novo(){
        $titular_conta = DB::table('titular_conta as t')
        ->select(
            't.id as id_titular_conta',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();

        $categorias = CategoriaReceber::all();

        $clientes = Cliente::all();

        $data = [
            'titular_conta' => $titular_conta,
            'categorias' =>$categorias,
            'clientes' => $clientes,
        ];
        return view('conta_receber/conta_receber_novo', compact('data'));
    }

     //CADASTRO DE CONTA A RECEBER
     function cadastrar($usuario, Request $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $contaReceber = new ContaReceber();
        $contaReceber->titular_conta_id = $request->input('titular_conta_id');
        $contaReceber->cliente_id = $request->input('cliente_id');
        $contaReceber->categoria_receber_id = $request->input('categoria_receber_id');
        $contaReceber->quantidade_parcela = $request->input('quantidade_parcela');
        $contaReceber->data_vencimento = $request->input('data_vencimento');
        $contaReceber->valor_parcela = $request->input('valor_parcela');
        $contaReceber->valor_entrada = $request->input('valor_entrada');
        $contaReceber->observacao = $request->input('observacao');
        $contaReceber->data_cadastro = date('d-m-Y h:i:s a', time());
        $contaReceber->cadastrado_usuario_id = $usuario;
        $contaReceber->save();

        // Cadastrar Parcelas
        $qtd_parcelas = $request->input('quantidade_parcela');
        $contaReceber_id = $contaReceber->id;
        $data_vencimento = $contaReceber->data_vencimento; 
        $dataCarbon = Carbon::createFromFormat('Y-m-d', $data_vencimento);
        $valor_entrada = $contaReceber->valor_entrada;

        for($i = 1; $i <= $qtd_parcelas; $i++){
            $parcela = new ParcelaContaReceber();
            $parcela->conta_receber_id = $contaReceber_id;
            $parcela->numero_parcela = $i;
            $parcela->valor_parcela = $contaReceber->valor_parcela;
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

        return redirect('contas_receber')->with('success', 'Nova receita cadastrada com sucesso');
    }

    //VIEW PARA RETORNAR FINANCEIRO CONTAS A RECEBER
    function contas_receber(){
        $titular_conta = DB::table('titular_conta as t')
        ->select(
            't.id as id_titular_conta',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();

        return view('conta_receber/contas_receber', compact('titular_conta'));
    }

    //LISTAGEM E FILTRO CONTAS A RECEBER
    function contas_receber_listagem(ContaReceberRequest $request){

        //Campos
        $titular_conta_id = $request->input('titular_conta_id');
        $isReferenteLotes = $request->input('referenteLotes');
        $isReferenteOutros = $request->input('referenteOutros');
        $periodoDe = $request->input('periodoDe');
        $periodoAte = $request->input('periodoAte');
        $isPeriodoVencimento = $request->input('periodoVencimento');
        $isPeriodoBaixa = $request->input('periodoBaixa');
        $isPeriodoLancamento = $request->input('periodoLancamento');
        $isPeriodoRecebimento = $request->input('periodoRecebimento');

    

        //select referente a parcelas de contas a receber de lotes
        $queryReferenteLotes = DB::table('parcela as p')
        ->select( 
            'p.id as id',
            'p.numero_parcela as numero_parcela',
            'p.data_vencimento as data_vencimento',
            'p.valor_parcela as valor_parcela',
            'p.situacao as situacao_parcela',
            'p.valor_pago as parcela_valor_pago',
            'p.data_recebimento as data_recebimento',
            'p.data_baixa as data_baixa',
            'p.cadastrado_usuario_id as parcela_cadastrado_usuario_id',
            'p.alterado_usuario_id as parcela_alterado_usuario_id',
            'p.usuario_baixa_id as parcela_usuario_baixa_id',
            'p.data_alteracao as parcela_data_alteracao',
            'd.quantidade_parcela as quantidade_parcela',
            'dd.descricao as descricao',  
            'c.nome as nome',
            'c.tipo_cadastro as tipo_cadastro',
            'c.razao_social as razao_social',
            'tpd.descricao as tipo_debito_descricao', 
            'l.lote as lote',
            'l.inscricao_municipal as inscricao',
            'e.nome as empreendimento',
            'q.nome as quadra',
            'uc.name as cadastrado_por',
            DB::raw('COALESCE(ua.name) as alterado_por'),
            DB::raw('COALESCE(ub.name) as baixado_por'))
        ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
        ->join('debito as d', 'p.debito_id', '=', 'd.id')
        ->join('lote as l', 'd.lote_id', '=', 'l.id')
        ->join('quadra as q', 'l.quadra_id', '=', 'q.id')
        ->join('empreendimento as e', 'q.empreendimento_id', '=', 'e.id')
        ->join('cliente as c', 'l.cliente_id', '=', 'c.id')
        ->join('descricao_debito as dd', 'd.descricao_debito_id', '=', 'dd.id')
        ->join('titular_conta as td', 'd.titular_conta_id', '=', 'td.id')
        ->join('tipo_debito as tpd', 'd.tipo_debito_id', '=', 'tpd.id')
        ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id')
        ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id') // Usuario que cadastrou a parcela
        ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id') // Usuário que alterou, usando LEFT JOIN para permitir nulos
        ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id') // Usuário que baixou, usando LEFT JOIN para permitir nulos
        ->whereColumn('l.cliente_id', '<>', 'td.cliente_id');

        //select referente a parcelas de outras contas a receber
        $queryReferenteOutros = DB::table('parcela_conta_receber as p')
        ->select(
            'p.id as id',
            'p.numero_parcela as numero_parcela',
            'p.data_vencimento as data_vencimento',
            'p.valor_parcela as valor_parcela',
            'p.situacao as situacao_parcela',
            'p.valor_pago as parcela_valor_pago',
            'p.data_recebimento as data_recebimento',
            'p.data_baixa as data_baixa',
            'p.cadastrado_usuario_id as parcela_cadastrado_usuario_id',
            'p.alterado_usuario_id as parcela_alterado_usuario_id',
            'p.usuario_baixa_id as parcela_usuario_baixa_id',
            'p.data_alteracao as parcela_data_alteracao',
            'cr.quantidade_parcela as quantidade_parcela',
            'ctr.descricao as descricao',
            'c.nome as nome',
            'c.tipo_cadastro as tipo_cadastro',
            'c.razao_social as razao_social',
            'uc.name as cadastrado_por',
            DB::raw('COALESCE(ua.name) as alterado_por'),
            DB::raw('COALESCE(ub.name) as baixado_por'),
        )
        ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
        ->join('conta_receber as cr', 'p.conta_receber_id', '=', 'cr.id')
        ->join('cliente as c', 'cr.cliente_id', '=', 'c.id')
        ->join('categoria_receber as ctr', 'cr.categoria_receber_id', '=', 'ctr.id')
        ->join('titular_conta as td', 'cr.titular_conta_id', '=', 'td.id')
        ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id')
        ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id')
        ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id')
        ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id');

        
        if ($isReferenteLotes) { //Referente a Lotes

            if($titular_conta_id == 0){ //Se o titular da conta for 'Todos'

                if(!empty($periodoDe) && !empty($periodoAte) && $isPeriodoVencimento){ //Verifica período e Vencimento
                    $resultados = $queryReferenteLotes
                    ->where('p.data_vencimento', '>', $periodoDe)
                    ->where('p.data_vencimento', '<', $periodoAte)
                    ->orderBy('data_vencimento', 'ASC') 
                    ->get();
                }else{ //Busca todos períodos referente a Lotes
                    $resultados = $queryReferenteLotes
                    ->orderBy('data_vencimento', 'ASC') 
                    ->get();
                }

            }else{ //Se o titular da conta for específico

                $resultados = $queryReferenteLotes
                ->where('d.titular_conta_id', $titular_conta_id)
                ->orderBy('data_vencimento', 'ASC') 
                ->get();
            } 
        
        } else { //Referente a outras receitas

            if($titular_conta_id == 0){ //Se o titular da conta for 'Todos'

                if(!empty($periodoDe) && !empty($periodoAte) && $isPeriodoVencimento){ //Verifica período e Vencimento

                    $resultados = $queryReferenteOutros
                    ->where('p.data_vencimento', '>', $periodoDe)
                    ->where('p.data_vencimento', '<', $periodoAte)
                    ->orderBy('data_vencimento', 'ASC') 
                    ->get();

                } else{ //Busca todos períodos referente a Lotes

                    $resultados = $queryReferenteOutros
                    ->orderBy('p.data_vencimento', 'ASC')
                    ->get();
                }

            }else{ //Se o titular da conta for específico

                $resultados = $queryReferenteOutros
                ->where('cr.titular_conta_id', $titular_conta_id)
                ->orderBy('p.data_vencimento', 'ASC')
                ->get();
            }      
        } 

        $titular_conta = DB::table('titular_conta as t')
        ->select(
            't.id as id_titular_conta',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();

        // Inicialize uma variável para armazenar o valor total
        $totalValorParcelas = 0;
        $totalValorPago = 0;

        // Percorra a coleção de resultados
        foreach ($resultados as $resultado) {
          $totalValorParcelas += $resultado->valor_parcela;

            // Verifique se a situação da parcela é igual a 1 (Pago)
            if ($resultado->situacao_parcela == 1) {
                // Adicione o valor da parcela ao valor total
                $totalValorPago += $resultado->valor_parcela;
            }
        }
    
        $data = [
            'resultados' => $resultados,
            'isReferenteLotes' => $isReferenteLotes, 
            'totalValorPago' => $totalValorPago,
            'totalValorParcelas' => $totalValorParcelas,
        ];
    
        return view('conta_receber/contas_receber', compact('titular_conta', 'data'));
    }

     //RETORNA VIEW PARA REAJUSTAR PARCELA
     function reajustar_view(Request $request){

        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
             // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 
    
            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela_conta_receber as p')
                ->select(
                    'p.id as id',
                    'p.numero_parcela as numero_parcela',
                    'p.data_vencimento as data_vencimento',
                    'p.valor_parcela as valor_parcela',
                    'p.situacao as situacao_parcela',
                    'cr.id as conta_receber_id',
                    'cr.quantidade_parcela as debito_quantidade_parcela',
                    'ccr.descricao as descricao',       
                )
                ->leftJoin('conta_receber AS cr', 'p.conta_receber_id', '=', 'cr.id')
                ->leftJoin('categoria_receber AS ccr', 'cr.categoria_receber_id', '=', 'ccr.id')
                ->where('p.id', $parcelaId)
                ->get();
            }
            $parcelaOutros = true;
            return view('parcela/parcela_reajustar', compact('parcelas', 'parcelaOutros'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    //REAJUSTAR PARCELAS
    function reajustar($user_id, Request $request){
        $validated = $request->validate([
            'valor_unico' => 'required|numeric|min:0.1',
        ]);

        $idParcelas = $request->get('id_parcela', []);

        foreach($idParcelas as $p){
            $parcela = ParcelaContaReceber::find($p);
            $parcela->valor_parcela = $request->input('valor_unico');
            $parcela->save();
        }
             
        return redirect("contas_receber")->with('success', 'Parcelas reajustadas com sucesso');   

    }

      //RETORNA VIEW PARA ALTERAR DATA DE VENCIMENTO
      function alterar_vencimento(Request $request){
       
        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
             // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

    
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

        $validated = $request->validate([
            'data_recebimento.*' => 'required|date',
            'valor_pago.*' => 'required|numeric|min:0.1',
        ]);

        $idParcelas = $request->get('id_parcela', []);
        $valorPago = $request->get('valor_pago', []);
        $dataRecebimento = $request->get('data_recebimento', []);
    
        $i = 0;
        foreach ($idParcelas as $id) {
            // Process each $id here
            $parcela = Parcela::find($id);
            $parcela->valor_pago = $valorPago[$i];
            $parcela->data_recebimento = $dataRecebimento[$i];
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