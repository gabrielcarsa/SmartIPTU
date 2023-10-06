<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;


class DashboardController extends Controller
{
    function dashboard(){
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

       $total_titular_conta = $titulares_contas->count();
 
       $clientes = Cliente::all();


       $data = [
           'total_titular_conta' => $total_titular_conta,
           'titulares_contas' => $data,
           'clientes' => $clientes,
       ];


       return view('dashboard', compact('data'));



    }
}
