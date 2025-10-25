@php($title = 'INICIAR SESIÓN')
<x-guest-layout :title="$title">
  <h1 class="title-centered">{{ $title }}</h1>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger" style="display:none">
      <ul class="m-0" style="padding-left:18px;">
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    <div class="field">
      <label class="label" for="email">Correo:</label>
      <div class="ctrl">
        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M4 6h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"/>
          <path d="m22 8-10 6L2 8"/>
        </svg>
        <input id="email" class="ipt" type="email" name="email" value="{{ old('email') }}"
               required autofocus autocomplete="username" placeholder="tucorreo@dominio.com">
      </div>
    </div>

    <div class="field">
      <label class="label" for="password">Contraseña:</label>
      <div class="ctrl">
        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <rect x="3" y="11" width="18" height="10" rx="2"/>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>

        <input id="password" class="ipt" type="password" name="password" required
               autocomplete="current-password" placeholder="••••••••">
        <button type="button" class="toggle" aria-label="Mostrar u ocultar contraseña" onclick="togglePass()">
          <svg id="eyeOn" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/><circle cx="12" cy="12" r="3"/>
          </svg>
          <svg id="eyeOff" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" style="display:none">
            <path d="M3 3l18 18M10.58 10.58A3 3 0 0 0 12 15a3 3 0 0 0 2.42-4.42M9.88 4.26A10.81 10.81 0 0 1 12 4c7 0 11 8 11 8a18.15 18.15 0 0 1-3.25 4.52"/><path d="M6.5 6.5A18.3 18.3 0 0 0 1 12s4 8 11 8a10.6 10.6 0 0 0 4.5-1"/>
          </svg>
        </button>
      </div>
    </div>

    <div class="actions">
      <label class="remember">
        <input type="checkbox" name="remember"> Recordarme
      </label>
    </div>

    <div class="actions" style="justify-content:flex-end; margin-top:12px;">
      <button class="btn" type="submit">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
        </svg>
        Entrar
      </button>
    </div>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    (function () {
      const authMsg = @json($errors->first('email') ?: $errors->first('password') ?: null);

      document.addEventListener('DOMContentLoaded', function () {
        if (authMsg) {
          Swal.fire({
            icon: 'error',
            title: 'Credenciales incorrectas',
            text: authMsg,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#76B041',
            allowOutsideClick: true,
            allowEscapeKey: true,
            timer: 5000,
            timerProgressBar: true
          });
        }
      });
    })();
  </script>

  <script>
    function togglePass(){
      const ip = document.getElementById('password');
      const on = document.getElementById('eyeOn');
      const off = document.getElementById('eyeOff');
      ip.type = ip.type === 'password' ? 'text' : 'password';
      const vis = ip.type === 'password';
      on.style.display = vis ? '' : 'none';
      off.style.display = vis ? 'none' : '';
    }
  </script>
</x-guest-layout>
