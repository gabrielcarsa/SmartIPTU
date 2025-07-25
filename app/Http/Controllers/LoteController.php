<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\Quadra;
use App\Models\User;
use App\Models\Debito;
use App\Models\TitularConta;
use App\Models\Cliente;
use App\Models\ParcelaContaPagar;
use App\Models\ParcelaContaReceber;
use App\Http\Requests\LoteRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Smalot\PdfParser\Parser;

class LoteController extends Controller
{
     //RETORNA VIEW PARA ADICIONAR LOTE
     function novo($empreendimento_id){
        $quadras = Quadra::where('empreendimento_id', $empreendimento_id)
        ->select('id as quadra_id', 'nome as quadra_nome')
        ->get();
        //$quadras = Quadra::select('quadra.id as quadra_id, quadra_nome')->where('quadra.empreendimento_id', '=', $empreendimento_id);
        $clientes = Cliente::all();
        return view('lote/lote_novo', compact('empreendimento_id', 'quadras'), compact('clientes'));
    }

     //CADASTRO DE LOTE
     function cadastrar($usuario, $empreendimento_id, loteRequest $request){
        // Validar campos
        //$validated = $request->validated();

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $lote = new Lote();
        $lote->lote = $request->input('lote');
        $lote->quadra_id = $request->input('quadra_id');
        $lote->cliente_id = $request->input('cliente_id');
        $lote->matricula = $request->input('matricula');
        $lote->inscricao_municipal = $request->input('inscricao_municipal');
        $lote->valor = str_replace(['.', ','], ['', '.'], $request->input('valor'));
        $lote->endereco = $request->input('endereco');
        $lote->metros_quadrados = $request->input('metros_quadrados');
        $lote->metragem_frente = $request->input('metragem_frente');
        $lote->metragem_fundo = $request->input('metragem_fundo');
        $lote->metragem_direita = $request->input('metragem_direita');
        $lote->metragem_esquerda = $request->input('metragem_esquerda');
        $lote->metragem_esquina = $request->input('metragem_esquina');
        $lote->confrontacao_frente = $request->input('confrontacao_frente');
        $lote->confrontacao_fundo = $request->input('confrontacao_fundo');
        $lote->confrontacao_direita = $request->input('confrontacao_direita');
        $lote->confrontacao_esquerda = $request->input('confrontacao_esquerda');
        $lote->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
        $lote->cadastrado_usuario_id = $usuario;
        $lote->save();
        return redirect('empreendimento/gestao/'.$empreendimento_id)->with('success', 'Lote cadastrado com sucesso');
    }

     //RETORNA VIEW ALTERAR LOTE      
     function editar($id){
        $lote = Lote::find($id);
        
        $cliente_nome = Cliente::select('nome as nome_cliente', 'razao_social as razao_social_cliente')
        ->where('id', $lote->cliente_id)
        ->first();

        $quadra_nome = Quadra::where('id', $lote->quadra_id)->first();

        $cadastrado_por_user_id = $lote['cadastrado_usuario_id'];
        $alterado_por_user_id = $lote['alterado_usuario_id'];

        $cadastrado_por_user = User::find($cadastrado_por_user_id);
        $alterado_por_user = User::find($alterado_por_user_id);

        $data = [
            'cliente_nome' => $cliente_nome,
            'quadra_nome' => $quadra_nome,
            'cadastrado_por_user' => $cadastrado_por_user,
            'alterado_por_user' => $alterado_por_user,
        ];

        return view('lote/lote_novo', compact('data', 'lote'));
    }

    //ALTERAR LOTE
    function alterar($id, $usuario, LoteRequest $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');

        $lote = Lote::find($id);

        if (!$lote) {
            return redirect()->back()->with('error', 'Lote não encontrado');
        }

        $lote->lote = $request->input('lote');
        $lote->matricula = $request->input('matricula');
        $lote->inscricao_municipal = $request->input('inscricao_municipal');
        $lote->valor = $request->input('valor');
        $lote->endereco = $request->input('endereco');
        $lote->metros_quadrados = $request->input('metros_quadrados');
        $lote->metragem_frente = $request->input('metragem_frente');
        $lote->metragem_fundo = $request->input('metragem_fundo');
        $lote->metragem_direita = $request->input('metragem_direita');
        $lote->metragem_esquerda = $request->input('metragem_esquerda');
        $lote->metragem_esquina = $request->input('metragem_esquina');
        $lote->confrontacao_frente = $request->input('confrontacao_frente');
        $lote->confrontacao_fundo = $request->input('confrontacao_fundo');
        $lote->confrontacao_direita = $request->input('confrontacao_direita');
        $lote->confrontacao_esquerda = $request->input('confrontacao_esquerda');
        $lote->data_alteracao = Carbon::now()->format('Y-m-d H:i:s');
        $lote->alterado_usuario_id = $usuario;
        $lote->save();

        return redirect()->back()->with('success', 'Lote cadastrado com sucesso');

    }

