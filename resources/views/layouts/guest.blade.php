<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name','App') }} — {{ $title ?? 'Iniciar sesión' }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    /* ===== Paleta DIPII ===== */
    :root{
      --brand-700:#76B041;   /* verde primario */
      --brand-600:#B8E986;   /* verde claro */
      --brand-400:#0F5D2E;   /* acento */
      --tint-1:#FFFDF5;      /* crema 1 */
      --tint-2:#FAFAF7;      /* crema 2 */
      --ink:#1E293B;         /* texto */
      --muted:#6B7280;
      --ring: rgba(102,187,106,.30);
      --shadow: 0 18px 50px rgba(2,6,23,.14);

      /* Tamaño del logo (ajústalo si lo quieres aún más grande) */
      --logo-size: 140px;
    }

    /* Fondo con blobs suaves */
    body{
      margin:0; color:var(--ink);
      min-height:100vh; display:grid; place-items:center;
      background:
        radial-gradient(1200px 700px at 90% -10%, var(--brand-600) 0%, transparent 55%),
        radial-gradient(1200px 700px at -20% 100%, var(--tint-1) 0%, var(--tint-2) 62%);
      font-family: system-ui, -apple-system, Inter, Roboto, "Segoe UI", Arial;
    }

    .auth-wrap{ width:100%; max-width:980px; padding:28px; }
    .auth-card{
      display:grid; grid-template-columns: 1fr 1.1fr;
      background: rgba(255,255,255,.86);
      backdrop-filter: blur(8px);
      border-radius:22px; overflow:hidden; box-shadow: var(--shadow);
      border:1px solid rgba(255,255,255,.6);
      transform: translateY(0); animation: pop .28s ease both;
    }
    @keyframes pop{ from{ transform: translateY(10px); opacity:.0 } to{ transform: translateY(0); opacity:1 } }

    /* Panel marca */
    .brand{
      position:relative; color:#fff;
      background: linear-gradient(160deg, var(--brand-700), var(--brand-600));
      padding:40px 26px; display:flex; align-items:center; justify-content:center;
    }
    .brand:after{
      content:""; position:absolute; inset:auto -40% -40% auto; width:480px; height:480px;
      background: radial-gradient(closest-side, rgba(255,255,255,.22), transparent 65%);
      transform: rotate(20deg);
    }
    .brand-inner{ position:relative; text-align:center; max-width:360px; z-index:1; }

    /* === Logo SIN fondo blanco y más grande === */
    .brand-logo{
      height: var(--logo-size);
      width: auto;
      background: transparent;   /* sin fondo */
      padding: 0;                /* sin padding */
      border-radius: 0;          /* sin bordes redondeados */
      display:inline-block;
      /* sombra suave para destacar sobre el degradé */
      filter: drop-shadow(0 10px 24px rgba(0,0,0,.18));
    }
    /* Si se usa el componente como fallback, igualar tamaño */
    .app-logo{ width: var(--logo-size); height: var(--logo-size); display:inline-block; }

    .brand-title{
      margin:16px 0 0; font-weight:800; letter-spacing:.2px;
      font-size:36px;   /* un poco más grande */
    }

    /* Panel form */
    .pane{
      padding:28px 26px 22px;
      background: #fff;
    }
    .pane h1{ margin:0 0 6px; font-size:22px; color:var(--brand-400); }

    /* Título centrado para el login */
    .title-centered{
      text-align:center; text-transform:uppercase; letter-spacing:.06em;
      font-weight:900; color:var(--brand-400); font-size:26px; margin:0 0 16px;
    }

    .alert{ padding:10px 12px; border-radius:12px; margin-bottom:12px; font-size:14px; }
    .alert-danger{ background:#fff1f2; color:#991b1b; border:1px solid #fecdd3; }
    .alert-success{ background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }

    .field{ margin-bottom:14px; }
    .label{ display:block; font-size:13px; color:var(--brand-400); font-weight:700; margin-bottom:6px; }
    .ctrl{ position:relative; }
    .ipt{
      width:100%; border:1px solid #e5e7eb; background:#fff;
      padding:12px 44px 12px 44px; border-radius:14px; font-size:15px; outline: none;
      transition: border-color .15s, box-shadow .15s;
    }
    .ipt:focus{ border-color: var(--brand-600); box-shadow:0 0 0 .28rem var(--ring); }
    .ico{
      position:absolute; left:14px; top:50%; transform:translateY(-50%);
      width:18px; height:18px; color:#9ca3af;
    }
    .toggle{
      position:absolute; right:10px; top:50%; transform:translateY(-50%);
      background:transparent; border:0; padding:6px; color:#9ca3af; cursor:pointer;
    }
    .remember{ display:flex; align-items:center; gap:8px; margin:6px 0 6px; font-size:14px; color:#374151; }

    .actions{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:4px; }
    .alink{ color:var(--brand-400); text-decoration:none; font-size:14px; }
    .alink:hover{ text-decoration:underline; }

    .btn{
      background: linear-gradient(180deg, var(--brand-400), #0b4a25);
      color:#fff; border:0; padding:12px 16px; border-radius:14px;
      font-weight:800; letter-spacing:.3px; cursor:pointer; min-width:128px;
      display:inline-flex; align-items:center; justify-content:center; gap:8px;
    }
    .btn:hover{ filter:brightness(1.06); }

    .foot{ margin-top:14px; text-align:center; font-size:12.5px; color:#6b7280; }

    /* Responsive */
    @media (max-width: 900px){
      .auth-wrap{ padding:18px; }
      .auth-card{ grid-template-columns: 1fr; }
      .brand{ display:none; }
      .pane{ padding:24px 20px; }
    }
  </style>
</head>
<body>
  <main class="auth-wrap">
    <section class="auth-card">
      <aside class="brand">
        <div class="brand-inner">
          <img class="brand-logo"
               src="{{ asset('imagen/Log.png') }}"
               onerror="this.style.display='none';document.getElementById('fallback-logo').classList.remove('hidden');"
               alt="Logo DIPII">

          <div id="fallback-logo" class="hidden">
            <x-application-logo class="app-logo" />
          </div>

          <h2 class="brand-title">Bienvenido</h2>
        </div>
      </aside>

      <div class="pane">
        {{ $slot }}
      </div>
    </section>
  </main>
</body>
</html>
