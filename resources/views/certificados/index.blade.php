@extends('layouts.main')
@section('title','Certificados')
@section('header','Certificados')

@push('styles')
<style>
  :root{ --verde-oscuro:#2e7d32; --verde-medio:#66bb6a; --crema:#f7f5e7; --borde:#d6d6d6; }
  .card{ border-radius:12px; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,.06); }
  .card-body{ padding:1.25rem 1.5rem; }
  .btn.btn-primary{ background:var(--verde-oscuro); border:none; font-weight:600; }
  .btn.btn-primary:hover{ background:var(--verde-medio); }

  /* Tabla con anchos fijos */
  .cert-table .table{ table-layout:fixed; margin-bottom:0; }
  .cert-table thead th{
    background:var(--crema); color:#1c3d1f; border-bottom:2px solid var(--verde-medio)!important;
    vertical-align:middle;
  }
  .cert-table th,.cert-table td{ padding:10px 12px; vertical-align:middle; }
  .text-end{ text-align:right; }

  /* Anchos por columna (~100%) */
  .cert-table col.col-fecha   { width:16%; }
  .cert-table col.col-prod    { width:28%; }
  .cert-table col.col-batch   { width:12%; }
  .cert-table col.col-cub     { width:12%; }
  .cert-table col.col-kg      { width:16%; }
  .cert-table col.col-actions { width:16%; }

  .badge-prod{
    display:inline-block; min-width:96px; text-align:center;
    background:#e9f7ec; color:#1f6f3b; border:1px solid #cdebd4;
    padding:4px 10px; border-radius:999px; font-weight:600;
  }

  /* Modal (tu CSS existente) */
  #pdfModal{ position:fixed; inset:0; background:rgba(0,0,0,.55); display:none; z-index:1050; }
  #pdfModal.show{ display:block; }
  #pdfModal .win{
    background:#fff; width:92vw; max-width:1200px; height:86vh;
    margin:7vh auto; border-radius:.5rem; display:flex; flex-direction:column; overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.25);
  }
  #pdfModal .hd, #pdfModal .ft{
    padding:.75rem 1rem; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between;
  }
  #pdfModal .ft{ border-top:1px solid #e5e7eb; border-bottom:0; gap:.5rem; justify-content:flex-end; }
  #pdfModal .title{ font-weight:600; margin:0; }
  #pdfModal .close{ background:none; border:0; font-size:1.5rem; line-height:1; cursor:pointer; }
  #pdfModal .bd{ flex:1; }
  #pdfFrame{ width:100%; height:100%; border:0; }
</style>
@endpush

