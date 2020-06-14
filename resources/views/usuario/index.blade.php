@extends('./layouts.app')

@section('content')
<div class="row bg-light w-100n" id="Motorista">
    <div class="col-sm-6" style="text-size:20px !important;">
        <h5 class="display-4 text-dark d-inline"> Usuários do Sistema</h5>
    </div>
    <div class="col-sm-3"> </div>
    <div class="col-sm-3 pt-2">
        <a href="/usuarios/novo" class="btn btn-sm btn-outline-dark "><i class="fas fa-user-plus"></i> Novo</a>
        <a href="/usuarios" class="btn btn-sm btn-outline-dark"> <i class="fas fa-retweet"></i> Recarregar</a>
        <a href="/home" class="btn btn-sm btn-outline-dark"> <i class="fas fa-times text-danger"></i> Sair </a>
    </div>
</div>

<table class="table w-100 bg-light">
    <thead class="thead-dark">
        <tr style="height: 20px !important;">
            <th scope="col">Nome</th>
            <th scope="col">Email</th>
            <th scope="col">Data de Criação</th>
            <th scope="col">Empresa</th>
            <th> </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($usuarios as $usuario)
        <tr>
            <th scope="row">{{$usuario->name}}</th>
            <td>{{$usuario->email}}</td>
            <td>{{ date('d-m-Y H:i:s',strtotime($usuario->created_at))}}</td>
            <td>{{$usuario->nome}}</td>
            <td>
                <a href="/usuarios/edit/{{$usuario->id}}" class='btn btn-outline-primary text-primary'>
                    <i class="far fa-edit"></i>
                </a>
                <a href="/usuarios/delete/{{$usuario->id}}" class='btn btn-outline-danger text-danger'>
                    <i class="fas fa-times"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
