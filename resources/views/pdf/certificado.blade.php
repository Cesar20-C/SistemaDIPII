<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Certificado #{{ $c->id }}</title>
  <style>
    @page { margin: 22mm 18mm 20mm 18mm; }
    body{ font-family: DejaVu Sans, sans-serif; font-size:12px; color:#111; }
    .wrapper{ border:2px solid #333; padding:14mm 12mm; }
    .header{ text-align:center; margin-bottom:10mm; position:relative; }
    .title-bar{
      display:inline-block; padding:6px 14px; border:1px solid #bbb; border-radius:3px;
      background:#f0f2f5; font-weight:700; text-transform:uppercase; letter-spacing:.3px;
    }
    .logo{ position:absolute; right:0; top:-8mm; width:90px; }
    .table{ width:100%; border-collapse:collapse; }
    .table td{ padding:4px 6px; vertical-align:top; }
    .label{ width:42%; font-weight:700; color:#333; }
    .sep{ width:3%; }
    .small{ font-size:11px; color:#555; }
    .sign{ margin-top:14mm; text-align:center; }
    .sign .line{ border-top:1px solid #666; width:240px; margin:0 auto 2mm; }
    .footer{ margin-top:8mm; text-align:center; font-size:10px; color:#555; line-height:1.4; }
    .watermark{
      position:fixed; top:35%; left:50%; transform:translate(-50%,-50%);
      opacity:.05; font-size:120px; font-weight:700; letter-spacing:3px;
    }
  </style>
</head>
<body>
@php
  \Carbon\Carbon::setLocale('es');
  $fecha   = \Carbon\Carbon::parse($c->fecha_elaboracion);
  $mes     = mb_strtoupper($fecha->translatedFormat('F'), 'UTF-8');
  $fechaLarga = "Guatemala, ".$fecha->format('d')." de {$mes} del ".$fecha->format('Y');

  $cubetas = number_format((int)$c->cantidad_cubetas, 0, '.', ',');
  $peso    = number_format((float)$c->peso_por_cubeta, 3, '.', ',');
  $total   = number_format((float)$c->kilogramos_total, 3, '.', ',');

  $logoPath = public_path('imagen/Logo.png');
  $logoB64  = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
@endphp

<div class="watermark">DIPII</div>

<div class="wrapper">
  <div class="header">
    @if($logoB64)
      <img class="logo" src="data:image/png;base64,{{ $logoB64 }}" alt="Logo">
    @endif
    <div class="title-bar">Certificado de Calidad</div>
  </div>

  <table class="table">
    <tr>
      <td class="label">Fecha de Elaboración de producto:</td>
      <td class="sep"></td>
      <td>{{ $fechaLarga }}</td>
    </tr>
    <tr>
      <td class="label">Producto:</td><td class="sep"></td>
      <td>{{ \Illuminate\Support\Str::upper($c->producto) }}</td>
    </tr>
    <tr>
      <td class="label">Origen del cultivo:</td><td class="sep"></td>
      <td>{{ $c->origen_cultivo }}</td>
    </tr>
    <tr><td class="label">Color:</td><td class="sep"></td><td>{{ $c->color }}</td></tr>
    <tr><td class="label">Olor:</td><td class="sep"></td><td>{{ $c->olor }}</td></tr>
    <tr><td class="label">Apariencia:</td><td class="sep"></td><td>{{ $c->apariencia }}</td></tr>
    <tr><td class="label">Sabor:</td><td class="sep"></td><td>{{ $c->sabor }}</td></tr>
    <tr>
      <td class="label">Número de Batch:</td><td class="sep"></td>
      <td>{{ $c->numero_batch }}</td>
    </tr>
    <tr>
      <td class="label">Cantidad de Kilogramos:</td><td class="sep"></td>
      <td>
        {{ $cubetas }} Cubetas de {{ $peso }} kilogramos
        <div class="small">{{ $total }} total</div>
      </td>
    </tr>
  </table>

  <div class="sign">
    <div class="line"></div>
    <div>Ing. Carlos Molina<br><span class="small">Control de Calidad</span></div>
  </div>

  <div class="footer">
    Distribuidora de Insumos Industriales &amp; Alimenticios S.A.<br>
    2da Calle 5-09 Zona 1, El Tejar, Chimaltenango<br>
    Tels. 53360002 / 55239500 &nbsp;&nbsp; Fax. 24725037
  </div>
</div>
</body>
</html>
