@extends('layouts.main')
@section('title','Nuevo Certificado')
@section('header','Nuevo Certificado')

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

<form method="POST" action="{{ route('certificados.store') }}" class="row g-3" novalidate>
  @csrf

  {{-- Fecha elaboración --}}
  <div class="col-md-4">
    <label class="form-label">Fecha elaboración</label>
    <input type="date" name="fecha_elaboracion" class="form-control" required
           value="{{ old('fecha_elaboracion', now()->format('Y-m-d')) }}">
  </div>

  {{-- Producto (vacío) --}}
  <div class="col-md-8">
    <label class="form-label">Producto</label>
    <input name="producto" class="form-control" required
           value="{{ old('producto') }}">
  </div>

  {{-- Origen del cultivo (vacío) --}}
  <div class="col-md-6">
    <label class="form-label">Origen del cultivo</label>
    <input name="origen_cultivo" class="form-control" required
           value="{{ old('origen_cultivo') }}">
  </div>

  {{-- Nº batch (1 por defecto, editable) --}}
  <div class="col-md-2">
    <label class="form-label">N° batch</label>
    <input type="number" name="numero_batch" class="form-control" min="1" required
           value="{{ old('numero_batch', 1) }}">
  </div>

  {{-- Cubetas (vacío) --}}
  <div class="col-md-2">
    <label class="form-label">Cubetas</label>
    <input type="number" name="cantidad_cubetas" class="form-control" min="1" required
           value="{{ old('cantidad_cubetas') }}">
  </div>

  {{-- Kg por cubeta (vacío, con máscara 3 enteros + 2 decimales) --}}
  <div class="col-md-2">
    <label class="form-label">Kg por cubeta</label>
    <input type="text" id="peso_por_cubeta" name="peso_por_cubeta" class="form-control"
           inputmode="decimal" pattern="^\d{1,3}(\.\d{2})$"
           value="{{ old('peso_por_cubeta') }}" required>
  </div>

  {{-- Campos ocultos con “Característico(a).” --}}
  <input type="hidden" name="color"      value="Característico.">
  <input type="hidden" name="olor"       value="Característico.">
  <input type="hidden" name="apariencia" value="Característica.">
  <input type="hidden" name="sabor"      value="Característico.">

  <div class="col-12 d-flex gap-2 justify-content-end">
    <button class="btn btn-primary"><i class="fa fa-save me-1"></i> Guardar y generar PDF</button>
    <a href="{{ route('certificados.index') }}" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>
</div></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const kg = document.getElementById('peso_por_cubeta');

  // Máscara: máximo 3 enteros y 2 decimales
  kg.addEventListener('input', () => {
    let v = kg.value.replace(/[^0-9.]/g, '');
    const parts = v.split('.');
    const enteros = (parts[0] || '').slice(0, 3);
    const dec     = (parts[1] || '').slice(0, 2);
    kg.value = enteros + (v.includes('.') ? '.' : '') + dec;
  });

  // Normaliza a 2 decimales al salir
  kg.addEventListener('blur', () => {
    if (!kg.value) return;
    const m = kg.value.match(/^(\d{1,3})(?:\.(\d{1,2}))?$/);
    if (!m) { kg.value = ''; return; }
    const e = m[1];
    const d = (m[2] ?? '').padEnd(2, '0').slice(0, 2);
    kg.value = `${e}.${d}`;
  });
});
</script>
@endpush
@endsection
