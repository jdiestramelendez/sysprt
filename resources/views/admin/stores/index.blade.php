@extends('layout.app')

@section('content')
<table class="table-striped">
    <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>Loja</th>
            <th>description</th>
            <th>    </th>
        </tr>
    </thead>
    <tbody>
        @foreach($stores as $store)
        <tr>
            <td>{{$store->id}}</td>
            <td>{{$store->name}}</td>
            <td>{{$store->description}}</td>
            <td>
            <a class="btn btn-sm btn-outline-primary mx-1">Editar </a>
            <a class="btn btn-sm btn-outline-danger mx-1">Excluir </a>
            </td>
        </tr>

        @endforeach
    </tbody>
</table>

{{$stores->links()}}

@endsection