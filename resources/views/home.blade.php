@extends('layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">大事报</div>

        <div class="panel-body">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            登陆成功
        </div>
    </div>
@endsection
