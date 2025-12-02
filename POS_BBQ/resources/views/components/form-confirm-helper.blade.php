<!-- Helper script for form submission in modals -->
<script>
    // Helper to get the button that triggered the confirm and submit its parent form
    function confirmAndSubmitForm(message, button) {
        showConfirm(message, function () {
            // Find the form that contains this button
            const form = button.closest('form');
            if (form) {
                form.submit();
            }
        });
    }
</script>