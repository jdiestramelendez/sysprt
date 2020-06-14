@extends('layouts.app')

@section('content')
<div class="row bg-light ">
    <div class="col-sm-6" style="text-size:20px !important;">
    <h5 class="display-4 text-dark d-inline">Novo Usuário do Sistema</h5> 
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
<form action="/usuarios/store" method="post" class="p-2">
 @csrf
<div class="row my-2">
    <div class="form-group col-sm-4">
        <label for="name" class="m-0">Nome</label>
        <input type="numeric" required class="form-control m-0" id="name" name="name" placeholder="ex: Fulano Sicrano">
    </div>
    <div class="form-group col-sm-6">
        <label for="email" required class="m-0">Email</label>
        <input type="text" class="form-control m-0" id="email" name="email" placeholder="ex: exemplo@email.com">
    </div>
</div>
<div class="row my-2">
    <div class="form-group col-sm-4">
        <label for="senha" required class="m-0">Senha</label>
        <input type="password" class="form-control m-0" id="password" name="password"  placeholder="ex: *******">
    </div>
</div>

<div class="row my-2">
    <div class="col-sm-10"> </div>
    <div class="col-sm-2" > 
         <button type="submit" class="btn  btn-outline-primary">Salvar</button>
         <a href="/usuarios"  class="btn  btn-outline-danger ml-1"> <i class="fas fa-times text-danger"></i> Cancelar </a>
    </div>
</div>


</form>
</div>
</div>

@endsection