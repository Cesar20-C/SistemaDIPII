@extends('layouts.main')
@section('title','Nuevo Ingreso')
@section('header','Nuevo Ingreso')

@push('styles')
<style>
  :root{
    --verde-oscuro:#2e7d32;
    --verde-medio:#66bb6a;
    --crema:#f7f5e7;
    --borde:#d6d6d6;
  }
  .card{ border-radius:12px; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,.06); }
  .card-body{ padding:1.25rem 1.5rem; }
  .form-label{ color:var(--verde-oscuro); font-weight:600; }
  .form-control,.form-select{
    border:1px solid var(--borde); border-radius:8px; padding:10px;
    transition:.2s ease-in-out;
  }
  .form-control:focus,.form-select:focus{
    border-color:var(--verde-medio);
    box-shadow:0 0 0 .2rem rgba(102,187,106,.25);
  }
  .btn.btn-primary{ background:var(--verde-oscuro); border:none; font-weight:600; }
  .btn.btn-primary:hover{ background:var(--verde-medio); }
  .btn-outline-secondary{ border-color:var(--verde-oscuro); color:var(--verde-oscuro); font-weight:600; }
  .btn-outline-secondary:hover{ background:var(--verde-oscuro); color:#fff; }
</style>
@endpush

@section('content')
<div class="card"><div class="card-body">
  <div class="mb-2 fw-bold text-secondary">
    <i class="fa-solid fa-box-open me-2"></i> Nuevo Ingreso
  </div>

  <form method="POST" action="{{ route('ingresos.store') }}" class="row g-3">
    @csrf

    <div class="col-md-3">
      <label class="form-label">Fecha</label>
      <input type="date" name="fecha" class="form-control"
             value="{{ old('fecha', now()->toDateString()) }}" required>
    </div>

    <div class="col-md-5">
      <label class="form-label">Producto</label>
      <select name="producto" class="form-select" required>
        <option value="" hidden>Seleccione…</option>
        <option value="Cebolla"   @selected(old('producto')=='Cebolla')>Cebolla</option>
        <option value="Jalapeño"  @selected(old('producto')=='Jalapeño')>Jalapeño</option>
        <option value="Pimiento"  @selected(old('producto')=='Pimiento')>Pimiento</option>
        <option value="Champiñón" @selected(old('producto')=='Champiñón')>Champiñón</option>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">Peso total</label>
      <input type="number" step="0.001" min="0" name="peso_total" class="form-control"
             value="{{ old('peso_total') }}" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">Proveedor</label>
      <select name="proveedor_id" class="form-select" required>
        <option value="" hidden>Seleccione…</option>
        @foreach($proveedores as $id=>$nombre)
          <option value="{{ $id }}" @selected(old('proveedor_id')==$id)>{{ $nombre }}</option>
        @endforeach
      </select>
    </div>

    <div class="col-md-6">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="1">{{ old('observaciones') }}</textarea>
    </div>

    <div class="col-12 d-flex gap-2 justify-content-end">
      <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Guardar</button>
      <a href="{{ route('ingresos.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-xmark me-1"></i> Cancelar
      </a>
    </div>
  </form>
</div></div>
@endsection
