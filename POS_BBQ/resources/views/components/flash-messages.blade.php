<!-- Session Flash Messages Handler -->
@if(session('success') || session('error') || session('warning') || session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                AlertModal.showAlert('{{ session('success') }}', 'success');
            @elseif(session('error'))
                AlertModal.showAlert('{{ session('error') }}', 'error');
            @elseif(session('warning'))
                AlertModal.showAlert('{{ session('warning') }}', 'warning');
            @elseif(session('info'))
                AlertModal.showAlert('{{ session('info') }}', 'info');
            @endif
    });
    </script>
@endif