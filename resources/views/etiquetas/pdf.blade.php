@php
  $pages = array_chunk($labels, 10);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
  /* Página carta con márgenes pequeños (en cm) */
  @page { size: letter; margin: 0.2cm 0.2cm; }
  *{ box-sizing: border-box; }
  body{ font-family: DejaVu Sans, Arial, sans-serif; color:#111; font-size:10.5pt; margin:0; }

  /* Grid 2 columnas (muy estable en DomPDF) */
  table.grid{ border-collapse: separate; border-spacing: 0.1cm 0.1cm; margin:0 auto; }
  td.cell{ width:10cm; height:5.10cm; padding:0; vertical-align:top; }

  /* Contenedor de una etiqueta */
  .label{
    position:relative;
    width:10cm; height:5.10cm;
    /* Subí el pie: menos reserva abajo (antes ~12mm) */
    padding:0.1cm 0.1cm 0.1cm 0.1cm;  /* top, right, bottom, left */
  }

  /* Encabezado */
  .empresa-1{ font-weight:800; font-size:12pt; line-height:1.06; }
  .empresa-2{ font-weight:800; font-size:10pt; line-height:1.06; }
  .empresa-3{ font-weight:800; letter-spacing:.22em; margin:.05cm 0 .22cm; }

  /* Fechas (compactas) */
  .row{ margin:.10cm 0; }
  .lbl{ font-weight:700; }
  .val{ margin-left:.15cm; }

  /* Producto: menos margen inferior para que el peso quede pegado */
  .producto{
    margin:.10cm 0 .02cm;
    font-weight:900; font-size:12pt; letter-spacing:.3px;
  }

  /* --------- Pie exacto: 2 filas × 2 columnas --------- */
  .footer{
    position:absolute; left:0.4cm; right:0.0cm;   /* Pie más arriba (bottom 0.2cm) */
  }
  table.bar{ width:80%; border-collapse:collapse; }
  table.bar td{ padding:0; }
  .tleft{ text-align:left; }
  .tright{ text-align:right; }
  .peso-title{ color:#6B7280; font-weight:700; font-size:10pt; }
  .cubeta-title{ color:#111; font-weight:600; font-size:10pt; }
  .peso-val{
    font-weight:900; font-size:22pt; line-height:1; letter-spacing:.3pt; white-space:nowrap;
  }
  .num{
    font-weight:900; font-size:22pt; line-height:1;
  }

  /* Logo (opcional) */
  .logo{ position:absolute; right:0.4cm; top:0.3cm; width:2.2cm; height:auto; }

  .page-break{ page-break-after: always; }
</style>
</head>
<body>

@foreach($pages as $page)
  <table class="grid" role="presentation">
    @foreach(array_chunk($page, 2) as $pair)
      <tr>
        @foreach($pair as $lb)
          <td class="cell">
            <div class="label">

              @if(!empty($logo))
                <img src="{{ $logo }}" class="logo" alt="logo">
              @endif

              <div class="empresa-1">Distribuidora de Insumos Industriales</div>
              <div class="empresa-2">&amp; Alimenticios</div>
              <div class="empresa-3">D I P I &amp; I</div>

              <div class="row"><span class="lbl">Fecha de Elaboración:</span> <span class="val">{{ $lb['fecha_elab'] }}</span></div>
              <div class="row"><span class="lbl">Fecha de Vencimiento:</span> <span class="val">{{ $lb['fecha_ven'] }}</span></div>

              <div class="producto">{{ $lb['producto'] }}</div>

              <!-- Barra inferior: títulos y valores (alineados) -->
              <div class="footer">
                <table class="bar">
                  <tr>
                    <td class="tleft"><span class="peso-title">Peso</span></td>
                    <td class="tright"><span class="cubeta-title">No. Cubeta</span></td>
                  </tr>
                  <tr>
                    <td class="tleft">
                      <span class="peso-val">{{ strtoupper(str_replace(' ', '', $lb['peso'])) }}</span>
                    </td>
                    <td class="tright"><span class="num">{{ $lb['numero'] }}</span></td>
                  </tr>
                </table>
              </div>

            </div>
          </td>
        @endforeach
        @if(count($pair) === 1)
          <td class="cell"></td>
        @endif
      </tr>
    @endforeach
  </table>

  @if(!$loop->last)
    <div class="page-break"></div>
  @endif
@endforeach

</body>
</html>
