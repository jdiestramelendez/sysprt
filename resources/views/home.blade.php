@extends('layouts.app')

@section('content')
    <div class="leftbar">

    </div>
    <div class="mainController">
        
        <div class="flashMessageBox">
            @include('flash::message')
        </div>
        <?php if (!session()->get('selected_group_id')) { ?>
        <section class="app-box noCompanySelected">
            <div class="appBoxContent">
                <i class="fal fa-bullhorn alertIcon"></i>
                <h4>Você ainda não selecionou um grupo.</h4>
                <p>Para continuar, selecione o grupo que deseja gerenciar.</p>
            </div>
        </section>
        <?php }else{ ?>
            <dashboard></dashboard>
        <?php } ?>
    </div>
    <div class="rightbar">
        
    </div>
@endsection