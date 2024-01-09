<?php

namespace App\Http\Controllers;
use App\Models\Cliente;
use App\Models\Quadra;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportarController extends Controller
{
    public function importarLotesCSV(Request $request, $user_id, $empreendimento_id){

        // Verificar arquivo
        if($request->hasFile('csv_file') && $request->file('csv_file')->isValid()){
            $file = $request->file('csv_file');

            $filePath = $file->getRealPath();
    
            $csvData = array_map('str_getcsv', file($filePath));
            foreach ($csvData as $row) {
                // Colunas estejam nessa ordem:  
                //quadra, lote, metros_quadrados, valor, endereco, matricula, inscricao_municipal, metragem_frente, metragem_fundo, 
                //metragem_direita, metragem_esquerda, metragem_esquina, confrontacao_frente, confrontacao_fundo, confrontacao_direita,
                // confrontacao_esquerda, cliente, data_venda
                $nomeQuadra = $row[0];
                $lote = $row[1];
                $metros_quadrados = $row[2];
                $valor = $row[3];
                $endereco = $row[4];
                $matricula = $row[5];
                $inscricao_municipal = $row[6];
                $metragem_frente = $row[7];
                $metragem_fundo = $row[8];
                $metragem_direita = $row[9];
                $metragem_esquerda = $row[10];
                $metragem_esquina = $row[11];
                $confrontacao_frente = $row[12];
                $confrontacao_fundo = $row[13];
                $confrontacao_direita = $row[14];
                $confrontacao_esquerda = $row[15];
                $nomeCliente = $row[16];
                if($row[17] != ""){
                    $data_venda = Carbon::createFromFormat('d-m-Y', $row[17])->format('Y-m-d');
                }else{
                    $data_venda = null;
                }

                // Buscar o ID do cliente usando Eloquent
                if($nomeCliente != ""){
                    $idCliente = Cliente::where('nome', $nomeCliente)->value('id');
                    if($idCliente == null){
                        return redirect()->back()->with('error', 'Cliente'.$nomeCliente.' não encontrado.');
                    }
                }

              
                // Buscar id quadra
                $idQuadra = Quadra::where('nome', $nomeQuadra)
                ->where('empreendimento_id', $empreendimento_id)
                ->value('id');

                if($idQuadra == null){
                    return redirect()->back()->with('error', 'Quadra não encontrada.');
                }
    
                // Inserir na tabela lote
                Lote::create([
                    'quadra_id' => $idQuadra,
                    'lote' => $lote,
                    'metros_quadrados' => $metros_quadrados,
                    'valor' => ($valor != '') ? floatval(str_replace(',', '.', $valor)) : null,
                    'endereco' => $endereco,
                    'matricula' => $matricula,
                    'inscricao_municipal' => $inscricao_municipal,
                    'metragem_frente' => $metragem_frente,
                    'metragem_fundo' => $metragem_fundo,
                    'metragem_direita' => $metragem_direita,
                    'metragem_esquerda' => $metragem_esquerda ,
                    'metragem_esquina' => $metragem_esquina ,
                    'confrontacao_frente' => $confrontacao_frente,
                    'confrontacao_fundo' => $confrontacao_fundo ,
                    'confrontacao_direita' => $confrontacao_direita ,
                    'confrontacao_esquerda' => $confrontacao_esquerda,
                    'cliente_id' => $nomeCliente != "" ? $idCliente : null,
                    'data_venda' => $nomeCliente != "" ? trim($data_venda) : null,
                    'data_cadastro' => Carbon::now()->format('Y-m-d H:i:s'),
                    'cadastrado_usuario_id' => 1, //ALTERAR
                ]);
            }
    
            return redirect()->back()->with('success', 'Dados importados com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Arquivo Inválido');
        }
       
    }
}