@section('content')
<div class="card"><div class="card-body">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h6 m-0 text-secondary">Listado</h2>
    <a class="btn btn-primary" href="{{ route('certificados.create') }}">
      <i class="fa fa-plus me-1"></i> Nuevo
    </a>
  </div>

  {{-- Filtros --}}
  <form method="GET" action="{{ route('certificados.index') }}" class="row g-2 mb-3">

    <div class="col-md-4">
      <input name="producto" list="productos" class="form-control" placeholder="Producto exacto (sugerencias)"
             value="{{ request('producto') }}">
      @if(isset($productos) && $productos->count())
        <datalist id="productos">
          @foreach($productos as $p)
            <option value="{{ $p }}">{{ $p }}</option>
          @endforeach
        </datalist>
      @endif
    </div>

    <div class="col-md-5">
      <div class="input-group">
        <span class="input-group-text">Fecha</span>
        <input type="date" name="f_from" class="form-control" value="{{ request('f_from') }}" title="Desde">
        <input type="date" name="f_to"   class="form-control" value="{{ request('f_to') }}"   title="Hasta">
      </div>
    </div>

    <div class="col-md-3 d-flex gap-2">
      <button class="btn btn-outline-secondary w-50" title="Buscar">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
      <a class="btn btn-outline-secondary w-50" href="{{ route('certificados.index') }}" title="Limpiar">
        <i class="fa-solid fa-rotate-left"></i>
      </a>
    </div>
  </form>

  <div class="table-responsive cert-table">
    <table class="table table-sm table-striped align-middle">
      <colgroup>
        <col class="col-fecha"><col class="col-prod"><col class="col-batch">
        <col class="col-cub"><col class="col-kg"><col class="col-actions">
      </colgroup>
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Producto</th>
          <th>Batch</th>
          <th>Cubetas</th>
          <th class="text-end">Total Kg</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($certs as $c)
          <tr>
            <td>{{ $c->fecha_elaboracion->format('Y-m-d') }}</td>
            <td><span class="badge-prod">{{ $c->producto }}</span></td>
            <td>{{ $c->numero_batch }}</td>
            <td>{{ $c->cantidad_cubetas }}</td>
            <td class="text-end">{{ number_format($c->kilogramos_total,3,'.',',') }}</td>
            <td class="text-end">
              @if($c->pdf_path)
                <a class="btn btn-sm btn-outline-success"
                   href="{{ route('certificados.descargar', $c) }}"
                   title="Ver/Imprimir/Descargar PDF"
                   onclick="event.preventDefault(); previewPDF(
                     '{{ asset('storage/'.$c->pdf_path) }}',
                     'Certificado #{{ $c->id }}',
                     '{{ route('certificados.descargar', $c) }}'
                   )">
                  <i class="fa fa-download"></i>
                </a>
              @endif
              <a class="btn btn-sm btn-outline-primary" href="{{ route('certificados.edit',$c) }}" title="Editar">
                <i class="fa fa-pen"></i>
              </a>

              {{-- Eliminación con SweetAlert2 (confirmación en top-end) --}}
              <form class="d-inline js-delete"
                    method="POST"
                    action="{{ route('certificados.destroy',$c) }}"
                    data-title="¿Eliminar certificado #{{ $c->id }}?"
                    data-text="Se eliminará el certificado del <b>{{ $c->fecha_elaboracion->format('Y-m-d') }}</b>."
                    data-confirm="Aceptar"
                    data-cancel="Cancelar">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                  <i class="fa fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted py-4">Sin certificados.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $certs->withQueryString()->links() }}
  </div>
</div></div>

{{-- ===== Modal de vista previa del PDF (sin dependencia de Bootstrap) ===== --}}
<div id="pdfModal" aria-hidden="true">
  <div class="win" role="dialog" aria-modal="true" aria-labelledby="pdfModalTitle">
    <div class="hd">
      <h5 id="pdfModalTitle" class="title">Vista previa</h5>
      <button type="button" class="close" aria-label="Cerrar" onclick="closePDFModal()">&times;</button>
    </div>
    <div class="bd">
      <iframe id="pdfFrame" src="" title="Vista previa PDF"></iframe>
    </div>
    <div class="ft">
      <a id="downloadLink" href="#" class="btn btn-success" target="_blank" rel="noopener">Descargar PDF</a>
      <button type="button" class="btn btn-secondary" onclick="printPDF()">Imprimir</button>
      <button type="button" class="btn btn-outline-secondary" onclick="closePDFModal()">Cerrar</button>
    </div>
  </div>
</div>

<script>
  function previewPDF(pdfUrl, titulo, downloadUrl) {
    const overlay = document.getElementById('pdfModal');
    document.getElementById('pdfFrame').src = pdfUrl + '#view=FitH';
    document.getElementById('pdfModalTitle').textContent = titulo || 'Vista previa';
    document.getElementById('downloadLink').href = downloadUrl || pdfUrl;
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
  function closePDFModal() {
    const overlay = document.getElementById('pdfModal');
    document.getElementById('pdfFrame').src = '';
    overlay.classList.remove('show');
    document.body.style.overflow = '';
  }
  function printPDF() {
    const frame = document.getElementById('pdfFrame');
    if (frame && frame.contentWindow) {
      frame.contentWindow.focus();
      frame.contentWindow.print();
    }
  }
  document.getElementById('pdfModal').addEventListener('click', function(e){
    if (e.target.id === 'pdfModal') closePDFModal();
  });
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closePDFModal();
  });
</script>
@endsection
