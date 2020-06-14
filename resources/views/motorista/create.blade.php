@extends('layouts.app')

@section('content')
<div class="row bg-light ">
    <div class="col-sm-6" style="text-size:20px !important;">
    <h5 class="display-4 text-dark d-inline">Novo Motorista</h5> 
    </div>
  </div>

<div class="row bg-light h-100">
     <div class="col-sm-2 pt-4">
        ​<picture>
            <source srcset="{{url('img/usuariopadrao.png')}}" type="image/svg+xml">
             <img src="{{url('img/usuariopadrao.png')}}" class="img-fluid img-thumbnail" alt="...">
        </picture>
     </div>
<div class="col-sm-10">
<form action="/motoristas/store" method="post" class="p-2">
 @csrf
<div class="row my-2">
    <div class="form-group col-sm-2">
        <label for="matricula" class="m-0">Matricula</label>
        <input type="numeric" required class="form-control m-0" id="matricula" name="matricula" placeholder="ex: 1234">
    </div>
    <div class="form-group col-sm-8">
        <label for="nomecompleto" required class="m-0">Nome Completo</label>
        <input type="text" class="form-control m-0" id="nomecompleto" name="nomecompleto" placeholder="ex: fulano da silva peixoto">
    </div>
</div>
<div class="row my-2">
    <div class="form-group col-sm-4">
        <label for="nomeescala" required class="m-0">Nome de Escala</label>
        <input type="text" class="form-control m-0" id="nomeescala" name="nomedeescala"  placeholder="ex: 1234 - peixoto">
    </div>
      <div class="form-group col-sm-4">
        <label for="cpf" required class="m-0">CPF</label>
        <input type="numeric" class="form-control m-0" id="cpf" name="cpf"  placeholder="ex: 000.000.000-00">
    </div>
</div>

<div class="row my-2">
    <div class="form-group col-sm-4">
        <label for="nascimento" required class="m-0">Data de Nascimento</label>
        <input type="text" class="form-control m-0" id="nascimento" name="datanascimento"  placeholder="ex: 01/01/2020">
    </div>
    <div class="form-group col-sm-4">
        <label for="cracha" class="m-0">id Crachá</label>
        <input type="numeric" class="form-control m-0" id="cracha" name="idcracha"  placeholder="ex: 0000000000">
    </div>
</div>


<div class="row my-2">
    <div class="col-sm-10"> </div>
    <div class="col-sm-2" > 
         <button type="submit" class="btn  btn-outline-primary">Salvar</button>
         <a href="/motoristas"  class="btn  btn-outline-danger ml-1"> <i class="fas fa-times text-danger"></i> Cancelar </a>
    </div>
</div>


</form>
</div>
</div>

@endsection