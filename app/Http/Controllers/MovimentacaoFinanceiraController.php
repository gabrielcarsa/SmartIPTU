<?php

namespace App\Http\Controllers;
use App\Models\MovimentacaoFinanceira;
use App\Models\SaldoDiario;
use App\Models\Cliente;
use App\Models\ContaCorrente;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\ParcelaContaPagar;
use App\Models\ParcelaContaReceber;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MovimentacaoFinanceiraController extends Controller
{
    function movimentacao_financeira(){
        $hoje = now()->toDateString(); // Obtém a data de hoje no formato 'YYYY-MM-DD'

        //Soma das entradas do dia atual
        $entradas = DB::table('movimentacao_financeira')
            ->whereDate('data_movimentacao', $hoje)
            ->where('tipo_movimentacao', 0)
            ->sum('valor');

        //Soma das saidas do dia atual
        $saidas = DB::table('movimentacao_financeira')
            ->whereDate('data_movimentacao', $hoje)
            ->where('tipo_movimentacao', 1)
            ->sum('valor');

        $data = [
            'entradas' => $entradas,
            'saidas' => $saidas
        ];

        return view('movimentacao_financeira/movimentacao_financeira', compact('data'));
    }

     // LISTAGEM DE MOVIMENTAÇÃO FINANCEIRA
     function listar(Request $request){
        $hoje = now()->toDateString(); // Obtém a data de hoje no formato 'YYYY-MM-DD'

        //Soma das entradas do dia atual
        $entradas = DB::table('movimentacao_financeira')
            ->whereDate('data_movimentacao', $hoje)
            ->where('tipo_movimentacao', 0)
            ->sum('valor');

        //Soma das saidas do dia atual
        $saidas = DB::table('movimentacao_financeira')
            ->whereDate('data_movimentacao', $hoje)
            ->where('tipo_movimentacao', 1)
            ->sum('valor');

        $movimentacao = MovimentacaoFinanceira::all();

        //Datas 
        $dataRef = $request->input('data');

        $saldo_anterior = SaldoDiario::orderBy('data', 'desc')->where('data', '<', $request->input('data'))->get(); // Saldo anterior
        $saldo_atual = SaldoDiario::where('data', $dataRef)->get(); // Saldo do dia

        $total_movimentacao = $movimentacao->count();
        $query = DB::table('movimentacao_financeira as mf');

        // Verifique se o campo "data" está preenchido no formulário
        if ($request->filled('data')) {
            $query->select(
                'mf.*',
                'c.nome as nome',
                'c.tipo_cadastro as tipo_cadastro',
                'c.razao_social as razao_social',
            )
            ->join('cliente as c', 'mf.cliente_fornecedor_id', '=', 'c.id')
            ->where('data_movimentacao', '=', '%' . $dataRef);
        }

        // Execute a consulta e obtenha os resultados
        $movimentacao = $query->get();


        $data = [
            'saldo_anterior' => $saldo_anterior,
            'saldo_atual' => $saldo_atual,
            'total_movimentacao' => $total_movimentacao,
            'entradas' => $entradas,
            'saidas' => $saidas
        ];
        
        return view('movimentacao_financeira/movimentacao_financeira', compact('movimentacao', 'data'));
    }

     //RETORNA VIEW PARA CADASTRO DE NOVA MOVIMENTAÇÃO
     function novo(){
        $titular_conta = DB::table('titular_conta as t')
        ->select(
            't.id as id_titular_conta',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();

        $clientes = Cliente::all();


        $data = [
            'titular_conta' => $titular_conta,
            'clientes' => $clientes,
        ];
        return view('movimentacao_financeira/movimentacao_financeira_novo', compact('data'));
    }

    //RETORNA UM JSON COM A CONTA CORRENTE ESPECÍFICA
    function conta_corrente($titular_conta_id){
        $conta_corrente = ContaCorrente::where('titular_conta_id',$titular_conta_id)->get();
        return response()->json($conta_corrente);
    }

    //CADASTRAR MOVIMENTAÇÃO
    function cadastrar($usuario, Request $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    
        
        // Salve as movimentações
        foreach ($request->input('movimentacoes') as $movimentacaoData) {
            $request->merge([
                'valor' => str_replace(['.', ','], ['', '.'], $movimentacaoData['valor']),
            ]);

            $movimentacao_financeira = new MovimentacaoFinanceira();
            $movimentacao_financeira->cliente_fornecedor_id = $movimentacaoData['cliente_fornecedor_id'];
            $movimentacao_financeira->descricao = $movimentacaoData['descricao'];
            $movimentacao_financeira->data_movimentacao = $request->input('data');
            $movimentacao_financeira->titular_conta_id = $request->input('titular_conta_id');
            $movimentacao_financeira->conta_corrente_id = $request->input('conta_corrente_id');
            
            // No Banco de Dados o 'tipo_movimentacao' é boolean = False (Entrada 0) e True(Saida 1)
            // Porém no input 0 (Selecione), 1 (Entrada) e 2 (Saída)
            $movimentacao_financeira->tipo_movimentacao = ($movimentacaoData['tipo_movimentacao'] == 1) ? 0 : 1;
    
            $valor = str_replace(',', '.', $movimentacaoData['valor']);
            $movimentacao_financeira->valor = (double) $valor; // Converter a string diretamente para um número em ponto flutuante
            $valor_movimentacao = (double) $valor; //Armazenar em uma variavel o valor da movimentação
        
            $movimentacao_financeira->data_cadastro = date('d-m-Y h:i:s a', time());
            $movimentacao_financeira->cadastrado_usuario_id = $usuario;
    
            //Variavel de saldo para manipulacao e verificacao do saldo
            $saldo = SaldoDiario::where('data', $request->input('data'))->get(); // Saldo do dia
    
            //Se não houver saldo para aquele dia
            if(!isset($saldo[0]->saldo)){
                //Último saldo cadastrado
                $ultimo_saldo = SaldoDiario::orderBy('data', 'desc')->where('data', '<', $request->input('data'))->first();
                
                //Cadastrar saldo daquela data com o último saldo para depois fazer a movimentação
                $addSaldo = new SaldoDiario();
                $addSaldo->saldo = $ultimo_saldo->saldo;
                $addSaldo->data = $request->input('data');
                $addSaldo->data_cadastro = date('d-m-Y h:i:s a', time());
                $addSaldo->save();
    
                $saldo = $addSaldo;
                $valor_desatualizado_saldo =  $saldo->saldo; //Armazenar o ultimo saldo
    
            }else{//Caso houver saldo para aquele dia
                $valor_desatualizado_saldo =  $saldo[0]->saldo; //Armazenar o ultimo saldo
            }
    
            //variavel que será responsavel por alterar-lo
            $saldo_model = SaldoDiario::where('data', $request->input('data'))->first();
    
            //Verificar se a movimentação é de entrada ou saída
            $tipo_movimentacao = $movimentacaoData['tipo_movimentacao'];
    
            if($tipo_movimentacao == 1){ // ENTRADA
    
                //Adicionando categoria
                $movimentacao_financeira->categoria_receber_id = $movimentacaoData['categoria_id'];
    
                //Atualizando o saldo
                $saldo_model->saldo = $valor_desatualizado_saldo + $valor_movimentacao; 
                $saldo_model->save();
    
                //Atualizar no Contas a Receber
                $contaReceber = new ContaReceber();
                $contaReceber->titular_conta_id = $request->input('titular_conta_id');
                $contaReceber->cliente_id = $movimentacaoData['cliente_fornecedor_id'];
                $contaReceber->categoria_receber_id = $movimentacaoData['categoria_id'];
                $contaReceber->quantidade_parcela = 1;
                $contaReceber->data_vencimento = $request->input('data');
                $contaReceber->valor_parcela = $valor_movimentacao; 
                $contaReceber->descricao = $movimentacaoData['descricao'];
                $contaReceber->data_cadastro = date('d-m-Y h:i:s a', time());
                $contaReceber->cadastrado_usuario_id = $usuario;
                $contaReceber->save();
    
                // Cadastrar Parcelas
                $qtd_parcelas = 1;
                $contaReceber_id = $contaReceber->id;
                $data_vencimento = $contaReceber->data_vencimento; 
                $dataCarbon = Carbon::createFromFormat('Y-m-d', $data_vencimento);
    
                $parcela = new ParcelaContaReceber();
                $parcela->conta_receber_id = $contaReceber_id;
                $parcela->numero_parcela = 1;
                $parcela->situacao = 1;
                $parcela->valor_parcela = $valor_movimentacao;
                $parcela->cadastrado_usuario_id = $usuario;
                $parcela->data_vencimento = $data_vencimento;
                $parcela->save();
                
                        
            }else{ // SAÍDA
    
                //Adicionando categoria
                $movimentacao_financeira->categoria_pagar_id = $movimentacaoData['categoria_id'];
    
                //Atualizando o saldo
                $saldo_model->saldo = $valor_desatualizado_saldo - $valor_movimentacao; 
                $saldo_model->save();
    
                //Atualizar no Contas a Pagar
                $contaPagar = new ContaPagar();
                $contaPagar->titular_conta_id = $request->input('titular_conta_id');
                $contaPagar->fornecedor_id = $movimentacaoData['cliente_fornecedor_id'];
                $contaPagar->categoria_pagar_id = $movimentacaoData['categoria_id'];
                $contaPagar->quantidade_parcela = 1;
                $contaPagar->data_vencimento = $request->input('data');
                $contaPagar->valor_parcela = $valor_movimentacao; 
                $contaPagar->descricao = $movimentacaoData['descricao'];
                $contaPagar->data_cadastro = date('d-m-Y h:i:s a', time());
                $contaPagar->cadastrado_usuario_id = $usuario;
                $contaPagar->save();
    
                // Cadastrar Parcelas
                $qtd_parcelas = 1;
                $contaPagar_id = $contaPagar->id;
                $data_vencimento = $contaPagar->data_vencimento; 
                $dataCarbon = Carbon::createFromFormat('Y-m-d', $data_vencimento);
    
                $parcela = new ParcelaContaPagar();
                $parcela->conta_pagar_id = $contaPagar_id;
                $parcela->numero_parcela = 1;
                $parcela->situacao = 1;
                $parcela->valor_parcela = $valor_movimentacao;
                $parcela->cadastrado_usuario_id = $usuario;
                $parcela->data_vencimento = $data_vencimento;
                $parcela->save();
                
            }
    
            //salvar movimentação
            $movimentacao_financeira->save();
        }

        return redirect('movimentacao_financeira')->with('success', 'Movimentação cadastrada com sucesso');
    }
}
