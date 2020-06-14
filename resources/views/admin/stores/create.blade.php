@extends('layout.app')

@section('content')
<h1 class="text-center display-4"> Criar Loja </h1>
  <form class="container" action="/admin/stores/store" method="POST">
  <input type="hidden" name="_token" value="{{csrf_token()}}"/>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="name">Nome</label>
        <input type="text" class="form-control" name="name" placeholder="Nome">
      </div>
      <div class="form-group col-md-6">
        <label for="description">Descrição</label>
        <input type="text" class="form-control" name="description" placeholder="Descrição">
      </div>
    </div>
    <div class="form-row">
    <div class="form-group">
      <label for="phone">Telefone</label>
      <input type="numeric" class="form-control" name="phone" placeholder="() 0000-0000">
    </div>
    <div class="form-group">
      <label for="mobile-phone">Celular</label>
      <input type="numeric" class="form-control" name="mobile_phone" placeholder="()0 0000-0000">
    </div>
  </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="slug">Slug</label>
            <input type="text" class="form-control" name="slug">
        </div>
      <div class="form-group col-md-4">
        <label for="User">Usuario</label>
        <select name="User" class="form-control">
         
          @foreach($users as $user)
          
          <option value="{{$user->id}}">{{$user->name}}</option>

          @endforeach

        </select>
      </div>
    </div>
    <button type="submit" class="btn btn-primary" >Salvar Loja</button>
  </form>
@endsection