    //EXCLUIR LOTE
    function excluir($id){
        $lote = Lote::find($id);
    
        if (!$lote) {
            return redirect()->back()->with('error', 'Lote não encontrado');
        }
    
        $quadra = Quadra::find($lote->quadra_id);
    
        if (!$quadra) {
            return redirect()->back()->with('error', 'Quadra não encontrada');
        }
    
        $empreendimento_id = $quadra->empreendimento_id;
    
        $lote->delete();
    
        return redirect("empreendimento/gestao/".$empreendimento_id)->with('success', 'Lote excluído com sucesso');
    }

    //RETORNA VIEW PARA GESTÃO DO LOTE
    function gestao($id){
        $empresa = TitularConta::find(1);
        $lote = Lote::find($id);

        $resultadosPagar = DB::table('lote AS l')
        ->select(
            'l.id AS lote_id',
            'l.lote',
            'l.inscricao_municipal',
            'l.data_venda',
            'q.id AS quadra_id',
            'q.nome AS quadra_nome',
            'd.id AS debito_id',
            'd.data_cadastro AS debito_data_cadastro',
            'd.data_alteracao AS debito_data_alteracao',
            'd.valor_parcela AS valor_parcela_debito',
            'd.quantidade_parcela AS quantidade_parcela_debito',
            'e.id AS empreendimento_id',
            'e.nome AS empreendimento_nome',
            'td.id AS tipo_debito_id',
            'td.descricao AS tipo_debito_descricao',
            'dd.id AS descricao_debito_id',
            'dd.descricao AS descricao_debito_descricao',
            'p.id AS parcela_id',
            'p.valor_parcela AS valor_parcela',
            'p.valor_pago AS valor_pago_parcela',
            'p.data_pagamento AS data_recebimento_parcela',
            'p.data_vencimento AS data_vencimento_parcela',
            'p.numero_parcela AS numero_parcela',
            'p.situacao AS situacao_parcela',
            'cliente.nome AS nome_cliente',
            'cliente.razao_social AS razao_social_cliente',
            'cliente.telefone1 AS tel1',
            'cliente.telefone1 AS tel2',
            'users_cadastrado.name AS cadastrado_usuario_nome',
            'users_alterado.name AS alterado_usuario_nome'
        )
        ->join('quadra AS q', 'l.quadra_id', '=', 'q.id')
        ->join('empreendimento AS e', 'q.empreendimento_id', '=', 'e.id')
        ->join('cliente', 'cliente.id', '=', 'l.cliente_id') // Corrigido para incluir a tabela cliente
        ->leftJoin('debito AS d', 'l.id', '=', 'd.lote_id')
        ->leftJoin('tipo_debito AS td', 'd.tipo_debito_id', '=', 'td.id')
        ->leftJoin('parcela_conta_pagar AS p', 'd.id', '=', 'p.debito_id')
        ->leftJoin('descricao_debito AS dd', 'p.descricao_debito_id', '=', 'dd.id')
        ->leftJoin('users AS users_cadastrado', 'users_cadastrado.id', '=', 'd.cadastrado_usuario_id')
        ->leftJoin('users AS users_alterado', 'users_alterado.id', '=', 'd.alterado_usuario_id')
        ->where('l.id', $id)
        ->orderBy('data_vencimento_parcela', 'ASC') 
        ->get();
     
        $resultadosReceber = DB::table('lote AS l')
        ->select(
            'l.id AS lote_id',
            'l.lote',
            'l.inscricao_municipal',
            'l.data_venda',
            'q.id AS quadra_id',
            'q.nome AS quadra_nome',
            'd.id AS debito_id',
            'd.data_cadastro AS debito_data_cadastro',
            'd.data_alteracao AS debito_data_alteracao',
            'd.valor_parcela AS valor_parcela_debito',
            'd.quantidade_parcela AS quantidade_parcela_debito',
            'e.id AS empreendimento_id',
            'e.nome AS empreendimento_nome',
            'td.id AS tipo_debito_id',
            'td.descricao AS tipo_debito_descricao',
            'dd.id AS descricao_debito_id',
            'dd.descricao AS descricao_debito_descricao',
            'p.id AS parcela_id',
            'p.valor_parcela AS valor_parcela',
            'p.valor_recebido AS valor_pago_parcela',
            'p.data_recebimento AS data_recebimento_parcela',
            'p.data_vencimento AS data_vencimento_parcela',
            'p.numero_parcela AS numero_parcela',
            'p.situacao AS situacao_parcela',
            'cliente.nome AS nome_cliente',
            'cliente.razao_social AS razao_social_cliente',
            'cliente.telefone1 AS tel1',
            'cliente.telefone1 AS tel2',
            'users_cadastrado.name AS cadastrado_usuario_nome',
            'users_alterado.name AS alterado_usuario_nome'
        )
        ->join('quadra AS q', 'l.quadra_id', '=', 'q.id')
        ->join('empreendimento AS e', 'q.empreendimento_id', '=', 'e.id')
        ->join('cliente', 'cliente.id', '=', 'l.cliente_id') // Corrigido para incluir a tabela cliente
        ->leftJoin('debito AS d', 'l.id', '=', 'd.lote_id')
        ->leftJoin('tipo_debito AS td', 'd.tipo_debito_id', '=', 'td.id')
        ->leftJoin('parcela_conta_receber AS p', 'd.id', '=', 'p.debito_id')
        ->leftJoin('descricao_debito AS dd', 'p.descricao_debito_id', '=', 'dd.id')
        ->leftJoin('users AS users_cadastrado', 'users_cadastrado.id', '=', 'd.cadastrado_usuario_id')
        ->leftJoin('users AS users_alterado', 'users_alterado.id', '=', 'd.alterado_usuario_id')
        ->where('l.id', $id)
        ->orderBy('data_vencimento_parcela', 'ASC') 
        ->get();

        // Inicialize uma variável para armazenar o valor total
        $totalValorParcelas = 0;
        $totalValorReceber = 0;
        $totalValorPagar = 0;


        // Percorra a coleção de resultados
        if($resultadosPagar != null){
            foreach ($resultadosPagar as $resultado) {
                // Verifique se a situação da parcela é igual a 0
                if ($resultado->situacao_parcela == 0) {
                    // Adicione o valor da parcela ao valor total
                    $totalValorParcelas += $resultado->valor_parcela;
                    $totalValorPagar += $resultado->valor_parcela;
                    
                }
            }
        }
       
        if($resultadosReceber != null){
            // Percorra a coleção de resultados
            foreach ($resultadosReceber as $resultado) {
                // Verifique se a situação da parcela é igual a 0
                if ($resultado->situacao_parcela == 0) {
                    // Adicione o valor da parcela ao valor total
                    $totalValorParcelas += $resultado->valor_parcela;
                    $totalValorReceber += $resultado->valor_parcela;
                }
            }
        }

        $valoresTotais = [
            'totalValorParcelas' => $totalValorParcelas,
            'totalValorReceber' => $totalValorReceber,
            'totalValorPagar' => $totalValorPagar,
        ];

        return view('lote/lote_gestao', compact('resultadosReceber', 'resultadosPagar'), compact('valoresTotais'));

    }

