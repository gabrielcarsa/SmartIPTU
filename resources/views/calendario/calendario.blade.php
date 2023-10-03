@extends('layouts/app')

@section('conteudo')

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>

<div id='calendar'></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var eventos = @json($eventos); // Converte a variável PHP para um objeto JavaScript
    var eventosDoCalendario = [];

    eventos.forEach(function(item) {
        eventosDoCalendario.push({
            title: item.descricao + " " + item.tipo_debito_descricao,
            start: item.data_vencimento,
            url: '', // Adicione a URL desejada aqui
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
        }
    });
    calendar.render();
});
</script>
@endsection