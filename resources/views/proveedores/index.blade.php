@extends('layouts.main')
@section('title','Proveedores')
@section('header','Proveedores')

@push('styles')
<style>
  :root{
    --verde-oscuro:#2e7d32;   /* primario */
    --verde-medio:#66bb6a;    /* foco/hover */
    --crema:#f7f5e7;          /* fondo suave */
    --borde:#d6d6d6;
  }
  .card{ border-radius:12px; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,.06); }
  .card-body{ padding:1.25rem 1.5rem; }
  .prov-toolbar .btn-primary{ background:var(--verde-oscuro); border:none; font-weight:600; }
  .prov-toolbar .btn-primary:hover{ background:var(--verde-medio); }
  .prov-table .table{ table-layout:fixed; margin-bottom:0; }
  .prov-table thead th{ background:var(--crema); color:#1c3d1f; border-bottom:2px solid var(--verde-medio)!important; }
  .prov-table th,.prov-table td{ padding:10px 12px; vertical-align:middle; }
  /* Anchos por columna */
  .prov-table col.col-name    { width:30%; }
  .prov-table col.col-phone   { width:16%; }
  .prov-table col.col-addr    { width:34%; }
  .prov-table col.col-status  { width:10%; }
  .prov-table col.col-actions { width:10%; }
  .prov-table .text-end{ text-align:right; }
  .td-addr{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .badge-estado{ padding:4px 10px; border-radius:999px; font-weight:600; display:inline-block; min-width:92px; text-align:center; }
  .badge-estado.activo{ background:#e9f7ec; color:#1f6f3b; border:1px solid #cdebd4; }
  .badge-estado.inactivo{ background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb; }
  @media (max-width: 768px){
    .prov-table col.col-addr{ width:0%; display:none; }
    .prov-table td.td-addr, .prov-table th.th-addr{ display:none; }
    .prov-table col.col-name{ width:44%; }
  }
</style>
@endpush

@section('content')
<div class="card">
  <div class="card-body">

    {{-- Toolbar --}}
    <div class="d-flex justify-content-between align-items-center prov-toolbar mb-3">
      <div class="fw-bold text-secondary">Listado</div>
      <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Nuevo
      </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('proveedores.index') }}" class="row g-2 mb-3">
      <div class="col-md-5">
        <input name="q" class="form-control" placeholder="Buscar por nombre, teléfono o dirección…"
               value="{{ request('q') }}">
      </div>
      <div class="col-md-3">
        <select name="estado" class="form-select">
          <option value="">Todos</option>
          <option value="activos"   {{ request('estado')==='activos'?'selected':'' }}>Activos</option>
          <option value="inactivos" {{ request('estado')==='inactivos'?'selected':'' }}>Inactivos</option>
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-outline-secondary w-50" title="Buscar"><i class="fa-solid fa-magnifying-glass"></i></button>
        <a class="btn btn-outline-secondary w-50" href="{{ route('proveedores.index') }}" title="Limpiar">
          <i class="fa-solid fa-rotate-left"></i>
        </a>
      </div>
    </form>

    {{-- Tabla --}}
    <div class="table-responsive prov-table">
      <table class="table table-striped align-middle">
        <colgroup>
          <col class="col-name"><col class="col-phone"><col class="col-addr"><col class="col-status"><col class="col-actions">
        </colgroup>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th class="th-addr">Dirección</th>
            <th>Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($proveedores as $p)
            @php $activo = (bool)$p->activo; @endphp
            <tr>
              <td>{{ $p->nombre }}</td>
              <td>{{ $p->telefono }}</td>
              <td class="td-addr" title="{{ $p->direccion }}">{{ $p->direccion }}</td>
              <td>
                <span class="badge-estado {{ $activo ? 'activo' : 'inactivo' }}">
                  {{ $activo ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-success me-1" href="{{ route('proveedores.edit',$p) }}" title="Editar">
                  <i class="fa-solid fa-pen"></i>
                </a>

                {{-- Eliminación con SweetAlert2 (sin confirm() nativo) --}}
                <form class="d-inline js-delete"
                      method="POST"
                      action="{{ route('proveedores.destroy',$p) }}"
                      data-title="¿Eliminar proveedor?"
                      data-text="Se eliminará <b>{{ e($p->nombre) }}</b>."
                      data-confirm="Aceptar"
                      data-cancel="Cancelar">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">Sin registros.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $proveedores->withQueryString()->links() }}
    </div>
  </div>
</div>
@endsection
