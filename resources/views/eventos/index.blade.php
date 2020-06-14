@extends('./layouts.app')

@section('content')

<div class="row" id="Rotulo">
    <div class="col-sm-2">
        <h5 class="display-4 text-dark d-inline mr-1"> Eventos</h5>
    </div>
    <eventos-view> </eventos-view>
</div>


@endsection
