@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="col-md-10 offset-md-1 dashboard-title-container">
    <h1>Meus eventos</h1>
</div>
<div class="col-md-10 offset-md-1 dashboard-events-container">
 @if(count($events)>0)
 {{-- rever a acao --}}
    <table class="table datatables table-hover table-bordered" id="dataTable-1">
        <thead class="thead-dark">
         <tr class="text-center">
            <th scope="col">#</th>
            <th scope="col">Nome</th>
            <th scope="col">participantes</th>
            <th>Acao</th>
        </tr>
    </thead>

<tbody class="bg-white">
  @foreach ($events as $event)
     <tr class="text-center">
        <th scropt="row">{{ $loop->index +1 }}</th>
        <th><a href="/events/{{ $event->id }}">{{ $event->title }}</a> </th>
        <th>{{ count($event->users) }}</th>
     <td>
        <form action="/events/{{ $event->id }}" method="post"  class="row">
         <a href="/events/edit/{{ $event->id }}" class="btn btn-info edit-btn"><ion-icon name="create-outline"></ion-icon>Editar</a>
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger delete-btn"><ion-icon name="trash-outline"></ion-icon>Eliminar</button>


        </form>
     </td>
   </tr>
    @endforeach

</tbody>
</table>
<script src="/js/datatables/jquery-3.5.1.js"></script>

    @else
    <p>Não tens eventos <a href="/events/create">Criar eventos</a></p>
    @endif
</div>
<div class="col-md-10 offset-md-1 dashboard-title-container">
    <h1> Eventos que estou participando</h1>
</div>
<div class="col-md-10 offset-md-1 dashboard-events-container">
    @if(count($eventAsParticipant)>0):
    <table class="table datatables table-hover table-bordered" id="dataTable-1">
        <thead class="thead-dark">
         <tr class="text-center">
            <th scope="col">#</th>
            <th scope="col">Nome</th>
            <th scope="col">participantes</th>
            <th>Acao</th>
        </tr>
    </thead>

<tbody class="bg-white">
  @foreach ($eventAsParticipant as $event)
     <tr class="text-center">
        <th scropt="row">{{ $loop->index +1 }}</th>
        <th><a href="/events/{{ $event->id }}">{{ $event->title }}</a> </th>
        <th>{{ count($event->users) }}</th>
     <td>
      <form action="/events/leave/{{ $event->id }}" method="Post">
          @csrf
          @method("DELETE")
          <button type="submit" class="btn btn-danger delete-btn">
            <ion-icon name="trash-outline"></ion-icon>Sair do evento
          </button>
 
      </form>

     </td>
   </tr>
    @endforeach

</tbody>
</table>
    @else
<p>Voce não participa de nenhum evento<a href="/">Veja os eventos</a></p>
@endif
</div>
@endsection
