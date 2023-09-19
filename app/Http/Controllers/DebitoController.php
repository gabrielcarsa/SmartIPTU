<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Debito;
use App\Models\Parcela;
use App\Models\Descricao_Debito;
use App\Models\Tipo_Debito;
use Carbon\Carbon;
use App\Http\Requests\DebitoRequest;


class DebitoController extends Controller
{
    //RETORNA VIEW PARA ADICIONAR DÉBITO
    function novo($lote_id){
        $tipo_debito = Tipo_Debito::all();
        $descricao_debito = Descricao_Debito::all();

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
        $debito->data_vencimento = $request->input('data_vencimento');
        $debito->descricao_debito_id = $request->input('descricao_debito_id');
        $debito->valor_parcela = $request->input('valor_parcela');
        $debito->valor_entrada = $request->input('valor_entrada');
        $debito->observacao = $request->input('observacao');
        $debito->data_cadastro = date('d-m-Y h:i:s a', time());
        $debito->cadastrado_usuario_id = $usuario;
        $debito->save();

        // Cadastrar Parcelas
        $qtd_parcelas = $request->input('quantidade_parcela');
        $debito_id = $debito->id;
        $data_vencimento = $debito->data_vencimento; 
        $dataCarbon = Carbon::createFromFormat('Y-m-d', $data_vencimento);
        for($i = 1; $i <= $qtd_parcelas; $i++){
            $parcela = new Parcela();
            $parcela->debito_id = $debito_id;
            $parcela->numero_parcela = $i;
            $parcela->valor_parcela = $debito->valor_parcela;
            $parcela->cadastrado_usuario_id = $usuario;
            if($i > 1){
                $parcela->data_vencimento = $dataCarbon->addMonth();
            }else{
                $parcela->data_vencimento = $data_vencimento;
            }
            $parcela->save();
        }

        return redirect('lote/gestao/'.$lote_id)->with('success', 'Débito cadastrado com sucesso');
    }
}