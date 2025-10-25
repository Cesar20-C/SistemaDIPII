@extends('layouts.main')
@section('title','Nuevo lote de etiquetas')
@section('header','Nuevo lote de etiquetas')

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
<div class="card shadow-sm"><div class="card-body">
  @if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <form method="POST" action="{{ route('etiquetas.store') }}" class="row g-3" novalidate>
    @csrf

    {{-- ===== Fecha elaboración (visible) ===== --}}
    <div class="col-md-3">
      <label class="form-label">Fecha elaboración</label>
      <input
        type="date"
        id="fecha_elaboracion"
        name="fecha_elaboracion"
        value="{{ old('fecha_elaboracion',$hoy) }}"
        class="form-control"
        required>
    </div>

    {{-- ===== Fecha vencimiento (oculta, +2 días) ===== --}}
    <input
      type="hidden"
      id="fecha_vencimiento"
      name="fecha_vencimiento"
      value="{{ old('fecha_vencimiento', \Carbon\Carbon::parse($hoy)->addDays(2)->format('Y-m-d')) }}">

    {{-- ===== Producto (lista) ===== --}}
    <div class="col-md-6">
      <label class="form-label">Producto</label>
      @php $productos = ['CEBOLLA EN CUBOS','JALAPEÑO EN CUBOS','PIMIENTO EN CUBOS']; @endphp
      <select id="producto" name="producto" class="form-select" required>
        <option value="" disabled {{ old('producto') ? '' : 'selected' }}>Seleccione…</option>
        @foreach($productos as $p)
          <option value="{{ $p }}" {{ old('producto')===$p ? 'selected' : '' }}>{{ $p }}</option>
        @endforeach
      </select>
    </div>

    {{-- ===== Peso por cubeta (3 enteros + 2 decimales) ===== --}}
    <div class="col-md-3">
      <label class="form-label">Peso por cubeta (kg)</label>
      <input
        type="text"
        id="peso_kg"
        name="peso_kg"
        class="form-control"
        inputmode="decimal"
        placeholder="000.00"
        pattern="^\d{1,3}(\.\d{2})$"
        value="{{ old('peso_kg') }}"
        required>
    </div>

    {{-- ===== Número inicial (1 por defecto) ===== --}}
    <div class="col-md-3">
      <label class="form-label">Número inicial</label>
      <input type="number" name="numero_inicial" value="{{ old('numero_inicial',1) }}" min="1" class="form-control" required>
    </div>

    {{-- ===== Cantidad de etiquetas (vacío) ===== --}}
    <div class="col-md-3">
      <label class="form-label">Cantidad de etiquetas</label>
      <input type="number" id="cantidad" name="cantidad" value="{{ old('cantidad') }}" min="1" class="form-control" placeholder="Ingrese la cantidad" required>
    </div>

    <div class="col-12 d-flex gap-2 mt-2 justify-content-end">
      <button class="btn btn-primary"><i class="fa fa-save me-1"></i> Guardar y generar PDF</button>
      <a href="{{ route('etiquetas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const fe = document.getElementById('fecha_elaboracion'); // visible
  const fv = document.getElementById('fecha_vencimiento'); // hidden
  const peso = document.getElementById('peso_kg');

  // ===== fecha_vencimiento = fecha_elaboracion + 2 días
  function toISO(date){
    const y = date.getFullYear();
    const m = String(date.getMonth()+1).padStart(2,'0');
    const d = String(date.getDate()).padStart(2,'0');
    return `${y}-${m}-${d}`;
  }
  function syncVencimiento(){
    const d = new Date(fe.value);
    if (isNaN(d)) return;
    d.setDate(d.getDate() + 2);
    fv.value = toISO(d);
  }
  syncVencimiento();
  fe.addEventListener('change', syncVencimiento);
  fe.addEventListener('input', syncVencimiento);

  // ===== Máscara/normalización para peso (###.##)
  peso.addEventListener('input', () => {
    let v = peso.value.replace(/[^0-9.]/g,'');
    const parts = v.split('.');
    const enteros = (parts[0] || '').slice(0,3);          // máx 3 enteros
    const dec = (parts[1] || '').slice(0,2);              // máx 2 decimales
    peso.value = enteros + (v.includes('.') ? '.' : '') + dec;
  });
  peso.addEventListener('blur', () => {
    if (!peso.value) return;
    const m = peso.value.match(/^(\d{1,3})(?:\.(\d{1,2}))?$/);
    if (!m) { peso.value = ''; return; }
    const e = m[1];
    const d = (m[2] ?? '').padEnd(2,'0').slice(0,2);
    peso.value = `${e}.${d}`;
  });
});
</script>
@endpush
@endsection
