@extends('layouts.main')
@section('title','Nuevo Proveedor')
@section('header','Nuevo Proveedor')

@push('styles')
<style>
  :root{ --verde-oscuro:#2e7d32; --verde-medio:#66bb6a; --borde:#d6d6d6; }
  .card{ border-radius:12px; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,.06); }
  .card-body{ padding:1.25rem 1.5rem; }
  .form-label{ color:var(--verde-oscuro); font-weight:600; }
  .form-control,.form-select{ border:1px solid var(--borde); border-radius:8px; padding:10px; transition:.2s; }
  .form-control:focus,.form-select:focus{ border-color:var(--verde-medio); box-shadow:0 0 0 .2rem rgba(102,187,106,.25); }
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

  <form method="POST" action="{{ route('proveedores.store') }}" class="row g-3">
    @csrf
    <div class="col-md-6">
      <label class="form-label">Nombre</label>
      <input name="nombre" class="form-control" maxlength="150" value="{{ old('nombre') }}" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Teléfono</label>
      <input name="telefono" class="form-control" maxlength="30" value="{{ old('telefono') }}">
    </div>
    <div class="col-12">
      <label class="form-label">Dirección</label>
      <input name="direccion" class="form-control" maxlength="255" value="{{ old('direccion') }}">
    </div>

    {{-- checkbox activo (hidden para forzar 0 cuando se desmarca) --}}
    <input type="hidden" name="activo" value="0">
    <div class="col-md-3 d-flex align-items-center">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="chkActivo" name="activo" value="1"
               {{ old('activo', 1) ? 'checked' : '' }}>
        <label class="form-check-label" for="chkActivo">Activo</label>
      </div>
    </div>

    <div class="col-12 d-flex gap-2 justify-content-end">
      <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Guardar</button>
      <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-xmark me-1"></i> Cancelar</a>
    </div>
  </form>
</div></div>
@endsection
