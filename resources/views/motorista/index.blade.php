@extends('./layouts.app')

@section('content')
  <div class="row bg-light w-100n" id="Motorista">
    <div class="col-sm-6" style="text-size:20px !important;">
    <h5 class="display-4 text-dark d-inline"> Motoristas</h5> 
    </div>
    <div class="col-sm-3"> </div>
    <div class="col-sm-3 pt-2">
      <a href="/motoristas/novo" class="btn btn-sm btn-outline-dark "><i class="fas fa-user-plus"></i> Novo</a>
       <a href="/motoristas" class="btn btn-sm btn-outline-dark"> <i class="fas fa-retweet"></i> Recarregar</a>
       <a href="/home"  class="btn btn-sm btn-outline-dark"> <i class="fas fa-times text-danger"></i> Sair </a>
    </div>
  </div>

<table class="table w-100 bg-light">
  <thead class="thead-dark">
    <tr style="height: 20px !important;">
      <th scope="col">Matricula</th>
      <th scope="col">Nome de Escala</th>
      <th scope="col">Nome Completo</th>
      <th scope="col">CPF</th>
      <th scope="col">Data de Nascimento</th>
      <th>  </th>
    </tr>
  </thead>
  <tbody>
  @foreach ($motoristas as $motorista)
    <tr>
      <th scope="row">{{$motorista->matricula}}</th>
      <td>{{$motorista->nomedeescala}}</td>
       <td>{{$motorista->nomecompleto}}</td>
      <td>{{$motorista->cpf}}</td>
      <td>{{ date('d-m-Y',strtotime($motorista->datanascimento))}}</td>
      <td> 
      <a href="/motoristas/edit/{{$motorista->matricula}}"  class='btn btn-outline-primary text-primary'> 
      <i class="far fa-edit"></i>
      </a>
     <a href="/motoristas/delete/{{$motorista->matricula}}" class='btn btn-outline-danger text-danger'> 
       <i class="fas fa-times"></i>
      </a> 
       </td>
    </tr>
      @endforeach
  </tbody>
</table>
@endsection