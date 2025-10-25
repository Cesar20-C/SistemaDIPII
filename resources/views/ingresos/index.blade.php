@extends('layouts.main')
@section('title','Ingresos')
@section('header','Ingresos')

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

  .ing-toolbar .btn-primary{ background:var(--verde-oscuro); border:none; font-weight:600; }
  .ing-toolbar .btn-primary:hover{ background:var(--verde-medio); }

  .ing-table thead th{
    background: var(--crema);
    color:#1c3d1f;
    border-bottom:2px solid var(--verde-medio)!important;
  }
  .ing-table tbody tr:hover{ background:#fafafa; }
  .ing-table .col-actions{ width:120px; text-align:right; }
  .badge-producto{
    background:#e9f7ec;
    color:#1f6f3b;
    border:1px solid #cdebd4;
    font-weight:600;
  }
</style>
@endpush

@section('content')
@php use Illuminate\Support\Str; @endphp

<div class="card">
  <div class="card-body">

    {{-- Encabezado + nuevo --}}
    <div class="d-flex justify-content-between align-items-center ing-toolbar mb-3">
      <div class="fw-bold text-secondary">Listado</div>
      <a href="{{ route('ingresos.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Nuevo
      </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('ingresos.index') }}" class="row g-2 mb-3">
      <div class="col-md-3">
        <input name="q" class="form-control" placeholder="Buscar por producto"
               value="{{ request('q') }}">
      </div>
      <div class="col-md-3">
        <select name="proveedor_id" class="form-select">
          <option value="">Todos los proveedores</option>
          @foreach($proveedores as $id=>$nombre)
            <option value="{{ $id }}" {{ (string)request('proveedor_id')===(string)$id ? 'selected':'' }}>
              {{ $nombre }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="Desde">
      </div>
      <div class="col-md-2">
        <input type="date" name="to" class="form-control" value="{{ request('to') }}" placeholder="Hasta">
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-outline-secondary w-50"><i class="fa-solid fa-magnifying-glass"></i></button>
        <a class="btn btn-outline-secondary w-50" href="{{ route('ingresos.index') }}">
          <i class="fa-solid fa-rotate-left"></i>
        </a>
      </div>
    </form>

    {{-- Tabla --}}
    <div class="table-responsive ing-table">
      <table class="table table-striped align-middle mb-0">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Producto</th>
            <th class="text-end">Peso total</th>
            <th>Proveedor</th>
            <th>Observaciones</th>
            <th class="col-actions">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($ingresos as $i)
            <tr>
              <td>{{ \Illuminate\Support\Carbon::parse($i->fecha)->format('Y-m-d') }}</td>
              <td>
                <span class="badge rounded-pill badge-producto">{{ $i->producto }}</span>
              </td>
              <td class="text-end">{{ number_format($i->peso_total, 3, '.', ',') }}</td>
              <td>{{ $i->proveedor?->nombre }}</td>
              <td title="{{ $i->observaciones }}">{{ Str::limit($i->observaciones, 40) }}</td>
              <td class="text-end">
                <a href="{{ route('ingresos.edit', $i) }}" class="btn btn-sm btn-outline-success me-1"
                   title="Editar"><i class="fa-solid fa-pen"></i></a>

                {{-- Eliminación con SweetAlert2 (sin confirm() nativo) --}}
                <form action="{{ route('ingresos.destroy', $i) }}"
                      method="POST"
                      class="d-inline js-delete"
                      data-title="¿Eliminar ingreso?"
                      data-text="Eliminar ingreso del {{ \Illuminate\Support\Carbon::parse($i->fecha)->format('Y-m-d') }}."
                      data-confirm="Aceptar"
                      data-cancel="Cancelar">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                No hay ingresos que coincidan con el filtro.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-3">
      {{ $ingresos->withQueryString()->links() }}
    </div>
  </div>
</div>
@endsection
