@extends('layouts.main')
@section('title','Editar Certificado')
@section('header','Editar Certificado')

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

<form method="POST" action="{{ route('certificados.update', $certificado) }}" class="row g-3">
  @csrf @method('PUT')

  <div class="col-md-4">
    <label class="form-label">Fecha elaboración</label>
    <input type="date" name="fecha_elaboracion" class="form-control" required
           value="{{ old('fecha_elaboracion', $certificado->fecha_elaboracion->format('Y-m-d')) }}">
  </div>

  <div class="col-md-8">
    <label class="form-label">Producto</label>
    <input name="producto" class="form-control" required
           value="{{ old('producto', $certificado->producto) }}">
  </div>

  <div class="col-md-6">
    <label class="form-label">Origen del cultivo</label>
    <input name="origen_cultivo" class="form-control"
           value="{{ old('origen_cultivo', $certificado->origen_cultivo) }}">
  </div>

  <div class="col-md-2">
    <label class="form-label">N° batch</label>
    <input type="number" name="numero_batch" class="form-control" min="1" required
           value="{{ old('numero_batch', $certificado->numero_batch) }}">
  </div>

  <div class="col-md-2">
    <label class="form-label">Cubetas</label>
    <input type="number" name="cantidad_cubetas" class="form-control" min="0" required
           value="{{ old('cantidad_cubetas', $certificado->cantidad_cubetas) }}">
  </div>

  <div class="col-md-2">
    <label class="form-label">Kg por cubeta</label>
    <input type="number" step="0.001" name="peso_por_cubeta" class="form-control" required
           value="{{ old('peso_por_cubeta', $certificado->peso_por_cubeta) }}">
  </div>

  <div class="col-md-3">
    <label class="form-label">Color</label>
    <input name="color" class="form-control" value="{{ old('color', $certificado->color) }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">Olor</label>
    <input name="olor" class="form-control" value="{{ old('olor', $certificado->olor) }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">Apariencia</label>
    <input name="apariencia" class="form-control" value="{{ old('apariencia', $certificado->apariencia) }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">Sabor</label>
    <input name="sabor" class="form-control" value="{{ old('sabor', $certificado->sabor) }}">
  </div>

  <div class="col-12 d-flex gap-2 justify-content-end">
    <button class="btn btn-primary"><i class="fa fa-save me-1"></i> Actualizar y regenerar PDF</button>
    <a href="{{ route('certificados.index') }}" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>
</div></div>
@endsection