    //RETORNA VIEW NOVA LOTE      
    function nova_venda($id){
        $lote = Lote::find($id);
        $clientes = Cliente::orderBy('nome')->get();

        $quadra = Quadra::where('id', $lote->quadra_id)->first();

        $data = [
            'lote' => $lote,
            'quadra' => $quadra,
        ];

        return view('lote/lote_contrato', compact('data', 'clientes'));
    }

    //CADASTRAR VENDA LOTE
    function cadastrar_venda($lote_id, $usuario, Request $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');

        $lote = Lote::find($lote_id);

        if (!$lote) {
            return redirect()->back()->with('error', 'Lote não encontrado');
        }

        $lote->data_venda = $request->input('data_contrato');
        $lote->cliente_id = $request->input('cliente_id');
        $lote->data_alteracao = Carbon::now()->format('Y-m-d H:i:s');
        $lote->alterado_usuario_id = $usuario;
        $lote->save();

        return redirect()->back()->with('success', 'Venda cadastrada com sucesso');

    }

    //NEGATIVAR
    function negativar($lote_id){

        $lote = Lote::find($lote_id);

        if (!$lote) {
            return redirect()->back()->with('error', 'Lote não encontrado');
        }

        if($lote->negativar != true){
            $lote->negativar = true;
        }else{
            $lote->negativar = false;
        }
        $lote->save();

        return redirect()->back()->with('success', 'Operação realizada com sucesso');
    }

    //ACORDO PARCIAL
    function acordo_parcial(Request $request){
        $id = $request->get('id');

        $lote = Lote::find($id);

        if (!$lote) {
            return redirect()->back()->with('error', 'Lote não encontrado');
        }

        if($lote->is_acordo_parcial != true){
            $lote->is_acordo_parcial = true;
        }else{
            $lote->is_acordo_parcial = false;
        }
        $lote->save();

        return redirect()->back()->with('success', 'Operação realizada com sucesso');
    }

