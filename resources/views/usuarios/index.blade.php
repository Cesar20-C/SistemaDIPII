@extends('layouts.main')
@section('title','Usuarios')
@section('header','Usuarios')

@push('styles')
<style>
  :root{ --verde-oscuro:#2e7d32; --verde-medio:#66bb6a; --crema:#f7f5e7; --borde:#d6d6d6; }
  .card{ border-radius:12px; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,.06); }
  .card-body{ padding:1.25rem 1.5rem; }
  .usr-toolbar .btn-primary{ background:var(--verde-oscuro); border:none; font-weight:600; }
  .usr-toolbar .btn-primary:hover{ background:var(--verde-medio); }

  .usr-table .table{ table-layout:fixed; margin-bottom:0; }
  .usr-table thead th{ background:var(--crema); color:#1c3d1f; border-bottom:2px solid var(--verde-medio)!important; }
  .usr-table th,.usr-table td{ padding:10px 12px; vertical-align:middle; }

  /* anchos por columna */
  .usr-table col.col-name   { width:24%; }
  .usr-table col.col-phone  { width:16%; }
  .usr-table col.col-user   { width:18%; }
  .usr-table col.col-email  { width:32%; }
  .usr-table col.col-actions{ width:10%; }
  .text-end{ text-align:right; }

  @media (max-width: 768px){
    .usr-table col.col-email{ width:0%; display:none; }
    .usr-table td.td-email, .usr-table th.th-email{ display:none; }
    .usr-table col.col-name{ width:36%; }
  }
</style>
@endpush

@section('content')
<div class="card"><div class="card-body">

  <div class="d-flex justify-content-between align-items-center usr-toolbar mb-3">
    <div class="fw-bold text-secondary">Listado</div>
    <a class="btn btn-primary" href="{{ route('usuarios.create') }}">
      <i class="fa-solid fa-plus me-1"></i> Nuevo
    </a>
  </div>

  <div class="table-responsive usr-table">
    <table class="table table-striped align-middle">
      <colgroup>
        <col class="col-name"><col class="col-phone"><col class="col-user"><col class="col-email"><col class="col-actions">
      </colgroup>
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Teléfono</th>
          <th>Usuario</th>
          <th class="th-email">Email</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($usuarios as $u)
          <tr>
            <td>{{ $u->nombre }}</td>
            <td>{{ $u->telefono }}</td>
            <td>{{ $u->usuario }}</td>
            <td class="td-email">{{ $u->email }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-success me-1" href="{{ route('usuarios.edit',$u) }}" title="Editar">
                <i class="fa-solid fa-pen"></i>
              </a>

              {{-- Eliminación con SweetAlert2 (confirmación en top-end) --}}
              <form class="d-inline js-delete"
                    method="POST"
                    action="{{ route('usuarios.destroy',$u) }}"
                    data-title="¿Eliminar usuario?"
                    data-text="Se eliminará <b>{{ e($u->nombre) }}</b>."
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
    {{ $usuarios->links() }}
  </div>
</div></div>
@endsection
