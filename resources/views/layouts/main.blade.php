<?php
use App\Game;
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

		<title>Auth Landing</title>
		{{ HTML::style('assets/css/bootstrap.min.css') }}
		{{ HTML::style('assets/css/bootstrap-select.min.css') }}
		{{ HTML::style('assets/css/bootstrap-datepicker3.min.css') }}
		{{ HTML::style('assets/css/auth.css') }}
		{{ HTML::script('assets/js/jquery-1.11.1.min.js') }}
		{{ HTML::script('assets/js/bootstrap.min.js') }}
		{{ HTML::script('assets/js/bootstrap-select.min.js') }}
		{{ HTML::script('assets/js/bootstrap-datepicker.js') }}

		<link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'>
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->

		<script>
			function getQueryParams(qs) {
				qs = qs.split("+").join(" ");

				var params = {}, tokens, re = /[?&]?([^=]+)=([^&]*)/g;

				while (tokens = re.exec(qs)) {
					params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
				}

				return params;
			}
		</script>
	</head>

	<body>

		<!-- Static navbar -->
		<div class="navbar navbar-inverse navbar-static-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".top-nav-bar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="{{ URL::to('/') }}">Auth Landing</a>
				</div>
				<div class="top-nav-bar navbar-collapse collapse">
					<ul class="nav navbar-nav">
						@if (Auth::check())
							<li{{ Request::is('/') || Request::is('home') ? ' class="active"' : '' }}><a href="{{ URL::to('/') }}">Home</a></li>

							<li class="dropdown{{ Request::is('games') || Request::is('games/*') ? ' active' : '' }}">
								<a href="{{ URL::to('games') }}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Games <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
								<?php $count = 0; ?>
								@foreach (Game::get() as $game)
									@if ($count > 0)
										<li role="presentation" class="divider"></li>
									@endif
									<li><a href="{{ URL::to('games/'.$game->GAbbr) }}">{{ $game->GName }}</a></li>
									@foreach ($game->orgs()->get() as $org)
										<li class="dropdown-header"><a href="{{ URL::to('games/'.$game->GAbbr.'/'.$org->GOAbbr) }}">{{ $org->GOName }}</a></li>
									@endforeach
									<?php ++$count; ?>
								@endforeach
								</ul>
							</li>

							@if (!is_null(Auth::user()->USAUserID))
								<li{{ Request::is('sponsor') || Request::is('sponsor/*') ? ' class="active"' : '' }}><a href="{{ URL::to('sponsor') }}">Sponsor</a></li>
							@endif
						@endif
					</ul>
					<ul class="nav navbar-nav navbar-right">
						@if (Auth::check())
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ Auth::user()->UGoonID }} <b class="caret"></b></a>
								<ul class="dropdown-menu">
									@if (Auth::user()->is_admin)
									<li><a href="{{ URL::to('admin') }}">Admin</a></li>
									@endif
									<li><a href="{{ URL::to('logout') }}">Logout</a></li>
								</ul>
							</li>
							<li><a href="{{ config('goonauth.forum_url') }}">Forum</a></li>
						@else
							<li><a href="{{ URL::to('login') }}">Login</a></li>
							<li><a href="{{ config('goonauth.forum_url') }}">Forum</a></li>
						@endif
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>

		<div class="container">
			@yield('content')
		</div>

		<div class="container">
			<div class="row">
				<div class="col-md-12 footer">
				GoonAuth originally created by {{ HTML::link('https://github.com/sct', 'sct', array('target' => '_blank')) }} - {{ HTML::link('https://github.com/sct/GoonAuth', 'GitHub', array('target' => '_blank')) }}<br>
				FLJK v{{ config('goonauth.version') }} - Created by {{ HTML::link('https://github.com/LoneBoco', 'Nalin', array('target' => '_blank')) }} - {{ HTML::link('https://github.com/LoneBoco/GoonAuth', 'GitHub', array('target' => '_blank')) }}
				</div>
			</div>
		</div>
	</body>
</html>
