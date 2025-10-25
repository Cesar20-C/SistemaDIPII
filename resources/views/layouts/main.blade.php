<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Sistema')</title>

  {{-- Carga SweetAlert2 una sola vez (los parciales lo usan) --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
  :root{
    /* Paleta DIPII */
    --brand-700:#76B041;
    --brand-600:#B8E986;
    --brand-400:#0F5D2E;
    --tint-1:#FFFDF5;
    --tint-2:#FAFAF7;

    --sb-open: 280px;
    --sb-closed: 84px;

    --shadow-lg: 0 14px 40px rgba(2,6,23,.16);
    --shadow-sm: 0 8px 18px rgba(2,6,23,.12);

    /* Tamaño del logo */
    --logo-size: 40px;
  }

  /* ===== Sidebar ===== */
  .sidebar{
    position:fixed; inset:0 auto 0 0; width:var(--sb-open);
    background: linear-gradient(180deg,var(--tint-1),var(--tint-2));
    border-right:0; padding:20px 12px; transition:width .22s ease;
    z-index: 20;
  }
  .sidebar.collapsed{ width:var(--sb-closed); }

  .sb-card{
    background: linear-gradient(180deg,var(--brand-700),var(--brand-600));
    color:#fff;
    border-radius:20px; box-shadow:var(--shadow-lg);
    overflow:hidden; height:calc(100vh - 40px);
    display:flex; flex-direction:column;
  }

  /* Head */
  .sb-head{
    position:relative;
    display:flex; align-items:center; justify-content:center;
    padding:16px 12px 14px;
    border-bottom:1px solid rgba(255,255,255,.12);
    background:linear-gradient(180deg,rgba(255,255,255,.08),rgba(255,255,255,.03));
  }

  /* Logo + título */
  .sb-brand{ display:flex; flex-direction:column; align-items:center; gap:8px; }
  .sb-logo{ display:flex; align-items:center; justify-content:center; }
  .sb-logo img{
    height:var(--logo-size); width:auto;
    background:transparent;
    filter: drop-shadow(0 6px 14px rgba(0,0,0,.18));
  }
  .sb-title{
    font-weight:900; letter-spacing:.3px; white-space:nowrap; color:#fff;
    font-size:1.1rem;
  }

  /* Toggle */
  .sb-toggle{
    position:absolute; right:12px; top:12px;
    background:#fff; border:0; width:36px; height:36px; border-radius:11px;
    display:grid; place-items:center; color:#0F5D2E;
    box-shadow:var(--shadow-sm); transition:transform .12s ease, filter .12s ease;
  }
  .sb-toggle:hover{ filter:brightness(1.03); }
  .sb-toggle:active{ transform: scale(.98); }

  /* Nav */
  .sb-menu{ list-style:none; margin:10px 0; padding:10px 10px 14px; flex:1; overflow:auto; }
  .sb-menu li{ margin-bottom:10px; }

  .sb-item{
    --pad: 12px;
    position:relative;
    display:flex; align-items:center; gap:12px;
    color:#eaf6ea; text-decoration:none;
    padding: 12px var(--pad);
    border-radius:16px;
    background: rgba(255,255,255,.12);
    backdrop-filter: blur(3px);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.16);
    transition: transform .12s ease, box-shadow .15s ease, background .15s ease, color .15s ease;
  }
  .sb-item .icon{ width:24px; text-align:center; opacity:.98; }
  .sb-item .label{ white-space:nowrap; font-weight:600; letter-spacing:.1px; }

  .sb-item:hover{
    background:#fff;
    color:#0F5D2E;
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
  }

  .sb-item.is-active{
    background:#fff;
    color:#0F5D2E;
    box-shadow: var(--shadow-lg);
  }
  .sb-item.is-active .icon{ color:#0F5D2E; }
  .sb-item.is-active::before{
    content:""; position:absolute; left:6px; top:50%; transform:translateY(-50%);
    width:4px; height:60%; border-radius:6px; background:#0F5D2E;
  }

  /* Footer */
  .sb-footer{ padding:12px 10px 14px; border-top:1px solid rgba(255,255,255,.10); }
  .as-link{ background:transparent; border:0; color:inherit; padding:0; cursor:pointer; width:100%; text-align:left; }
  .sb-logout.sb-item{ background:rgba(0,0,0,.12); padding: 16px 14px; }
  .sb-logout.sb-item:hover{ background:#fff; color:#0F5D2E; }

  /* Tooltips (colapsado) */
  .sb-tooltip{
    position:fixed; pointer-events:none; transform:translateY(-50%);
    background:#fff; color:#111827; padding:8px 10px; border-radius:10px; font-size:12.5px;
    box-shadow:0 10px 24px rgba(2,6,23,.20); opacity:0; transition:opacity .12s; z-index:50;
  }

  /* Contenido empujado */
  main{ margin-left:var(--sb-open); transition:margin-left .22s ease; }
  main.collapsed{ margin-left:var(--sb-closed); }

  /* Estados colapsado */
  .sidebar.collapsed .sb-title, .sidebar.collapsed .label{ display:none; }
  .sidebar.collapsed .sb-head{ justify-content:center; padding-top:14px; }
  .sidebar.collapsed .sb-item{ justify-content:center; padding-left:0; padding-right:0; }
  .sidebar.collapsed .sb-item.is-active::before{ left:50%; transform:translate(-50%,-50%); height:6px; width:40%; border-radius:6px; }

  /* Scroll fino */
  .sb-menu{ scrollbar-width:thin; scrollbar-color: rgba(255,255,255,.35) transparent; }
  .sb-menu::-webkit-scrollbar{ width:8px; }
  .sb-menu::-webkit-scrollbar-thumb{ background:rgba(255,255,255,.35); border-radius:8px; }
  </style>

  @stack('styles')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

  <aside id="sidebar" class="sidebar">
    <div class="sb-card">

      <div class="sb-head">
        <div class="sb-brand">
          <div class="sb-logo">
            {{-- Logo con fondo transparente --}}
            <img src="{{ asset('imagen/Logo.png') }}" alt="Logo">
          </div>
          <span class="sb-title">Menú</span>
        </div>

        <button id="sbToggle" class="sb-toggle" aria-label="Contraer/Expandir" aria-expanded="true">
          <i class="fa-solid fa-chevron-left chev"></i>
        </button>
      </div>

      <ul class="sb-menu">
        <li>
          <a class="sb-item {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
             href="{{ route('dashboard') }}" data-title="Dashboard">
            <i class="fa-solid fa-gauge-high icon"></i>
            <span class="label">Dashboard</span>
          </a>
        </li>

        <li>
          <a class="sb-item {{ request()->routeIs('proveedores.*') ? 'is-active' : '' }}"
             href="{{ route('proveedores.index') }}" data-title="Proveedores">
            <i class="fa-solid fa-truck icon"></i>
            <span class="label">Proveedores</span>
          </a>
        </li>

        <li>
          <a class="sb-item {{ request()->routeIs('certificados.*') ? 'is-active' : '' }}"
             href="{{ route('certificados.index') }}" data-title="Certificados">
            <i class="fa-solid fa-file-lines icon"></i>
            <span class="label">Certificados</span>
          </a>
        </li>

        <li>
          <a class="sb-item {{ request()->routeIs('etiquetas.*') ? 'is-active' : '' }}"
             href="{{ route('etiquetas.index') }}" data-title="Etiquetas">
            <i class="fa-solid fa-tags icon"></i>
            <span class="label">Etiquetas</span>
          </a>
        </li>

        <li>
          <a class="sb-item {{ request()->routeIs('usuarios.*') ? 'is-active' : '' }}"
             href="{{ route('usuarios.index') }}" data-title="Usuarios">
            <i class="fa-solid fa-user icon"></i>
            <span class="label">Usuarios</span>
          </a>
        </li>

        <li>
          <a class="sb-item {{ request()->routeIs('ingresos.*') ? 'is-active' : '' }}"
             href="{{ route('ingresos.index') }}" data-title="Ingresos">
            <i class="fa-solid fa-box icon"></i>
            <span class="label">Ingresos</span>
          </a>
        </li>
      </ul>

      <div class="sb-footer">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="sb-item as-link sb-logout" type="submit" data-title="Cerrar sesión">
            <i class="fa-solid fa-right-from-bracket icon"></i>
            <span class="label">Cerrar sesión</span>
          </button>
        </form>
      </div>

    </div>
  </aside>

  <main id="ct">
    <header class="bg-white border-bottom">
      <div class="container-fluid py-2">
        <h1 class="h5 m-0">@yield('header','Dashboard')</h1>
      </div>
    </header>

    <section class="container-fluid py-3">
      @yield('content')
    </section>
  </main>

  <script>
  (function(){
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('ct');
    const btn = document.getElementById('sbToggle');
    const KEY = 'sb-collapsed';

    const collapsed = localStorage.getItem(KEY) === '1';
    sidebar.classList.toggle('collapsed', collapsed);
    if(content) content.classList.toggle('collapsed', collapsed);
    btn.setAttribute('aria-expanded', (!collapsed).toString());
    btn.innerHTML = collapsed ? '<i class="fa-solid fa-angle-right"></i>' : '<i class="fa-solid fa-angle-left"></i>';

    btn.addEventListener('click', () => {
      const isCollapsed = sidebar.classList.toggle('collapsed');
      if(content) content.classList.toggle('collapsed', isCollapsed);
      localStorage.setItem(KEY, isCollapsed ? '1' : '0');
      btn.setAttribute('aria-expanded', (!isCollapsed).toString());
      btn.innerHTML = isCollapsed ? '<i class="fa-solid fa-angle-right"></i>' : '<i class="fa-solid fa-angle-left"></i>';
    });

    // Tooltips solo cuando está colapsado
    const tip = document.createElement('div'); tip.className = 'sb-tooltip'; document.body.appendChild(tip);
    function showTip(e){
      if(!sidebar.classList.contains('collapsed')) return;
      const t = e.currentTarget.getAttribute('data-title'); if(!t) return;
      tip.textContent = t;
      const r = e.currentTarget.getBoundingClientRect();
      tip.style.top = (r.top + r.height/2) + 'px';
      tip.style.left = (r.right + 12) + 'px';
      tip.style.opacity = '1';
    }
    function hideTip(){ tip.style.opacity = '0'; }
    document.querySelectorAll('.sb-item').forEach(el=>{
      el.addEventListener('mouseenter', showTip);
      el.addEventListener('mouseleave', hideTip);
    });
  })();
  </script>

  {{-- SweetAlerts (flashes + confirmación eliminar) --}}
  @include('partials.sweetalert-flash')
  @include('partials.sweetalert-confirm-delete')

  {{-- Scripts extra por página --}}
  @stack('scripts')
</body>
</html>