    //ACORDO
    function acordo(Request $request){
        $id = $request->get('id');

        $lote = Lote::find($id);

        if (!$lote) {
            return redirect()->back()->with('error', 'Lote não encontrado');
        }

        if($lote->is_acordo_total != true){
            $lote->is_acordo_total = true;
        }else{
            $lote->is_acordo_total = false;
        }
        $lote->save();

        return redirect()->back()->with('success', 'Operação realizada com sucesso');
    }

    private function extrairInscricoes($filePath) {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();
    
        // Expressão regular para inscrições imobiliárias
        $pattern = '/\b\d{10}-\d\b/';
        preg_match_all($pattern, $text, $matches);
    
        return $matches[0]; // Array de inscrições encontradas
    }

    private function extrairCDAs($filePath) {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();
    
        // Expressão regular para CDAs
        $pattern = '/\b\d{6}\/\d{2}-\d{2}\b/';
        preg_match_all($pattern, $text, $matches);
    
        return $matches[0];
    }

    public function processarPDF(Request $request) {
        $request->validate([
            'arquivo' => 'required|file|mimes:pdf', 
        ]);
    
        $loteController = new LoteController();

        $filePath = $request->file('arquivo')->store('temp');
        $inscricoes = $loteController->extrairInscricoes(storage_path("app/$filePath"));
    
        return response()->json($inscricoes);
    }

    public function processarPDFcomCDA(Request $request) {
        $request->validate([
            'arquivo' => 'required|file|mimes:pdf', 
        ]);
    
        $loteController = new LoteController();

        $filePath = $request->file('arquivo')->store('temp');
        $cdas = $loteController->extrairCDAs(storage_path("app/$filePath"));
    
        return response()->json($cdas);
    }

    public function getInscricaoProcesso(){
        return view('lote.inscricao_processo');
    }

    public function postInscricaoProcesso(Request $request){
        $inscricoesJson = $request->input('inscricoes');

        $inscricoes = json_decode($inscricoesJson, true);

        $dados = [];
        $lotesNaoEntrados = [];
       

        foreach($inscricoes as $inscricao){

            // Remove o hífen
            $inscricaoSemHifen = ltrim(str_replace('-', '', $inscricao), '0');

            //Lote
            $lote = Lote::where('inscricao_municipal', $inscricaoSemHifen)->first();

            if ($lote) { // Verifica se o lote foi encontrado
                $lote_id = $lote->id;
            
                // Verificar 2018 EMPRESA
                $debitoEmpresa2018 = ParcelaContaPagar::with('debito')
                    ->whereBetween('data_vencimento', ['2018-01-01', '2018-12-31'])
                    ->whereHas('debito', function ($query) use ($lote_id) {
                        $query->where('lote_id', $lote_id);
                    })
                    ->get();
            
                // Verificar 2019 EMPRESA
                $debitoEmpresa2019 = ParcelaContaPagar::with('debito')
                    ->whereBetween('data_vencimento', ['2019-01-01', '2019-12-31'])
                    ->whereHas('debito', function ($query) use ($lote_id) {
                        $query->where('lote_id', $lote_id);
                    })
                    ->get();
            
                // Verificar 2018 CLIENTE
                $debitoCliente2018 = ParcelaContaReceber::with('debito')
                    ->whereBetween('data_vencimento', ['2018-01-01', '2018-12-31'])
                    ->whereHas('debito', function ($query) use ($lote_id) {
                        $query->where('lote_id', $lote_id);
                    })
                    ->get();
            
                // Verificar 2019 CLIENTE
                $debitoCliente2019 = ParcelaContaReceber::with('debito')
                    ->whereBetween('data_vencimento', ['2019-01-01', '2019-12-31'])
                    ->whereHas('debito', function ($query) use ($lote_id) {
                        $query->where('lote_id', $lote_id);
                    })
                    ->get();
            } else {
                $lotesNaoEntrados[] = $inscricaoSemHifen; // Salva o lote não encontrado
            }            
                       

            $dados[] = [
                'lote' => $lote,
                'debitoEmpresa2018' => $debitoEmpresa2018,
                'debitoEmpresa2019' => $debitoEmpresa2019,
                'debitoCliente2018' => $debitoCliente2018,
                'debitoCliente2019' => $debitoCliente2019,
            ];

        }

        return view('lote.inscricao_processo', compact('dados', 'lotesNaoEntrados'));
    }
}