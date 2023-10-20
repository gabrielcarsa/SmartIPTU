<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\TitularConta;
use App\Models\Empreendimento;
use App\Models\TipoDebito;


class DashboardController extends Controller
{
    function dashboard(){

        //Consultas para relacionar debitos e contas a pagar a titulares das contas
        $debitos_titulares = DB::table('parcela as p')
           ->selectRaw('tc.id as id')
           ->selectRaw('SUM(p.valor_parcela) as total_debitos')
           ->selectRaw('NULL as total_contas_pagar')
           ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
           ->join('debito as d', 'p.debito_id', '=', 'd.id')
           ->join('lote as l', 'd.lote_id', '=', 'l.id')
           ->join('cliente as c', 'l.cliente_id', '=', 'c.id')
           ->join('titular_conta as tc', 'd.titular_conta_id', '=', 'tc.id')
           ->leftJoin('cliente AS titular_conta_cliente', 'tc.cliente_id', '=', 'titular_conta_cliente.id')
           ->whereColumn('l.cliente_id', '=', 'tc.cliente_id')
           ->whereNull('p.situacao')
           ->groupBy('tc.id', 'nome_cliente_ou_razao_social');


        $conta_pagar_titulares = DB::table('parcela_conta_pagar as p')
           ->selectRaw('tc.id as id')
           ->selectRaw('NULL as total_debitos')
           ->selectRaw('SUM(p.valor_parcela) as total_contas_pagar')
           ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
           ->join('conta_pagar as cp', 'p.conta_pagar_id', '=', 'cp.id')
           ->join('titular_conta as tc', 'cp.titular_conta_id', '=', 'tc.id')
           ->leftJoin('cliente AS titular_conta_cliente', 'tc.cliente_id', '=', 'titular_conta_cliente.id')
           ->whereNull('p.situacao')
           ->groupBy('tc.id', 'nome_cliente_ou_razao_social');

        //Unindo resultados
        $titulares_contas = $debitos_titulares->union($conta_pagar_titulares)->get();
        $data = [];
        foreach ($titulares_contas as $titular) {
            $existingTitularKey = null;
        
            foreach ($data as $key => $item) {
                if ($item['id'] == $titular->id) {
                    $existingTitularKey = $key;
                    break;
                }
            }
        
            if ($existingTitularKey !== null) {
                // Se o titular já existe, verifique e preencha os campos vazios
                if ($titular->total_debitos !== null && $data[$existingTitularKey]['total_debitos'] === null) {
                    $data[$existingTitularKey]['total_debitos'] = $titular->total_debitos;
                }
                if ($titular->total_contas_pagar !== null && $data[$existingTitularKey]['total_contas_pagar'] === null) {
                    $data[$existingTitularKey]['total_contas_pagar'] = $titular->total_contas_pagar;
                }
            } else {
                // Se o titular não existe, adicione-o ao array $data
                $data[] = [
                    'id' => $titular->id,
                    'total_debitos' => $titular->total_debitos,
                    'total_contas_pagar' => $titular->total_contas_pagar,
                    'nome_cliente_ou_razao_social' => $titular->nome_cliente_ou_razao_social,
                ];
            }
        }

        //Total de titulares
        $total_titular_conta = $titulares_contas->count();
    

        //select referente a parcelas de contas a pagar de lotes
        $queryDebitos = DB::table('parcela as p')
            ->selectRaw('SUM(p.valor_parcela) as total_debitos')
            ->selectRaw('CASE WHEN c.razao_social IS NOT NULL THEN c.razao_social ELSE c.nome END AS nome_cliente_ou_razao_social')
            ->join('debito as d', 'p.debito_id', '=', 'd.id')
            ->join('lote as l', 'd.lote_id', '=', 'l.id')
            ->join('cliente as c', 'l.cliente_id', '=', 'c.id')
            ->join('titular_conta as tc', 'd.titular_conta_id', '=', 'tc.id')
            ->whereNull('p.situacao')
            ->groupBy('l.cliente_id', 'nome_cliente_ou_razao_social')
            ->get();

        //Consultar titulares de contas
        $conta_pagar_titulares = $conta_pagar_titulares->get();

        //Definir Empresa Principal responsável pelos débitos
        foreach($conta_pagar_titulares as $titular_conta){
            if($titular_conta->id == 1){
                $titular_empresa_principal_id = $titular_conta->id;
                $titular_empresa_principal_nome = $titular_conta->nome_cliente_ou_razao_social;
            }
        }

        $debitosEmpresa = [];
        $debitosValorClientes = 0;

        //Dividir em Débitos de Empresa e Clientes
        foreach ($queryDebitos as $debitos) {
            
            if ($debitos->nome_cliente_ou_razao_social == $titular_empresa_principal_nome) {
                $debitosEmpresa = [
                    'total_debitos' => $debitos->total_debitos,
                    'nome_cliente_ou_razao_social' => $debitos->nome_cliente_ou_razao_social,
                ];
    
            } else {
                $debitosValorClientes += $debitos->total_debitos;
            }
        }

        //Total Débitos de Clientes
        $debitosClientes = [
            'total_debitos' => $debitosValorClientes,
            'nome_cliente_ou_razao_social' => 'CLIENTES',

        ];

        //Unindo os dois resultados
        $debitosEmpresaCliente = [
            $debitosClientes,
            $debitosEmpresa,
        ];

        //Alimentar gráfico de Receber Débitos por ano
        $receberPorAnos = DB::table('parcela as p')
            ->selectRaw("EXTRACT(YEAR FROM p.data_vencimento) as ano_vencimento")
            ->selectRaw('SUM(p.valor_parcela) as total_debitos')
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
            ->whereNull('p.situacao')
            ->groupBy('ano_vencimento')
            ->orderBy('ano_vencimento', 'ASC')
            ->get();

        //Alimentar gráfico de Receber Débitos por ano
        $lotesEmpreendimentos = DB::table('empreendimento as e')
            ->select(
                'e.nome as empreendimento', 
                DB::raw('COUNT(l.id) as total_lotes')
            )
            ->join('quadra as q', 'e.id', '=', 'q.empreendimento_id')
            ->join('lote as l', 'q.id', '=', 'l.quadra_id')
            ->join('debito as d', 'l.id', '=', 'd.lote_id')
            ->groupBy('empreendimento')
            ->orderBy('empreendimento', 'ASC')
            ->get();


        /*$lotesAjuizadosPorEmpreendimento = DB::table('debito as d')
            ->select(
                'e.nome as empreendimento', 
                DB::raw('COUNT(DISTINCT l.id) as total_lotes_ajuizados'),
                DB::raw('
                    (SELECT COUNT(l.id)
                    FROM empreendimento as e JOIN quadra as q ON e.id = q.empreendimento_id 
                    JOIN lote as l ON q.id = l.quadra_id 
                    group by e.nome
                    order by e.nome ASC ) as total_lotes')
            )
            ->join('lote as l', 'd.lote_id', '=', 'l.id')
            ->join('quadra as q', 'l.quadra_id', '=', 'q.id')
            ->join('empreendimento as e', 'e.id', '=', 'q.empreendimento_id')
            ->where('d.tipo_debito_id', 1)
            ->groupBy('e.nome')
            ->get();*/

            $tipo_debitos = TipoDebito::limit(4)->get();

            $lotesEmpreendimentos = DB::table('empreendimento as e')
            ->select(
                'e.nome as empreendimento',
                DB::raw('COUNT(DISTINCT l.id) as total_lotes'),
                DB::raw('COUNT(DISTINCT CASE WHEN d.tipo_debito_id = 1 THEN l.id END) as total_lotes_1'),
                DB::raw('COUNT(DISTINCT CASE WHEN d.tipo_debito_id = 2 THEN l.id END) as total_lotes_2'),
                DB::raw('COUNT(DISTINCT CASE WHEN d.tipo_debito_id = 3 THEN l.id END) as total_lotes_3'),
                DB::raw('COUNT(DISTINCT CASE WHEN d.tipo_debito_id = 4 THEN l.id END) as total_lotes_4'),

            )
            ->join('quadra as q', 'e.id', '=', 'q.empreendimento_id')
            ->join('lote as l', 'q.id', '=', 'l.quadra_id')
            ->leftJoin('debito as d', 'l.id', '=', 'd.lote_id')
            ->groupBy('e.nome')
            ->orderBy('e.nome', 'ASC')
            ->get();

        $data = [
            'total_titular_conta' => $total_titular_conta,
            'titulares_contas' => $data,
            'debitosEmpresaCliente' => $debitosEmpresaCliente,
            'receberPorAnos' => $receberPorAnos->toArray(),
            'lotesEmpreendimentos' => $lotesEmpreendimentos,
            'tipo_debitos' => $tipo_debitos
        ];


        return view('dashboard', compact('data'));



    }
}
