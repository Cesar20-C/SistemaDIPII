@php
  $badges = [
    'usuarios.index'     => \App\Models\User::count(),
    'proveedores.index'  => \App\Models\Proveedor::count(),
    'certificados.index' => \App\Models\Certificado::count(),
    'etiquetas.index'    => \App\Models\EtiquetaLote::count(),
  ];
@endphp

<aside id="sidebar" class="sidebar">
  <div class="sb-card">

    <div class="sb-head">
      <div class="sb-logo">
        <img src="{{ asset('imagen/Logo.png') }}" alt="Logo">
        <span class="sb-title">Menú</span>
      </div>
      <button id="sbToggle" class="sb-toggle" aria-label="Contraer/Expandir" aria-expanded="true">
        <i class="fa-solid fa-angle-left"></i>
      </button>
    </div>

    <ul class="sb-menu">
      <li>
        <a class="sb-item {{ request()->routeIs('certificados.*') ? 'is-active' : '' }}"
           href="{{ route('certificados.index') }}" data-title="Dashboard">
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
    </ul>

    <div class="sb-footer">
      @if(Route::has('profile.edit'))
        <a class="sb-item" href="{{ route('profile.edit') }}" data-title="Perfil">
          <i class="fa-solid fa-id-badge icon"></i>
          <span class="label">Perfil</span>
        </a>
      @endif

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="sb-item as-link" type="submit" data-title="Cerrar sesión">
          <i class="fa-solid fa-right-from-bracket icon"></i>
          <span class="label">Cerrar sesión</span>
        </button>
      </form>
    </div>

  </div>
</aside>
