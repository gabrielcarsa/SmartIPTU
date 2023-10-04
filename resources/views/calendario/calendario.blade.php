@extends('layouts/app')

@section('conteudo')
<h2>Calendário Financeiro</h2>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>

<div class="card">
    <h5 class="card-header">Calendário de Contas a Pagar</h5>
    <div class="card-body">
        <div id='calendar'></div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="eventoModalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="" class="btn btn-primary redirect">Ver mais</a>
            </div>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var eventosDebitos = @json($eventosDebitos); // Converte a variável PHP para um objeto JavaScript
    var eventosOutros = @json($eventosOutros); // Converte a variável PHP para um objeto JavaScript
    var eventosDoCalendario = [];

    eventosDebitos.forEach(function(item) {
        eventosDoCalendario.push({
            title: item.descricao + " | " + item.tipo_debito_descricao,
            start: item.data_vencimento,
            valor: item.valor_parcela,
            numParcela: item.numero_parcela,
            qtdParcela: item.quantidade_parcela,
            data_vencimento: item.data_vencimento,
            idParcela: item.id,
            lote: item.lote,
        });
    });

    eventosOutros.forEach(function(item) {
        eventosDoCalendario.push({
            title: item.descricao + " | " + item.nome,
            start: item.data_vencimento,
            valor: item.valor_parcela,
            numParcela: item.numero_parcela,
            qtdParcela: item.quantidade_parcela,
            data_vencimento: item.data_vencimento,
            idParcela: item.id,
            lote: item.lote,
        });
    });

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        events: eventosDoCalendario,
        buttonText: {
            today: 'Hoje'
        },
        dayHeaderFormat: {
            weekday: 'short',
        },
        eventClick: function(info) {
            $('#eventoModalLabel').text(info.event.title);

            var dataVencimentoUTC = new Date(info.event.extendedProps.data_vencimento +
                'T00:00:00Z');
            var dataVencimentoLocal = new Date(dataVencimentoUTC.getTime() + dataVencimentoUTC
                .getTimezoneOffset() * 60000);

            var dataFormatada = dataVencimentoLocal.toLocaleDateString('pt-BR', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });

            var eventoInfo =
                '<p id="data_vencimento_modal"> <span class="material-symbols-outlined">event</span>' +
                dataFormatada +
                '</p><p> <span class="material-symbols-outlined">paid</span> Valor da parcela: R$ ' +
                info.event.extendedProps.valor +
                '</p><p> <span class="material-symbols-outlined">filter_1</span> N° da parcela: ' +
                info.event.extendedProps.numParcela + ' de ' + info.event
                .extendedProps.qtdParcela + '</p>';

            $('#eventoModal .modal-body').html(eventoInfo);

            // Define o novo href do elemento <a> com base nos detalhes do evento
            if(info.event.extendedProps.lote == null){
                var novoHref = "/contas_pagar/listar?idParcela=" + info.event.extendedProps.idParcela + "&titular_conta_id=0&referenteOutros=on";
            }else{
                var novoHref = "/contas_pagar/listar?idParcela=" + info.event.extendedProps.idParcela + "&titular_conta_id=0&referenteLotes=on";
            }
            $('.redirect').attr("href", novoHref);

            // Exibe o modal
            $('#eventoModal').modal('show');
        }
    });

    calendar.render();
});
</script>
@endsection