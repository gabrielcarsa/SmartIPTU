<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TitularConta;
use App\Models\ContaPagar;
use App\Models\ParcelaContaPagar;
use App\Models\SaldoDiario;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use App\Models\MovimentacaoFinanceira;
use App\Models\CategoriaPagar;
use App\Http\Requests\ContaPagarRequest;
use App\Models\TipoDebito;
use Carbon\Carbon;


class ContaPagarController extends Controller
{
    //VIEW PARA RETORNAR FINANCEIRO CONTAS A PAGAR
    function contas_pagar(){
        $titular_conta = DB::table('titular_conta as t')
        ->select(
            't.id as id_titular_conta',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();

        $categoria = CategoriaPagar::all();

        $tipo_debito = TipoDebito::all();

        return view('conta_pagar/contas_pagar', compact('titular_conta', 'categoria'), compact('tipo_debito'));
    }

    //RETORNA VIEW PARA CADASTRO DE NOVA DESPESA
    function conta_pagar_novo(){
        $titular_conta = DB::table('titular_conta as t')
        ->select(
            't.id as id_titular_conta',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();

        $categorias = CategoriaPagar::all();

        $clientes = Cliente::orderBy('nome')->get();

        $data = [
            'titular_conta' => $titular_conta,
            'categorias' =>$categorias,
            'clientes' => $clientes,
        ];
        return view('conta_pagar/conta_pagar_novo', compact('data'));
    }

    //CADASTRO DE CONTA A PAGAR
    function cadastrar($usuario, Request $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');   
        
        $request->merge([
            'valor_parcela' => str_replace(['.', ','], ['', '.'], $request->input('valor_parcela')),
            'valor_entrada' => str_replace(['.', ','], ['', '.'], $request->input('valor_entrada')),
        ]);

        $validated = $request->validate([
            'quantidade_parcela' => 'required|numeric',
            'cliente_id' => 'required|numeric|min:1',
            'categoria_pagar_id' => 'required|numeric|min:1',
            'valor_parcela' => 'required|numeric',
            'data_vencimento' => 'required|date',
            'valor_entrada' => 'nullable|numeric',
        ]);

        $contaPagar = new ContaPagar();
        $contaPagar->titular_conta_id = $request->input('titular_conta_id');
        $contaPagar->fornecedor_id = $request->input('cliente_id');
        $contaPagar->categoria_pagar_id = $request->input('categoria_pagar_id');
        $contaPagar->quantidade_parcela = $request->input('quantidade_parcela');
        $contaPagar->data_vencimento = $request->input('data_vencimento');

        $valor_parcela = str_replace(',', '.', $request->input('valor_parcela'));
        $contaPagar->valor_parcela = (double) $valor_parcela; // Converter a string diretamente para um número em ponto flutuante
      
        $valor_entrada = str_replace(',', '.', $request->input('valor_entrada'));
        $contaPagar->valor_entrada = (double) $valor_entrada; // Converter a string diretamente para um número em ponto flutuante

        $contaPagar->descricao = $request->input('descricao');
        $contaPagar->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
        $contaPagar->cadastrado_usuario_id = $usuario;
        $contaPagar->save();

        // Cadastrar Parcelas
        $qtd_parcelas = $request->input('quantidade_parcela');
        $contaPagar_id = $contaPagar->id;
        $data_vencimento = $contaPagar->data_vencimento; 
        $dataCarbon = Carbon::createFromFormat('Y-m-d', $data_vencimento);
        $valor_entrada = $contaPagar->valor_entrada;

        for($i = 1; $i <= $qtd_parcelas; $i++){
            $parcela = new ParcelaContaPagar();
            $parcela->conta_pagar_id = $contaPagar_id;
            $parcela->numero_parcela = $i;
            $parcela->situacao = 0;
            $parcela->valor_parcela = $contaPagar->valor_parcela;
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

        return redirect('contas_pagar')->with('success', 'Nova Despesa cadastrada com sucesso');
    }

      
    //LISTAGEM E FILTRO CONTAS A PAGAR
    function contas_pagar_listagem(ContaPagarRequest $request){

        //Campos
        $titular_conta_id = $request->input('titular_conta_id');
        $isReferenteLotes = $request->input('referenteLotes');
        $isReferenteOutros = $request->input('referenteOutros');
        $isSituacaoVencer = $request->input('situacaoVencer');
        $isSituacaoPago = $request->input('situacaoPago');
        $isSituacaoTodos = $request->input('situacaoTodos');
        $periodoDe = $request->input('periodoDe');
        $periodoAte = $request->input('periodoAte');
        $isPeriodoVencimento = $request->input('periodoVencimento');
        $isPeriodoBaixa = $request->input('periodoBaixa');
        $isPeriodoLancamento = $request->input('periodoLancamento');
        $isPeriodoRecebimento = $request->input('periodoRecebimento');
        $idParcela = $request->input('idParcela');
        $categoria = $request->input('categoria');
        $tipo_debito = $request->input('tipo_debito');
    

        //select referente a parcelas de contas a pagar de lotes
        $queryReferenteLotes = DB::table('parcela_conta_pagar as p')
        ->select( 
            'p.id as id',
            'p.numero_parcela as numero_parcela',
            'p.data_vencimento as data_vencimento',
            'p.valor_parcela as valor_parcela',
            'p.situacao as situacao_parcela',
            'p.valor_pago as parcela_valor_pago',
            'p.data_pagamento as data_pagamento',
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
            'c.telefone1 as tel1',
            'c.telefone2 as tel2',
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
        ->join('descricao_debito as dd', 'p.descricao_debito_id', '=', 'dd.id')
        ->join('titular_conta as td', 'd.titular_conta_id', '=', 'td.id')
        ->join('tipo_debito as tpd', 'd.tipo_debito_id', '=', 'tpd.id')
        ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id')
        ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id') // Usuario que cadastrou a parcela
        ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id') // Usuário que alterou, usando LEFT JOIN para permitir nulos
        ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id') // Usuário que baixou, usando LEFT JOIN para permitir nulos
        ->orderBy('data_vencimento', 'ASC');


        //select referente a parcelas de outras contas a pagar
        $queryReferenteOutros = DB::table('parcela_conta_pagar as p')
        ->select(
            'p.id as id',
            'p.numero_parcela as numero_parcela',
            'p.data_vencimento as data_vencimento',
            'p.valor_parcela as valor_parcela',
            'p.situacao as situacao_parcela',
            'p.valor_pago as parcela_valor_pago',
            'p.data_pagamento as data_pagamento',
            'p.data_baixa as data_baixa',
            'p.cadastrado_usuario_id as parcela_cadastrado_usuario_id',
            'p.alterado_usuario_id as parcela_alterado_usuario_id',
            'p.usuario_baixa_id as parcela_usuario_baixa_id',
            'p.data_alteracao as parcela_data_alteracao',
            'cp.quantidade_parcela as quantidade_parcela',
            'cp.descricao as descricao',
            'ctp.descricao as categoria',
            'c.nome as nome',
            'c.tipo_cadastro as tipo_cadastro',
            'c.razao_social as razao_social',
            'c.telefone1 as tel1',
            'c.telefone2 as tel2',
            'uc.name as cadastrado_por',
            DB::raw('COALESCE(ua.name) as alterado_por'),
            DB::raw('COALESCE(ub.name) as baixado_por'),
        )
        ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
        ->join('conta_pagar as cp', 'p.conta_pagar_id', '=', 'cp.id')
        ->join('cliente as c', 'cp.fornecedor_id', '=', 'c.id')
        ->join('categoria_pagar as ctp', 'cp.categoria_pagar_id', '=', 'ctp.id')
        ->join('titular_conta as td', 'cp.titular_conta_id', '=', 'td.id')
        ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id')
        ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id')
        ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id')
        ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id')
        ->orderBy('data_vencimento', 'ASC'); 

        /*----------
        FILTRO
        ----------*/
        if ($isReferenteLotes) { //Referente a Lotes
            if($titular_conta_id == 0){ //Se o titular da conta for 'Todos'

                if(!empty($periodoDe) && !empty($periodoAte) && $isPeriodoVencimento){ //Verifica período e Vencimento

                    $resultados = $queryReferenteLotes
                    ->where('p.data_vencimento', '>=', $periodoDe)
                    ->where('p.data_vencimento', '<=', $periodoAte);

                }elseif(!empty($idParcela)){ //Busca por ID da parcela se for referente a Lotes e Todos titulares

                    $resultados = $queryReferenteLotes
                    ->where('p.id', '=', $idParcela);

                }else{ //Se não houver nenhum período e nenhuma parcela específica

                    $resultados = $queryReferenteLotes->get();
                
                }

                if($isSituacaoVencer){ // Parcelas A VENCER

                    $resultados = $queryReferenteLotes
                    ->where('p.situacao', '=', 0);

                }else if($isSituacaoPago){ // Parcelas PAGAS

                    $resultados = $queryReferenteLotes
                    ->where('p.situacao', '=', 1);

                }

                if($tipo_debito != 0) { // Tipo do Débito

                    $resultados = $queryReferenteLotes
                    ->where('d.tipo_debito_id', '=', $tipo_debito);

                }

                $resultados = $queryReferenteLotes->get();

            }else{ //Se o titular da conta for específico

                if(!empty($periodoDe) && !empty($periodoAte) && $isPeriodoVencimento){ //Verifica período e Vencimento

                    $resultados = $queryReferenteLotes
                    ->where('p.data_vencimento', '>=', $periodoDe)
                    ->where('p.data_vencimento', '<=', $periodoAte)
                    ->where('d.titular_conta_id', $titular_conta_id);

                }elseif(!empty($idParcela)){ //Busca por ID da parcela se for referente a Lotes e titular especifico

                    $resultados = $queryReferenteLotes
                    ->where('p.id', '=', $idParcela)
                    ->where('d.titular_conta_id', $titular_conta_id);

                }else{ //Se não houver nenhum período e nenhuma parcela específica mas com titular especifico

                    $resultados = $queryReferenteLotes
                    ->where('d.titular_conta_id', $titular_conta_id);

                }

                if($isSituacaoVencer){ // Parcelas A VENCER

                    $resultados = $queryReferenteLotes
                    ->where('p.situacao', '=', 0);

                }else if($isSituacaoPago){ // Parcelas PAGAS

                    $resultados = $queryReferenteLotes
                    ->where('p.situacao', '=', 1);

                }

                if($tipo_debito != 0) { // Tipo do Débito

                    $resultados = $queryReferenteLotes
                    ->where('d.tipo_debito_id', '=', $tipo_debito);

                }

                $resultados = $queryReferenteLotes->get();
               
            } 
        
        } 

        //Referente a outras despesas

        else { 
            if($titular_conta_id == 0){ //Se o titular da conta for 'Todos'

                if(!empty($periodoDe) && !empty($periodoAte) && $isPeriodoVencimento){ //Verifica período e Vencimento

                    $resultados = $queryReferenteOutros
                    ->where('p.data_vencimento', '>=', $periodoDe)
                    ->where('p.data_vencimento', '<=', $periodoAte);

                } elseif(!empty($idParcela)){ //Busca por ID da parcela se for referente a outros e Todos titulares

                    $resultados = $queryReferenteOutros
                    ->where('p.id', '=', $idParcela);

                } else{ //Busca todos períodos referente a outras despesas

                    $resultados = $queryReferenteOutros->get();

                }

                if($isSituacaoVencer){ // Parcelas A VENCER

                    $resultados = $queryReferenteOutros
                    ->where('p.situacao', '=', 0);

                }else if($isSituacaoPago){ // Parcelas PAGAS

                    $resultados = $queryReferenteOutros
                    ->where('p.situacao', '=', 1);

                }

                if($categoria != 0) { // Categoria do Conta a Pagar

                    $resultados = $queryReferenteOutros
                    ->where('cp.categoria_pagar_id', '=', $categoria);

                }

                $resultados = $queryReferenteOutros->get();

            }else{ //Se o titular da conta for específico
                
                if(!empty($periodoDe) && !empty($periodoAte) && $isPeriodoVencimento){ //Verifica período e Vencimento

                    $resultados = $queryReferenteOutros
                    ->where('p.data_vencimento', '>=', $periodoDe)
                    ->where('p.data_vencimento', '<=', $periodoAte)
                    ->where('cp.titular_conta_id', $titular_conta_id);

                } elseif(!empty($idParcela)){ //Busca por ID da parcela se for referente a outros e titular específico

                    $resultados = $queryReferenteOutros
                    ->where('p.id', '=', $idParcela)
                    ->where('cp.titular_conta_id', $titular_conta_id);

                }
                else{ //Busca todos períodos referente a outras receitas e titular específico

                    $resultados = $queryReferenteOutros
                    ->where('cp.titular_conta_id', $titular_conta_id);

                }

                if($isSituacaoVencer){ // Parcelas A VENCER

                    $resultados = $queryReferenteOutros
                    ->where('p.situacao', '=', 0);

                }else if($isSituacaoPago){ // Parcelas PAGAS

                    $resultados = $queryReferenteOutros
                    ->where('p.situacao', '=', 1);

                }

                if($categoria != 0) { // Categoria do Conta a Pagar

                    $resultados = $queryReferenteOutros
                    ->where('cp.categoria_pagar_id', '=', $categoria);

                }

                $resultados = $queryReferenteOutros->get();
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

        $categoria = CategoriaPagar::all();

        $tipo_debito = TipoDebito::all();

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

        return view('conta_pagar/contas_pagar', compact('titular_conta', 'data'), compact('categoria', 'tipo_debito'));
    }

       //RETORNA VIEW PARA REAJUSTAR PARCELA
       function reajustar_view(Request $request){

        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
             // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

            //Verificar se há parcelas pagas
            foreach($checkboxesSelecionados as $parcelaId) {
                $parcela = ParcelaContaPagar::find($parcelaId);

                //Se houver parcelas pagas redireciona de volta
                if($parcela->situacao == 1){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto! Dica: para alterar parcelas já pagas estornar o pagamento!');
                }
            }

            $parcelas = [];

            //Select nas parcelas
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela_conta_pagar as p')
                ->select(
                    'p.id as id',
                    'p.numero_parcela as numero_parcela',
                    'p.data_vencimento as data_vencimento',
                    'p.valor_parcela as valor_parcela',
                    'p.situacao as situacao_parcela',
                    'cp.id as conta_id',
                    'cp.quantidade_parcela as debito_quantidade_parcela',
                    'ccp.descricao as descricao',       
                )
                ->leftJoin('conta_pagar AS cp', 'p.conta_pagar_id', '=', 'cp.id')
                ->leftJoin('categoria_pagar AS ccp', 'cp.categoria_pagar_id', '=', 'ccp.id')
                ->where('p.id', $parcelaId)
                ->get();
            }
            $parcelaPagarOutros = true;
            return view('parcela/parcela_reajustar', compact('parcelas', 'parcelaPagarOutros'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    //REAJUSTAR PARCELAS
    function reajustar($user_id, Request $request){
        //Transformar em formato correto para salvar no BD e validação
        $request->merge([
            'valor_unico' => str_replace(['.', ','], ['', '.'], $request->get('valor_unico')),
        ]);

        $validated = $request->validate([
            'valor_unico' => 'required|numeric|min:0.1',
        ]);

        $idParcelas = $request->get('id_parcela', []);

        foreach($idParcelas as $p){
            $parcela = ParcelaContaPagar::find($p);
            $parcela->valor_parcela = $request->input('valor_unico');
            $parcela->save();
        }
             
        return redirect("contas_pagar")->with('success', 'Parcelas reajustadas com sucesso');   

    }

      //RETORNA VIEW PARA ALTERAR DATA DE VENCIMENTO
      function alterar_vencimento(Request $request){
       
        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
             // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados);
            
            //Verificar se há parcelas pagas
            foreach($checkboxesSelecionados as $parcelaId) {
                $parcela = ParcelaContaPagar::find($parcelaId);

                //Se houver parcelas pagas redireciona de volta
                if($parcela->situacao == 1){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto! Dica: para alterar parcelas já pagas estornar o pagamento!');
                }
            }


            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela_conta_pagar as p')
                ->select(
                    'p.id as id',
                    'p.numero_parcela as numero_parcela',
                    'p.data_vencimento as data_vencimento',
                    'p.valor_parcela as valor_parcela',
                    'p.situacao as situacao_parcela',
                    'cp.id as conta_receber_id',
                    'cp.quantidade_parcela as debito_quantidade_parcela',
                    'ccp.descricao as descricao',       
                )
                ->leftJoin('conta_pagar AS cp', 'p.conta_pagar_id', '=', 'cp.id')
                ->leftJoin('categoria_pagar AS ccp', 'cp.categoria_pagar_id', '=', 'ccp.id')
                ->where('p.id', $parcelaId)
                ->get();
            }
            $parcelaPagarOutros = true;
            return view('parcela/parcela_alterar_vencimento', compact('parcelas', 'parcelaPagarOutros'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    //ALTERAR DATA DE VENCIMENTO
    function definir_alteracao_data($user_id, Request $request){
        
        $validated = $request->validate([
            'data_vencimento' => 'required|date',
        ]);

        $idParcelas = $request->get('id_parcela', []);

        $data_vencimento = $request->input('data_vencimento'); 
        $dataCarbon = Carbon::createFromFormat('Y-m-d', $data_vencimento);
        $i = 0;
        foreach($idParcelas as $p){
            $parcela = ParcelaContaPagar::find($p);
            if($i > 0){
                $parcela->data_vencimento = $dataCarbon->addMonth();
            }else{
                $parcela->data_vencimento = $data_vencimento;
            }
            $parcela->save();
            $i++;
        }

        return redirect("contas_pagar")->with('success', 'Data(s) de vencimento alteradas com sucesso');   
    }

     //RETORNA VIEW PARA BAIXAR PARCELA
     function baixar_parcela_view(Request $request){
       
        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
            // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

            //Verificar se há parcelas pagas
            foreach($checkboxesSelecionados as $parcelaId) {
                $parcela = ParcelaContaPagar::find($parcelaId);

                //Se houver parcelas pagas redireciona de volta
                if($parcela->situacao == 1){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto! Dica: para alterar parcelas já pagas estornar o pagamento!');
                }
            }

            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela_conta_pagar as p')
                ->select(
                    'p.id as id',
                    'p.numero_parcela as numero_parcela',
                    'p.data_vencimento as data_vencimento',
                    'p.valor_parcela as valor_parcela',
                    'p.situacao as situacao_parcela',
                    'cp.id as conta_receber_id',
                    'cp.quantidade_parcela as debito_quantidade_parcela',
                    'ccp.descricao as descricao',       
                )
                ->leftJoin('conta_pagar AS cp', 'p.conta_pagar_id', '=', 'cp.id')
                ->leftJoin('categoria_pagar AS ccp', 'cp.categoria_pagar_id', '=', 'ccp.id')
                ->where('p.id', $parcelaId)
                ->get();
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

            $parcelaPagarOutros = true;

            $data = [
                'titular_conta' => $titular_conta,
                'parcelaPagarOutros' => $parcelaPagarOutros,
            ];

            return view('parcela/parcela_baixar', compact('parcelas', 'data'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    //BAIXAR PARCELAS
    function definir_baixar_parcela($user_id, Request $request){

        //Transformar em formato correto para salvar no BD e validação
        $request->merge([
            'valor' => str_replace(['.', ','], ['', '.'], $request->get('valor', [])),
        ]);

        //Validação
        $validated = $request->validate([
            'data.*' => 'required|date',
            'valor.*' => 'required|numeric',
        ]);

        $idParcelas = $request->get('id_parcela', []);
        $valorPago = $request->get('valor', []);
        $dataPagamento = $request->get('data', []);
        $ordem = $request->get('ordem', []);

        //Verificar para não ser possível dar baixa com datas futuras
        foreach ($dataPagamento as $d) {
            if (strtotime($d) > strtotime(date('Y-m-d'))) {
                return redirect()->back()->with('error', 'Não é possível baixar com datas futuras!');
            }
        }   
    
        $i = 0;
        foreach ($idParcelas as $id) {
            $parcela = ParcelaContaPagar::find($id);

            $valor = str_replace(',', '.', $valorPago[$i]);
            $parcela->data_pagamento = $dataPagamento[$i];
            $parcela->data_baixa = Carbon::now()->format('Y-m-d H:i:s');
            $parcela->usuario_baixa_id = $user_id;
            if (request()->has('baixa_parcial')) {
                // O checkbox está selecionado
                $parcela->situacao = 2;
                $parcela->valor_pago += (double) $valor; // Converter a string diretamente para um número em ponto flutuante
            } else {
                // O checkbox não está selecionado
                $parcela->situacao = 1;
                $parcela->valor_pago = (double) $valor;
            }

             //Selecionar ID do contas a pagar
             $conta_pagar_id = $parcela->conta_pagar_id;
             
             //Obter titular da conta
             $contaPagar = ContaPagar::find($conta_pagar_id);
 
            //Se a conta está relacionada a uma movimentação
            //if ($parcela->movimentacao_financeira_id != null) {
                
           // }else{ //Se não estiver relacionado
 
                $movimentacao_financeira = new MovimentacaoFinanceira();
                $movimentacao_financeira->cliente_fornecedor_id = $contaPagar->fornecedor_id;
                $movimentacao_financeira->descricao = $contaPagar->descricao;
                $movimentacao_financeira->data_movimentacao = $dataPagamento[$i];
                $movimentacao_financeira->ordem = $ordem[$i];
                $movimentacao_financeira->titular_conta_id = $request->input('titular_conta_id');
                $movimentacao_financeira->conta_corrente_id = $request->input('conta_corrente_id');
                
                // No Banco de Dados o 'tipo_movimentacao' é boolean = False (Entrada 0) e True(Saida 1)
                // Porém no input 0 (Selecione), 1 (Entrada) e 2 (Saída)
                $movimentacao_financeira->tipo_movimentacao = 1; //Contas a Pagar é Saida
        
                $valor = str_replace(',', '.', $valorPago[$i]);
                $movimentacao_financeira->valor = (double) $valor; // Converter a string diretamente para um número em ponto flutuante
                $valor_movimentacao = (double) $valor; //Armazenar em uma variavel o valor da movimentação
            
                $movimentacao_financeira->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                $movimentacao_financeira->cadastrado_usuario_id = $user_id;
        
                //Variavel de saldo para manipulacao e verificacao do saldo
                $saldo = SaldoDiario::where('data', $dataPagamento[$i])
                ->where('titular_conta_id', $request->input('titular_conta_id'))
                ->where('conta_corrente_id', $request->input('conta_corrente_id'))
                ->get(); // Saldo do dia

        
                //Se não houver saldo para aquele dia
                if(!isset($saldo[0]->saldo)){
                    //Último saldo cadastrado
                    $ultimo_saldo = SaldoDiario::orderBy('data', 'desc')
                    ->where('titular_conta_id', $request->input('titular_conta_id'))
                    ->where('conta_corrente_id', $request->input('conta_corrente_id'))
                    ->where('data', '<', $dataPagamento[$i])
                    ->first();
                    
                    //Cadastrar saldo daquela data com o último saldo para depois fazer a movimentação
                    $addSaldo = new SaldoDiario();

                    //Se saldo for null
                    if($ultimo_saldo == null){
                        $addSaldo->saldo = 0;
                    }else{
                        $addSaldo->saldo = $ultimo_saldo->saldo;
                    }
                    $addSaldo->titular_conta_id = $request->input('titular_conta_id');
                    $addSaldo->conta_corrente_id = $request->input('conta_corrente_id');
                    $addSaldo->data = $dataPagamento[$i];
                    $addSaldo->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                    $addSaldo->save();
        
                    $saldo = $addSaldo;
                    $valor_desatualizado_saldo =  $saldo->saldo; //Armazenar o ultimo saldo
        
                }else{//Caso houver saldo para aquele dia
                    $valor_desatualizado_saldo =  $saldo[0]->saldo; //Armazenar o ultimo saldo
                }
        
                //variavel que será responsavel por alterar-lo
                $saldo_model = SaldoDiario::where('data', $dataPagamento[$i])
                ->where('titular_conta_id', $request->input('titular_conta_id'))
                ->where('conta_corrente_id', $request->input('conta_corrente_id'))
                ->first();
        
                //Adicionando categoria
                $movimentacao_financeira->categoria_pagar_id = $contaPagar->categoria_pagar_id;

                //Atualizando o saldo
                $saldo_model->saldo = $valor_desatualizado_saldo - $valor_movimentacao; 
                $saldo_model->save();

                //Vincular Conta com Movimentacao
                $movimentacao_financeira->conta_pagar_id = $contaPagar->id;
    
                //salvar movimentação
                $movimentacao_financeira->save();

                //Vincular parcela com Movimentação
                $parcela->movimentacao_financeira_id = $movimentacao_financeira->id;
            // }

            $parcela->save();
            $i++;
        }
        return redirect("contas_pagar")->with('success', 'Parcelas baixadas com sucesso');   
    }

    function estornar_pagamento_view(Request $request){
        
        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
            // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

            //Verificar se há parcelas em aberto, somente pode estornar quando estiveres pagas
            foreach($checkboxesSelecionados as $parcelaId) {
                $parcela = ParcelaContaPagar::find($parcelaId);

                //Se houver parcelas em aberto redireciona de volta
                if($parcela->situacao == 0){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas pagas para estornar o pagamento');
                }
            }

            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela_conta_pagar as p')
                ->select(
                    'p.id as id',
                    'p.numero_parcela as numero_parcela',
                    'p.data_vencimento as data_vencimento',
                    'p.data_pagamento as data_pagamento',
                    'p.valor_pago as valor_pago',
                    'p.situacao as situacao_parcela',
                    'cp.id as conta_id',
                    'cp.quantidade_parcela as debito_quantidade_parcela',
                    'ccp.descricao as descricao',       
                )
                ->leftJoin('conta_pagar AS cp', 'p.conta_pagar_id', '=', 'cp.id')
                ->leftJoin('categoria_pagar AS ccp', 'cp.categoria_pagar_id', '=', 'ccp.id')
                ->where('p.id', $parcelaId)
                ->get();
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

            $parcelaPagarOutros = true;

            $data = [
                'titular_conta' => $titular_conta,
                'parcelaPagarOutros' => $parcelaPagarOutros,
            ];

            return view('parcela/parcela_estornar_pagamento_recebimento', compact('parcelas', 'data'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    function estornar_pagamento($user_id, Request $request){
    
        $idParcelas = $request->get('id_parcela', []);
        $dataPagamento = $request->get('data_pagamento_recebimento', []);
        $valorPago = $request->get('valor', []);

        $i = 0;
        foreach ($idParcelas as $id) {
            $parcela = ParcelaContaPagar::find($id);
            $parcela->valor_pago = null; 
            $parcela->data_pagamento = null;
            $parcela->data_baixa = null;
            $parcela->usuario_baixa_id = $user_id;
            $parcela->situacao = 0;

            //Selecionar o ID do movimentacao financeira
            $movimentacao_financeira_id = $parcela->movimentacao_financeira_id;
            $parcela->movimentacao_financeira_id = null; 
            

            //Se a conta está relacionada a uma movimentação
            if ($movimentacao_financeira_id != null) {
                $movimentacao_financeira = MovimentacaoFinanceira::find($movimentacao_financeira_id);

                //Pegando variáveis necessárias para selecionar e estornar saldo
                $titular_conta_id = $movimentacao_financeira->titular_conta_id;
                $conta_corrente_id = $movimentacao_financeira->conta_corrente_id;
                
                $dataFormatada = Carbon::createFromFormat('d-m-Y', str_replace('/', '-', $dataPagamento[$i]))->format('Y-m-d');;

                //Corrigindo valor para salvar
                $valor = (double) str_replace(',', '.', str_replace('.', '', $valorPago[$i]));

                //Variavel de saldo para manipulacao e verificacao do saldo
                $saldo = SaldoDiario::where('data', $dataFormatada)
                ->where('titular_conta_id', $titular_conta_id)
                ->where('conta_corrente_id', $conta_corrente_id)
                ->get(); // Saldo do dia

                if($saldo != null && isset($saldo[0])){
                    $valor_desatualizado_saldo =  $saldo[0]->saldo; //Armazenar o ultimo saldo
                 
                    //variavel que será responsavel por alterar-lo
                    $saldo_model = SaldoDiario::where('data', $dataFormatada)
                    ->where('titular_conta_id', $titular_conta_id)
                    ->where('conta_corrente_id', $conta_corrente_id)
                    ->first();

                     //Atualizando o saldo
                    $saldo_model->saldo = $valor_desatualizado_saldo + $valor; 
    
                    //Salvando alterações
                    $saldo_model->save();
                }

                $parcela->save();
                $movimentacao_financeira->delete();

            }else{ //Se não estiver relacionado
 
            }
            
            $i++;
        }
        return redirect("contas_pagar")->with('success', 'Estornado pagamento com sucesso'); 
    }

    function estornar_parcela_view(Request $request){
          
        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {

            // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

            //Verificar se há parcelas em aberto, somente pode estornar quando estiveres pagas
            foreach($checkboxesSelecionados as $parcelaId) {
                $parcela = ParcelaContaPagar::find($parcelaId);

                //Se houver parcelas em aberto redireciona de volta
                if($parcela->situacao == 1){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto para estornar a parcela');
                }
            }

            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela_conta_pagar as p')
                ->select(
                    'p.id as id',
                    'p.numero_parcela as numero_parcela',
                    'p.valor_parcela as valor_parcela',
                    'p.data_vencimento as data_vencimento',
                    'p.situacao as situacao_parcela',
                    'cp.id as conta_id',
                    'cp.quantidade_parcela as debito_quantidade_parcela',
                    'ccp.descricao as descricao',       
                )
                ->leftJoin('conta_pagar AS cp', 'p.conta_pagar_id', '=', 'cp.id')
                ->leftJoin('categoria_pagar AS ccp', 'cp.categoria_pagar_id', '=', 'ccp.id')
                ->where('p.id', $parcelaId)
                ->get();
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

            $parcelaPagarOutros = true;

            $data = [
                'titular_conta' => $titular_conta,
                'parcelaPagarOutros' => $parcelaPagarOutros,
            ];

            return view('parcela/parcela_estornar_parcela', compact('parcelas', 'data'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    function estornar_parcela($user_id, Request $request){
        $idParcelas = $request->get('id_parcela', []);

        $i = 0;
        foreach ($idParcelas as $id) {
            $parcela = ParcelaContaPagar::find($id);

            $conta_pagar_id = $parcela->conta_pagar_id;
            $conta_pagar = ContaPagar::find($conta_pagar_id);
            $parcela->delete(); 
            
            $i++;
        }
        /*
        if($conta_pagar != null){
            $conta_pagar->delete();
        }*/
        return redirect("contas_pagar")->with('success', 'Parcela excluída com sucesso'); 
    }
}
