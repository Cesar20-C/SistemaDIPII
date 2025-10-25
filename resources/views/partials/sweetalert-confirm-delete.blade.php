
<script>
(function () {
  function bindDeleteConfirm() {
    // Intercepta cualquier submit de formularios con clase .js-delete
    document.addEventListener('submit', function (e) {
      const form = e.target.closest('form.js-delete');
      if (!form) return;
      if (form.dataset.confirmed === 'true') return; // ya confirmado, deja enviar

      e.preventDefault();

      const title   = form.dataset.title   || '¿Eliminar?';
      const text    = form.dataset.text    || 'Esta acción no se puede deshacer.';
      const confirm = form.dataset.confirm || 'Eliminar';
      const cancel  = form.dataset.cancel  || 'Cancelar';

      Swal.fire({
        position: 'center',     // <<< centrado
        icon: 'warning',
        title: title,
        html: text,
        width: 420,
        showCancelButton: true,
        confirmButtonText: confirm,
        cancelButtonText: cancel,
        reverseButtons: true,
        focusCancel: true,
        allowOutsideClick: false,
        allowEscapeKey: true,
        backdrop: true,         // oscurece el fondo (puedes poner false si no lo quieres)
        confirmButtonColor: '#dc2626', // rojo para acción destructiva
        cancelButtonColor: '#6b7280'
      }).then((result) => {
        if (result.isConfirmed) {
          form.dataset.confirmed = 'true';
          form.submit();
        }
      });
    }, true);
  }

  function ensureSwalAndBind() {
    if (window.Swal) { bindDeleteConfirm(); return; }
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    s.onload = bindDeleteConfirm;
    document.head.appendChild(s);
  }

  // Soporta cargas normales y navegaciones dinámicas
  document.addEventListener('DOMContentLoaded', ensureSwalAndBind);
  document.addEventListener('turbo:load', ensureSwalAndBind);
  document.addEventListener('livewire:navigated', ensureSwalAndBind);
})();
</script>
