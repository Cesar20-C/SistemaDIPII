<!doctype html><html lang="es"><head><meta charset="utf-8">
<style>
  @page{ margin:15mm } body{ font-family: DejaVu Sans, sans-serif; font-size:14pt }
  .etq{ border:1px solid #000; padding:10mm; page-break-after:always }
  h1{ font-size:18pt; margin:0 0 3mm 0; text-transform:uppercase; text-align:center }
  .row{ margin:2mm 0 } .lab{ display:inline-block; width:210px; font-weight:600 } .big{ font-size:20pt; font-weight:700 }
</style></head><body>
@foreach($numeros as $n)
  <div class="etq">
    <div style="text-align:center; font-weight:700">Distribuidora de Insumos Industriales &amp; Alimenticios — D I P I &amp; I</div>
    <h1>{{ $lote->producto }}</h1>
    <div class="row"><span class="lab">Fecha de Elaboración :</span> {{ $lote->fecha_elaboracion->format('d/m/Y') }}</div>
    <div class="row"><span class="lab">Fecha de Vencimiento :</span> {{ $lote->fecha_vencimiento->format('d/m/Y') }}</div>
    <div class="row"><span class="lab">Peso</span> <span class="big">{{ number_format($lote->peso_kg,3) }} kg</span></div>
    <div class="row"><span class="lab">No. Cubeta</span> <span class="big">{{ $n }}</span></div>
  </div>
@endforeach
</body></html>
