@extends('layouts.main')
@section('title','Nuevo Usuario')
@section('header','Nuevo Usuario')

@push('styles')
<style>
  :root{ --verde-oscuro:#2e7d32; --verde-medio:#66bb6a; --borde:#d6d6d6; }
  .card{ border-radius:12px; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,.06); }
  .card-body{ padding:1.25rem 1.5rem; }
  .form-label{ color:var(--verde-oscuro); font-weight:600; }
  .form-control{ border:1px solid var(--borde); border-radius:8px; padding:10px; transition:.2s; }
  .form-control:focus{ border-color:var(--verde-medio); box-shadow:0 0 0 .2rem rgba(102,187,106,.25); }
  .btn.btn-primary{ background:var(--verde-oscuro); border:none; font-weight:600; }
  .btn.btn-primary:hover{ background:var(--verde-medio); }
  .btn-outline-secondary{ border-color:var(--verde-oscuro); color:var(--verde-oscuro); font-weight:600; }
  .btn-outline-secondary:hover{ background:var(--verde-oscuro); color:#fff; }
</style>
@endpush

@section('content')
<div class="card"><div class="card-body">
@if ($errors->any())
  <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="POST" action="{{ route('usuarios.store') }}" class="row g-3">@csrf
  <div class="col-md-6">
    <label class="form-label">Nombre</label>
    <input name="nombre" class="form-control" value="{{ old('nombre') }}" required maxlength="150">
  </div>
  <div class="col-md-6">
    <label class="form-label">Teléfono</label>
    <input name="telefono" class="form-control" value="{{ old('telefono') }}" maxlength="30">
  </div>
  <div class="col-md-6">
    <label class="form-label">Usuario</label>
    <input name="usuario" class="form-control" value="{{ old('usuario') }}" required maxlength="50">
  </div>
  <div class="col-md-6">
    <label class="form-label">Correo</label>
    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required maxlength="150">
  </div>
  <div class="col-md-6">
    <label class="form-label">Contraseña</label>
    <input type="password" name="password" class="form-control" required minlength="6">
  </div>
  <div class="col-md-6">
    <label class="form-label">Confirmar contraseña</label>
    <input type="password" name="password_confirmation" class="form-control" required minlength="6">
  </div>

  <div class="col-12 d-flex gap-2 justify-content-end">
    <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Guardar</button>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-xmark me-1"></i> Cancelar</a>
  </div>
</form>
</div></div>
@endsection
