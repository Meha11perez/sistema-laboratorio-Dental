@extends('layouts.app')
@section('title', 'Registrar compra')
@section('content')
<div class="cc compras">@include('compras.partials.cabecera', ['titulo'=>'Registrar compra'])
    <form class="cc-card" action="{{ route('compras.store') }}" method="POST" enctype="multipart/form-data" data-compra-form>
        @csrf<input type="hidden" name="clave_registro" value="{{ old('clave_registro', (string) \Illuminate\Support\Str::uuid()) }}">
        @include('compras.partials.campos')
        <div class="cc-actions mt-5"><a class="cc-btn" href="{{ route('compras.index') }}">Cancelar</a><button class="cc-btn cc-btn-primary" type="submit">Registrar compra</button></div>
    </form>
</div>
@endsection
