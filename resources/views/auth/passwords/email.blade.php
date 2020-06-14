<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Mobs2</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/css/AdminLTE.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/css/skins/_all-skins.min.css"> -->
    <link rel="stylesheet" href="/css/skin.css">
    <link rel="stylesheet" href="/css/style.css">

    <!-- iCheck -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/square/_all.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="hold-transition loginPage">
    <div class="banner">
    <svg version="1.1" x="0px" y="0px" viewBox="0 0 169 810" style="enable-background:new 0 0 169 810;" xml:space="preserve">
        <style type="text/css">
            .st0{fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;}
        </style>
        <path class="st0" d="M169,810V0c-12.9,0-35.2,0-71.8,0C289.7,473.6-0.2,810-0.2,810H169z"/>
    </svg>

        </div>
    </div>
    <div class="loginBox closed">
        <div class="loginController">
            <div>
                <img src="/images/logo_blustock.png">

                <form method="post" action="{{ url('/password/email') }}">
                    {!! csrf_field() !!}

                    <div class="formControl has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                        <input type="email" class="inputControl labelController" name="email" value="{{ old('email') }}" placeholder="Email">
                        <label class="formControlFeedback">
                            Email
                        </label>
                        {{-- <span class="glyphicon glyphicon-envelope form-control-feedback"></span> --}}
                        @if ($errors->has('email'))
                            <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="row loginExtra">
                        <div class="col-xs-6">
                            <a href="{{ url('/login') }}" class="forgotPass">
                                <i class="fa fa-unlock"></i> Voltar para o login
                            </a>
                        </div>
                        <div class="col-xs-6">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">Resetar senha</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/js/adminlte.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>

<script src="/js/controller.js"></script>

</body>
</html>