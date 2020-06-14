@extends('layouts.app')

@section('content')
<div class="row bg-light ">
    <div class="col-sm-10" style="text-size:20px !important;">
    <h5 class="display-4 text-dark d-inline">Editando Usuario: <span class="text-primary">{{$usuario->name}}</span></h5> 
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
<form action="/usuarios/update" method="post" class="p-2">
 @csrf
 <input type="hidden" value="{{$usuario->id}}"  name="id" />
<div class="row my-2">
    <div class="form-group col-sm-2">
        <label for="name" class="m-0">Nome</label>
        <input type="numeric" required class="form-control m-0" value="{{$usuario->name}}" name="name" placeholder="ex: Fulano Sicrano">
    </div>
    <div class="form-group col-sm-8">
        <label for="email" required class="m-0">Email</label>
        <input type="text" class="form-control m-0" value="{{$usuario->email}}" name="email" placeholder="ex: fulano da silva peixoto">
    </div>
</div>
<div class="row my-2">
    <div class="col-sm-7"> </div>
    <div class="col-sm-5" > 
         <button type="submit" class="btn  btn-outline-primary">Salvar Edição</button>
          <a href="/motoristas"  class="btn  btn-outline-danger ml-1"> <i class="fas fa-times text-danger"></i> Resetar Senha </a>
         <a href="/motoristas"  class="btn  btn-outline-danger ml-1"> <i class="fas fa-times text-danger"></i> Cancelar </a>
    </div>
</div>


</form>
</div>
</div>

@endsection