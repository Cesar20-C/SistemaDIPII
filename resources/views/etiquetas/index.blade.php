@extends('layouts.main')
@section('title','Etiquetas')
@section('header','Etiquetas')

@push('styles')
<style>
  :root{ --verde-oscuro:#2e7d32; --verde-medio:#66bb6a; --crema:#f7f5e7; --borde:#d6d6d6; }

  .card{ border-radius:12px; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,.06); }
  .card-body{ padding:1.25rem 1.5rem; }

  .btn.btn-primary{ background:var(--verde-oscuro); border:none; font-weight:600; }
  .btn.btn-primary:hover{ background:var(--verde-medio); }

  /* ===== Tabla con anchos fijos ===== */
  .etq-table .table{ table-layout:fixed; margin-bottom:0; }
  .etq-table thead th{
    background: var(--crema);
    color:#1c3d1f;
    border-bottom:2px solid var(--verde-medio) !important;
    vertical-align: middle;
  }
  .etq-table th, .etq-table td{
    padding:10px 12px;
    vertical-align:middle;
  }
  /* Anchos por columna (suman ~100%) */
  .etq-table col.col-id      { width:6%;  }
  .etq-table col.col-elab    { width:12%; }
  .etq-table col.col-ven     { width:12%; }
  .etq-table col.col-prod    { width:22%; }
  .etq-table col.col-peso    { width:14%; }
  .etq-table col.col-desde   { width:12%; }
  .etq-table col.col-cant    { width:12%; }
  .etq-table col.col-actions { width:10%; }
  .text-end{ text-align:right; }

  .badge-prod{
    display:inline-block; min-width: 96px; text-align:center;
    background:#e9f7ec; color:#1f6f3b; border:1px solid #cdebd4;
    padding:4px 10px; border-radius:999px; font-weight:600;
  }

  /* ===== Modal ===== */
  #pdfModal{
    position:fixed; inset:0; background:rgba(0,0,0,.55);
    display:none; z-index:3000;
  }
  #pdfModal.show{ display:block; }

  #pdfModal .win{
    background:#fff; width:92vw; max-width:1200px; height:86vh;
    margin:7vh auto; border-radius:12px;
    display:flex; flex-direction:column; overflow:hidden;
    box-shadow:0 18px 40px rgba(0,0,0,.25);
  }
  #pdfModal .hd, #pdfModal .ft{
    padding:.75rem 1rem; border-bottom:1px solid #e5e7eb;
    display:flex; align-items:center; justify-content:space-between;
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

  {{-- Toolbar --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h6 m-0 text-secondary">Lotes de Etiquetas</h2>
    <a href="{{ route('etiquetas.create') }}" class="btn btn-primary">
      <i class="fa fa-plus me-1"></i>Nuevo
    </a>
  </div>

  {{-- Filtros --}}
  <form method="GET" action="{{ route('etiquetas.index') }}" class="row g-2 mb-3">
    <div class="col-md-3">
      <input name="producto" list="productos" class="form-control"
             placeholder="Producto exacto (sugerencias)" value="{{ request('producto') }}">
      @if(isset($productos) && $productos->count())
        <datalist id="productos">
          @foreach($productos as $p)
            <option value="{{ $p }}">{{ $p }}</option>
          @endforeach
        </datalist>
      @endif
    </div>

    <div class="col-md-6">
      <div class="input-group">
        <span class="input-group-text">Elaboración</span>
        <input type="date" name="elab_from" class="form-control" value="{{ request('elab_from') }}" title="Desde">
        <input type="date" name="elab_to"   class="form-control" value="{{ request('elab_to') }}"   title="Hasta">
      </div>
    </div>

    <div class="col-md-3 d-flex gap-2">
      <button class="btn btn-outline-secondary w-50" title="Buscar">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
      <a class="btn btn-outline-secondary w-50" href="{{ route('etiquetas.index') }}" title="Limpiar">
        <i class="fa-solid fa-rotate-left"></i>
      </a>
    </div>
  </form>

  {{-- Tabla --}}
  <div class="table-responsive etq-table">
    <table class="table table-sm table-striped align-middle">
      <colgroup>
        <col class="col-id"><col class="col-elab"><col class="col-ven"><col class="col-prod">
        <col class="col-peso"><col class="col-desde"><col class="col-cant"><col class="col-actions">
      </colgroup>
      <thead>
        <tr>
          <th>#</th>
          <th>Elaboración</th>
          <th>Vence</th>
          <th>Producto</th>
          <th class="text-end">Peso (kg)</th>
          <th>Desde</th>
          <th>Cant.</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($lotes as $l)
          <tr>
            <td>{{ $l->id }}</td>
            <td>{{ $l->fecha_elaboracion->format('Y-m-d') }}</td>
            <td>{{ $l->fecha_vencimiento->format('Y-m-d') }}</td>
            <td><span class="badge-prod">{{ $l->producto }}</span></td>
            <td class="text-end">{{ number_format($l->peso_kg,3,'.',',') }}</td>
            <td>{{ $l->numero_inicial }}</td>
            <td>{{ $l->cantidad }}</td>
            <td class="text-end">

              @if($l->pdf_path)
                <button type="button"
                        class="btn btn-sm btn-outline-success"
                        title="Ver/Imprimir/Descargar PDF"
                        data-src="{{ asset($l->pdf_path) }}"
                        data-title="Lote #{{ $l->id }}"
                        data-download="{{ asset($l->pdf_path) }}"
                        onclick="openPdf(this)">
                  <i class="fa fa-download"></i>
                </button>
              @endif

              {{-- Eliminación con confirmación --}}
              <form class="d-inline js-delete"
                    method="POST"
                    action="{{ route('etiquetas.destroy', ['lote' => $l->id]) }}"
                    data-title="¿Eliminar lote #{{ $l->id }}?"
                    data-text="Se eliminará el lote y su PDF asociado."
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
          <tr><td colspan="8" class="text-center text-muted">Sin lotes cargados…</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $lotes->withQueryString()->links() }}
  </div>
</div></div>

{{-- ===== Modal PDF ===== --}}
<div id="pdfModal" aria-hidden="true">
  <div class="win" role="dialog" aria-modal="true" aria-labelledby="pdfModalTitle">
    <div class="hd">
      <h5 id="pdfModalTitle" class="title">Vista previa</h5>
      <button type="button" class="close" aria-label="Cerrar" onclick="closePDFModal()">&times;</button>
    </div>
    <div class="bd"><iframe id="pdfFrame" src="" title="Vista previa PDF"></iframe></div>
    <div class="ft">
      <a id="downloadLink" href="#" class="btn btn-success" target="_blank" rel="noopener">Descargar PDF</a>
      <button type="button" class="btn btn-secondary" onclick="printPDF()">Imprimir</button>
      <button type="button" class="btn btn-outline-secondary" onclick="closePDFModal()">Cerrar</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function openPdf(btn){
    const pdfUrl = btn.dataset.src;
    const titulo = btn.dataset.title || 'Vista previa';
    const downloadUrl = btn.dataset.download || pdfUrl;
    previewPDF(pdfUrl, titulo, downloadUrl);
  }

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

  // Cierre al hacer click fuera del cuadro
  document.getElementById('pdfModal').addEventListener('click', e => {
    if (e.target.id === 'pdfModal') closePDFModal();
  });

  // Cierre con ESC
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closePDFModal(); });
</script>
@endpush
@endsection
