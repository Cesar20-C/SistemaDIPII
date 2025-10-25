@extends('layouts.main')

@section('title','Dashboard DIPII')

@section('content')
<div class="container-fluid py-3">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Dashboard DIPII</h3>
    <span class="text-muted" id="periodoLabel">Período: {{ $filters['label'] ?? '' }}</span>
  </div>

  {{-- Filtros --}}
  <form id="dashboardForm"
        class="card card-body mb-3"
        method="GET"
        action="{{ route('dashboard') }}"
        data-endpoint="{{ route('dashboard.data') }}">
    <div class="row g-2 align-items-end">
      <div class="col-12 col-md-2">
        <label class="form-label">Modo</label>
        <select name="modo" class="form-select">
          <option value="anio"  {{ request('modo','anio')=='anio' ? 'selected':'' }}>Año</option>
          <option value="mes"   {{ request('modo')=='mes'  ? 'selected':'' }}>Mes</option>
          <option value="rango" {{ request('modo')=='rango'? 'selected':'' }}>Rango</option>
        </select>
      </div>

      <div class="col-6 col-md-2">
        <label class="form-label">Año</label>
        @php $yy = now()->year; @endphp
        <select name="anio" class="form-select">
          @for($y=$yy; $y>=$yy-5; $y--)
            <option value="{{ $y }}" {{ (int)request('anio', $yy)===$y ? 'selected':'' }}>{{ $y }}</option>
          @endfor
        </select>
      </div>

      <div class="col-6 col-md-2">
        <label class="form-label">Mes</label>
        <select name="mes" class="form-select">
          @for($m=1;$m<=12;$m++)
            <option value="{{ $m }}" {{ (int)request('mes', now()->month)===$m ? 'selected':'' }}>
              {{ str_pad($m,2,'0',STR_PAD_LEFT) }}
            </option>
          @endfor
        </select>
      </div>

      <div class="col-6 col-md-3">
        <label class="form-label">Desde</label>
        <input type="date" name="desde" value="{{ request('desde') }}" class="form-control" placeholder="aaaa-mm-dd">
      </div>

      <div class="col-6 col-md-3">
        <label class="form-label">Hasta</label>
        <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control" placeholder="aaaa-mm-dd">
      </div>

      <div class="col-12 col-md-2">
        <button type="submit" class="btn btn-success w-100">Aplicar</button>
      </div>
    </div>
  </form>

  {{-- KPIs --}}
  <div class="row g-3 mb-3">
    <div class="col-6 col-lg-2">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Total comprado</div>
          <div class="fs-5 fw-bold" id="kpiTotalKg">{{ number_format($kpis['total_kg'] ?? 0, 3) }} kg</div>
        </div>
      </div>
    </div>

    <div class="col-6 col-lg-2">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Proveedores activos</div>
          <div class="fs-5 fw-bold" id="kpiProvActivos">{{ $kpis['proveedores_activos'] ?? 0 }}</div>
        </div>
      </div>
    </div>

    <div class="col-6 col-lg-2">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Productos distintos</div>
          <div class="fs-5 fw-bold" id="kpiProdDistintos">{{ $kpis['productos_distintos'] ?? 0 }}</div>
        </div>
      </div>
    </div>

    <div class="col-6 col-lg-2">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Ingresos registrados</div>
          <div class="fs-5 fw-bold" id="kpiIngresosReg">{{ $kpis['compras_registradas'] ?? 0 }}</div>
        </div>
      </div>
    </div>

    <div class="col-6 col-lg-2">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Documentos generados</div>
          <div class="fs-5 fw-bold" id="kpiDocs">{{ $kpis['documentos_generados'] ?? 0 }}</div>
        </div>
      </div>
    </div>

    <div class="col-6 col-lg-2">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Top proveedor (kg)</div>
          @if(($kpis['proveedor_top'] ?? null))
            <div class="fw-semibold" id="kpiTopProvName">{{ $kpis['proveedor_top']['proveedor'] }}</div>
            <div class="small text-muted" id="kpiTopProvKg">{{ number_format($kpis['proveedor_top']['kg'], 3) }} kg</div>
          @else
            <div class="text-muted" id="kpiTopProvName">—</div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <style>
    #chartProductos, #chartProveedores, #chartProdFrecuencia, #chartDocs { min-height: 340px; }
  </style>

  {{-- Gráficas fila 1 --}}
  <div class="row g-3">
    <div class="col-12 col-xl-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white fw-semibold">Ingresos por producto (Top 10, kg)</div>
        <div class="card-body"><div id="chartProductos"></div></div>
      </div>
    </div>
    <div class="col-12 col-xl-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white fw-semibold">Top proveedores por kg</div>
        <div class="card-body"><div id="chartProveedores"></div></div>
      </div>
    </div>
  </div>

  {{-- Gráficas fila 2 --}}
  <div class="row g-3 mt-1">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">Productos que más ingresan (veces)</div>
        <div class="card-body"><div id="chartProdFrecuencia"></div></div>
      </div>
    </div>
  </div>

  {{-- Gráficas fila 3 --}}
  <div class="row g-3 mt-1">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">Documentos generados por mes</div>
        <div class="card-body"><div id="chartDocs"></div></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const $ = sel => document.querySelector(sel);
    const nf = new Intl.NumberFormat('es-GT', { minimumFractionDigits: 0, maximumFractionDigits: 3 });

    // Datos iniciales renderizados por PHP
    const init = {
      comprasPorProducto:  @json($comprasPorProducto ?? []),
      topProveedores:      @json($topProveedores ?? []),
      documentosPorMes:    @json($documentosPorMes ?? []),
      productosFrecuentes: @json($productosFrecuentes ?? []),
      kpis:                @json($kpis ?? []),
      filters:             @json($filters ?? []),
    };

    // Crear charts una sola vez
    const chartProductos = new ApexCharts($('#chartProductos'), {
      chart: { type: 'bar', height: 360 },
      series: [{ name: 'kg', data: init.comprasPorProducto.map(r => Number(r.total_kg||0)) }],
      xaxis: { categories: init.comprasPorProducto.map(r => r.producto) },
      dataLabels: { enabled: false },
      tooltip: { y: { formatter: v => nf.format(v||0) + ' kg' } }
    });

    const chartProveedores = new ApexCharts($('#chartProveedores'), {
      chart: { type: 'donut', height: 360 },
      series: init.topProveedores.map(r => Number(r.total_kg||0)),
      labels: init.topProveedores.map(r => r.proveedor),
      legend: { position: 'bottom' },
      tooltip: { y: { formatter: v => nf.format(v||0) + ' kg' } }
    });

    const chartProdFrecuencia = new ApexCharts($('#chartProdFrecuencia'), {
      chart: { type: 'bar', height: 360 },
      series: [{ name: 'veces', data: init.productosFrecuentes.map(r => Number(r.veces||0)) }],
      xaxis: { categories: init.productosFrecuentes.map(r => r.producto) },
      dataLabels: { enabled: false },
      tooltip: { y: { formatter: v => nf.format(v||0) + ' veces' } }
    });

    const chartDocs = new ApexCharts($('#chartDocs'), {
      chart: { type: 'line', height: 360 },
      series: [{ name: 'Documentos', data: init.documentosPorMes.map(r => r.cnt) }],
      xaxis: { categories: init.documentosPorMes.map(r => r.ym) },
      dataLabels: { enabled: false }
    });

    Promise.all([
      chartProductos.render(),
      chartProveedores.render(),
      chartProdFrecuencia.render(),
      chartDocs.render()
    ]);

    // Actualiza KPIs
    function updateKpis(k) {
      const top = k.proveedor_top || null;

      $('#kpiTotalKg').textContent     = `${nf.format(k.total_kg || 0)} kg`;
      $('#kpiProvActivos').textContent = nf.format(k.proveedores_activos || 0);
      $('#kpiProdDistintos').textContent = nf.format(k.productos_distintos || 0);
      $('#kpiIngresosReg').textContent = nf.format(k.compras_registradas || 0);
      $('#kpiDocs').textContent       = nf.format(k.documentos_generados || 0);

      if (top) {
        $('#kpiTopProvName').textContent = top.proveedor || '—';
        if ($('#kpiTopProvKg')) $('#kpiTopProvKg').textContent = `${nf.format(top.kg || 0)} kg`;
      } else {
        $('#kpiTopProvName').textContent = '—';
        if ($('#kpiTopProvKg')) $('#kpiTopProvKg').textContent = '';
      }
    }

    // Actualiza charts
    function updateCharts(d) {
      chartProductos.updateSeries([{ name: 'kg', data: d.comprasPorProducto.map(r => Number(r.total_kg||0)) }]);
      chartProductos.updateOptions({ xaxis: { categories: d.comprasPorProducto.map(r => r.producto) } });

      chartProveedores.updateSeries(d.topProveedores.map(r => Number(r.total_kg||0)));
      chartProveedores.updateOptions({ labels: d.topProveedores.map(r => r.proveedor) });

      chartProdFrecuencia.updateSeries([{ name: 'veces', data: d.productosFrecuentes.map(r => Number(r.veces||0)) }]);
      chartProdFrecuencia.updateOptions({ xaxis: { categories: d.productosFrecuentes.map(r => r.producto) } });

      chartDocs.updateSeries([{ name: 'Documentos', data: d.documentosPorMes.map(r => r.cnt) }]);
      chartDocs.updateOptions({ xaxis: { categories: d.documentosPorMes.map(r => r.ym) } });
    }

    function updatePeriodo(label) {
      const el = document.getElementById('periodoLabel');
      if (el) el.textContent = 'Período: ' + label;
    }

    // Interceptar submit y pedir JSON
    const form = document.getElementById('dashboardForm');
    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      const endpoint = form.dataset.endpoint; // /dashboard/data
      const params   = new URLSearchParams(new FormData(form));
      const url      = endpoint + '?' + params.toString();

      try {
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const json = await res.json();

        updateCharts(json);
        updateKpis(json.kpis);
        updatePeriodo(json.filters.label);
      } catch (err) {
        console.error('Dashboard fetch error:', err);
        alert('No se pudieron actualizar las gráficas.');
      }
    });
  });
  </script>
@endpush
