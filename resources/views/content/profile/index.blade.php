@extends('layouts.app')

@section('content')
<div class="container">
    <div class="justify-content-center">
        <div class="card">
            <div class="card-header">
                {{ $user->name }} Profile
            </div>
            <div class="card-body">
                @if(Session::get('message'))
                    <div class="mb-2 alert alert-{{ Session::get('status') ? Session::get('status') : 'secondary' }} alert-dismissible fade show mb-0" role="alert">
                        <span>{{ Session::get('message') }}</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form action="{{ route('profile.update', $user->id) }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="PUT" readonly>

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $user->name }}" placeholder="Name">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ $user->email }}" placeholder="Username">
                        @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="float-right btn-group">
                        <button type="reset" class="btn btn-danger">Reset</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection