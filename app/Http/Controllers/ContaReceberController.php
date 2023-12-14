<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TitularConta;
use App\Models\ContaReceber;
use App\Models\ParcelaContaReceber;
use App\Models\SaldoDiario;
use App\Models\Cliente;
use App\Models\Parcela;
use App\Models\MovimentacaoFinanceira;
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

        $request->merge([
            'valor_parcela' => str_replace(['.', ','], ['', '.'], $request->input('valor_parcela')),
            'valor_entrada' => str_replace(['.', ','], ['', '.'], $request->input('valor_entrada')),
        ]);

        $validated = $request->validate([
            'quantidade_parcela' => 'required|numeric',
            'cliente_id' => 'required|numeric|min:1',
            'categoria_receber_id' => 'required|numeric|min:1',
            'valor_parcela' => 'required|numeric',
            'data_vencimento' => 'required|date',
            'valor_entrada' => 'nullable|numeric',
        ]);

        $contaReceber = new ContaReceber();
        $contaReceber->titular_conta_id = $request->input('titular_conta_id');
        $contaReceber->cliente_id = $request->input('cliente_id');
        $contaReceber->categoria_receber_id = $request->input('categoria_receber_id');
        $contaReceber->quantidade_parcela = $request->input('quantidade_parcela');
        $contaReceber->data_vencimento = $request->input('data_vencimento');

        $valor_parcela = str_replace(',', '.', $request->input('valor_parcela'));
        $contaReceber->valor_parcela = (double) $valor_parcela; // Converter a string diretamente para um número em ponto flutuante
      
        $valor_entrada = str_replace(',', '.', $request->input('valor_entrada'));
        $contaReceber->valor_entrada = (double) $valor_entrada; // Converter a string diretamente para um número em ponto flutuante

        $contaReceber->descricao = $request->input('descricao');
        $contaReceber->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
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
        $idParcela = $request->input('idParcela');

    
        //select referente a parcelas de contas a receber de lotes
        $queryReferenteLotes = DB::table('parcela_conta_receber as p')
        ->select( 
            'p.id as id',
            'p.numero_parcela as numero_parcela',
            'p.data_vencimento as data_vencimento',
            'p.valor_parcela as valor_parcela',
            'p.situacao as situacao_parcela',
            'p.valor_recebido as parcela_valor_pago',
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
            'tpd.descricao as tipo_debito_descricao', 
            'l.id as lote_id',
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
        ->whereColumn('l.cliente_id', '<>', 'td.cliente_id')
        ->orderBy('data_vencimento', 'ASC');


        //select referente a parcelas de outras contas a receber
        $queryReferenteOutros = DB::table('parcela_conta_receber as p')
        ->select(
            'p.id as id',
            'p.numero_parcela as numero_parcela',
            'p.data_vencimento as data_vencimento',
            'p.valor_parcela as valor_parcela',
            'p.situacao as situacao_parcela',
            'p.valor_recebido as parcela_recebido',
            'p.data_recebimento as data_recebimento',
            'p.data_baixa as data_baixa',
            'p.cadastrado_usuario_id as parcela_cadastrado_usuario_id',
            'p.alterado_usuario_id as parcela_alterado_usuario_id',
            'p.usuario_baixa_id as parcela_usuario_baixa_id',
            'p.data_alteracao as parcela_data_alteracao',
            'cr.quantidade_parcela as quantidade_parcela',
            'cr.descricao as descricao',
            'ctr.descricao as categoria',
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
        ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id')
        ->orderBy('p.data_vencimento', 'ASC');

        /*----------
        FILTRO
        ----------*/
        if ($isReferenteLotes) { //Referente a Lotes

            if($titular_conta_id == 0){ //Se o titular da conta for 'Todos'

                if(!empty($periodoDe) && !empty($periodoAte) && $isPeriodoVencimento){ //Verifica período e Vencimento

                    $resultados = $queryReferenteLotes
                    ->where('p.data_vencimento', '>=', $periodoDe)
                    ->where('p.data_vencimento', '<=', $periodoAte)
                    ->get();

                }elseif(!empty($idParcela)){ //Busca por ID da parcela se for referente a Lotes e Todos titulares

                    $resultados = $queryReferenteLotes
                    ->where('p.id', '=', $idParcela)
                    ->get();

                }else{ //Se não houver nenhum período e nenhuma parcela específica

                    $resultados = $queryReferenteLotes->get();
                
                }

            }else{ //Se o titular da conta for específico

                if(!empty($periodoDe) && !empty($periodoAte) && $isPeriodoVencimento){ //Verifica período e Vencimento

                    $resultados = $queryReferenteLotes
                    ->where('p.data_vencimento', '>=', $periodoDe)
                    ->where('p.data_vencimento', '<=', $periodoAte)
                    ->where('d.titular_conta_id', $titular_conta_id)
                    ->get();

                }elseif(!empty($idParcela)){ //Busca por ID da parcela se for referente a Lotes e titular especifico

                    $resultados = $queryReferenteLotes
                    ->where('p.id', '=', $idParcela)
                    ->where('d.titular_conta_id', $titular_conta_id)
                    ->get();

                }else{ //Se não houver nenhum período e nenhuma parcela específica mas com titular especifico

                    $resultados = $queryReferenteLotes
                    ->where('d.titular_conta_id', $titular_conta_id)
                    ->get();

                }
                
            } 
        
        } 

        //Referente a outras receitas

        else { 

            if($titular_conta_id == 0){ //Se o titular da conta for 'Todos'

                if(!empty($periodoDe) && !empty($periodoAte) && $isPeriodoVencimento){ //Verifica período e Vencimento

                    $resultados = $queryReferenteOutros
                    ->where('p.data_vencimento', '>=', $periodoDe)
                    ->where('p.data_vencimento', '<=', $periodoAte)
                    ->get();

                } elseif(!empty($idParcela)){ //Busca por ID da parcela se for referente a outros e Todos titulares

                    $resultados = $queryReferenteOutros
                    ->where('p.id', '=', $idParcela)
                    ->get();

                } else{ //Busca todos períodos referente a outras receitas

                    $resultados = $queryReferenteOutros->get();

                }

            }else{ //Se o titular da conta for específico

                if(!empty($periodoDe) && !empty($periodoAte) && $isPeriodoVencimento){ //Verifica período e Vencimento

                    $resultados = $queryReferenteOutros
                    ->where('p.data_vencimento', '>=', $periodoDe)
                    ->where('p.data_vencimento', '<=', $periodoAte)
                    ->where('cr.titular_conta_id', $titular_conta_id)
                    ->get();

                } elseif(!empty($idParcela)){ //Busca por ID da parcela se for referente a outros e titular específico

                    $resultados = $queryReferenteOutros
                    ->where('p.id', '=', $idParcela)
                    ->where('cr.titular_conta_id', $titular_conta_id)
                    ->get();

                }
                else{ //Busca todos períodos referente a outras receitas e titular específico

                    $resultados = $queryReferenteOutros
                    ->where('cr.titular_conta_id', $titular_conta_id)
                    ->get();

                }

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

            //Verificar se há parcelas já recebidas
            foreach($checkboxesSelecionados as $parcelaId) {
                $parcela = ParcelaContaReceber::find($parcelaId);

                //Se houver parcelas já recebidas redireciona de volta
                if($parcela->situacao == 1){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto! Dica: para alterar parcelas já recebidas estornar o recebimento!');
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
                    'cr.id as conta_receber_id',
                    'cr.quantidade_parcela as debito_quantidade_parcela',
                    'ccr.descricao as descricao',       
                )
                ->leftJoin('conta_receber AS cr', 'p.conta_receber_id', '=', 'cr.id')
                ->leftJoin('categoria_receber AS ccr', 'cr.categoria_receber_id', '=', 'ccr.id')
                ->where('p.id', $parcelaId)
                ->get();
            }
            $parcelaReceberOutros = true;
            return view('parcela/parcela_reajustar', compact('parcelas', 'parcelaReceberOutros'));

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

        //Validar
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

            //Verificar se há parcelas já recebidas
            foreach($checkboxesSelecionados as $parcelaId) {
                $parcela = ParcelaContaReceber::find($parcelaId);

                //Se houver parcelas já recebidas redireciona de volta
                if($parcela->situacao == 1){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto! Dica: para alterar parcelas já recebidas estornar o recebimento!');
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
                    'cr.id as conta_receber_id',
                    'cr.quantidade_parcela as debito_quantidade_parcela',
                    'ccr.descricao as descricao',    
                )
                ->leftJoin('conta_receber AS cr', 'p.conta_receber_id', '=', 'cr.id')
                ->leftJoin('categoria_receber AS ccr', 'cr.categoria_receber_id', '=', 'ccr.id')
                ->where('p.id', $parcelaId)
                ->get();
            }
            $parcelaReceberOutros = true;
            return view('parcela/parcela_alterar_vencimento', compact('parcelas', 'parcelaReceberOutros'));

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
            $parcela = ParcelaContaReceber::find($p);
            if($i > 0){
                $parcela->data_vencimento = $dataCarbon->addMonth();
            }else{
                $parcela->data_vencimento = $data_vencimento;
            }
            $parcela->save();
            $i++;
        }

        return redirect("contas_receber")->with('success', 'Data(s) de vencimento alteradas com sucesso');   
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
                    return redirect()->back()->with('error', 'Selecione apenas parcelas em aberto! Dica: para alterar parcelas já recebidas estornar o recebimento!');
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
                    'cr.id as conta_receber_id',
                    'cr.quantidade_parcela as debito_quantidade_parcela',
                    'ccr.descricao as descricao',    
                )
                ->leftJoin('conta_receber AS cr', 'p.conta_receber_id', '=', 'cr.id')
                ->leftJoin('categoria_receber AS ccr', 'cr.categoria_receber_id', '=', 'ccr.id')
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

            $parcelaReceberOutros = true;
            
            $data = [
                'titular_conta' => $titular_conta,
                'parcelaReceberOutros' => $parcelaReceberOutros,
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
            'valor.*' => 'required|numeric|min:0.1',
        ]);

        $idParcelas = $request->get('id_parcela', []);
        $valorRecebido = $request->get('valor', []);
        $dataRecebimento = $request->get('data', []);

        if($dataRecebimento > date('d-m-Y h:i:s a', time())){
            return redirect()->back()->with('error', 'Não é possível baixar com datas futuras!');
        }
     
        $i = 0;
        foreach ($idParcelas as $id) {
            $parcela = ParcelaContaReceber::find($id);

            $valor = str_replace(',', '.', $valorRecebido[$i]);
            $parcela->valor_recebido = (double) $valor; // Converter a string diretamente para um número em ponto flutuante
            $parcela->data_recebimento = $dataRecebimento[$i];
            $parcela->data_baixa = Carbon::now()->format('Y-m-d H:i:s');
            $parcela->usuario_baixa_id = $user_id;
            $parcela->situacao = 1;

            //Selecionar ID do contas a receber
            $conta_receber_id = $parcela->conta_receber_id;
            
            //Obter conta a receber
            $contaReceber = ContaReceber::find($conta_receber_id);

            //Se a conta está relacionada a uma movimentação
            if ($parcela->movimentacao_financeira_id != null) {
                
            }else{ //Se não estiver relacionado

                $movimentacao_financeira = new MovimentacaoFinanceira();
                $movimentacao_financeira->cliente_fornecedor_id = $contaReceber->cliente_id;
                $movimentacao_financeira->descricao = $contaReceber->descricao;
                $movimentacao_financeira->data_movimentacao = $dataRecebimento[$i];
                $movimentacao_financeira->titular_conta_id = $request->input('titular_conta_id');
                $movimentacao_financeira->conta_corrente_id = $request->input('conta_corrente_id');
                
                // No Banco de Dados o 'tipo_movimentacao' é boolean = False (Entrada 0) e True(Saida 1)
                // Porém no input 0 (Selecione), 1 (Entrada) e 2 (Saída)
                $movimentacao_financeira->tipo_movimentacao = 0; //Contas a Receber é Entrada
        
                $valor = str_replace(',', '.', $valorRecebido[$i]);
                $movimentacao_financeira->valor = (double) $valor; // Converter a string diretamente para um número em ponto flutuante
                $valor_movimentacao = (double) $valor; //Armazenar em uma variavel o valor da movimentação
            
                $movimentacao_financeira->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                $movimentacao_financeira->cadastrado_usuario_id = $user_id;
        
                //Variavel de saldo para manipulacao e verificacao do saldo
                $saldo = SaldoDiario::where('data', $dataRecebimento[$i])
                ->where('titular_conta_id', $request->input('titular_conta_id'))
                ->where('conta_corrente_id', $request->input('conta_corrente_id'))
                ->get(); // Saldo do dia
        
                //Se não houver saldo para aquele dia
                if(!isset($saldo[0]->saldo)){
                    //Último saldo cadastrado
                    $ultimo_saldo = SaldoDiario::orderBy('data', 'desc')
                    ->where('titular_conta_id', $request->input('titular_conta_id'))
                    ->where('conta_corrente_id', $request->input('conta_corrente_id'))
                    ->where('data', '<', $dataRecebimento[$i])
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
                    $addSaldo->data = $dataRecebimento[$i];
                    $addSaldo->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                    $addSaldo->save();
        
                    $saldo = $addSaldo;
                    $valor_desatualizado_saldo =  $saldo->saldo; //Armazenar o ultimo saldo
        
                }else{//Caso houver saldo para aquele dia
                    $valor_desatualizado_saldo =  $saldo[0]->saldo; //Armazenar o ultimo saldo
                }
        
                //variavel que será responsavel por alterar-lo
                $saldo_model = SaldoDiario::where('data', $dataRecebimento[$i])
                ->where('titular_conta_id', $request->input('titular_conta_id'))
                ->where('conta_corrente_id', $request->input('conta_corrente_id'))
                ->first();
        
                //Adicionando categoria
                $movimentacao_financeira->categoria_receber_id = $contaReceber->categoria_receber_id;

                //Atualizando o saldo
                $saldo_model->saldo = $valor_desatualizado_saldo + $valor_movimentacao; 
                $saldo_model->save();

                //Vincular Conta com Movimentacao
                $movimentacao_financeira->conta_receber_id = $contaReceber->id;
    
                //salvar movimentação
                $movimentacao_financeira->save();

                //Vincular parcela com Movimentação
                $parcela->movimentacao_financeira_id = $movimentacao_financeira->id;

            }
            $parcela->save();
            $i++;
        }
        return redirect("contas_receber")->with('success', 'Parcelas baixadas com sucesso');   
    }

    function estornar_recebimento_view(Request $request){
          
        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {

            // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

            //Verificar se há parcelas em aberto, somente pode estornar quando estiveres pagas
            foreach($checkboxesSelecionados as $parcelaId) {
                $parcela = ParcelaContaReceber::find($parcelaId);

                //Se houver parcelas em aberto redireciona de volta
                if($parcela->situacao == 0){
                    return redirect()->back()->with('error', 'Selecione apenas parcelas pagas para estornar o recebimento');
                }
            }

            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela_conta_receber as p')
                ->select(
                    'p.id as id',
                    'p.numero_parcela as numero_parcela',
                    'p.data_vencimento as data_vencimento',
                    'p.data_recebimento as data_recebimento',
                    'p.valor_recebido as valor_recebido',
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
            
            $titular_conta = DB::table('titular_conta as t')
            ->select(
                't.id as id_titular_conta',
                't.cliente_id as cliente_id',
                'c.nome as nome',
                'c.razao_social as razao_social',
            )
            ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
            ->get();

            $parcelaReceberOutros = true;

            $data = [
                'titular_conta' => $titular_conta,
                'parcelaReceberOutros' => $parcelaReceberOutros,
            ];

            return view('parcela/parcela_estornar_pagamento_recebimento', compact('parcelas', 'data'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }


    function estornar_recebimento($user_id, Request $request){
    
        $idParcelas = $request->get('id_parcela', []);
        $dataRecebimento = $request->get('data_pagamento_recebimento', []);
        $valorRecebido = $request->get('valor', []);

        $i = 0;
        foreach ($idParcelas as $id) {
            $parcela = ParcelaContaReceber::find($id);
            $parcela->valor_recebido = null; 
            $parcela->data_recebimento = null;
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

                //Variavel de saldo para manipulacao e verificacao do saldo
                $saldo = SaldoDiario::where('data', '=', $dataRecebimento[$i])
                ->where('titular_conta_id', $titular_conta_id)
                ->where('conta_corrente_id', $conta_corrente_id)
                ->get(); // Saldo do dia
                dd($dataRecebimento[$i]);
                $valor_desatualizado_saldo =  $saldo[0]->saldo; //Armazenar o ultimo saldo
                 
                //variavel que será responsavel por alterar-lo
                $saldo_model = SaldoDiario::where('data', $dataRecebimento[$i])
                ->where('titular_conta_id', $titular_conta_id)
                ->where('conta_corrente_id', $conta_corrente_id)
                ->first();

                $valor = (double) str_replace(',', '.', $valorRecebido[$i]);

                //Atualizando o saldo
                $saldo_model->saldo = $valor_desatualizado_saldo - $valor; 

                //Salvando alterações
                $saldo_model->save();
                $parcela->save();
                $movimentacao_financeira->delete();

            }else{ //Se não estiver relacionado
 
            }
            
            $i++;
        }
        return redirect("contas_receber")->with('success', 'Estornado recebimento com sucesso'); 
    }
}