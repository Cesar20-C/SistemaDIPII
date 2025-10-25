<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const flash = {
    success: @json(session('success')),
    error:   @json(session('error')),
    warning: @json(session('warning')),
    info:    @json(session('info')),
  };
  const errors = @json($errors->any() ? $errors->all() : []);

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (t) => {
      t.addEventListener('mouseenter', Swal.stopTimer);
      t.addEventListener('mouseleave', Swal.resumeTimer);
    }
  });

  // Errores de validación (lista). Uso modal en top-end (no toast) para que quepa el listado.
  if (errors.length) {
    const html = '<ul style="text-align:left;margin:0 0 0 1rem;">'
               + errors.map(e => `<li>${e}</li>`).join('')
               + '</ul>';
    Swal.fire({
      position: 'top-end',   // esquina superior derecha
      icon: 'error',
      title: 'No se pudo guardar',
      html,
      width: 420,
      showConfirmButton: false,
      timer: 6000,
      timerProgressBar: true,
      backdrop: false        // no bloquea toda la pantalla
    });
    return;
  }

  if (flash.success) { Toast.fire({ icon: 'success', title: flash.success }); return; }
  if (flash.error)   { Toast.fire({ icon: 'error',   title: flash.error   }); return; }
  if (flash.warning) { Toast.fire({ icon: 'warning', title: flash.warning }); return; }
  if (flash.info)    { Toast.fire({ icon: 'info',    title: flash.info    }); return; }
});
</script>
