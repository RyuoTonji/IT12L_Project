<div id="alertModal"
    class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex items-center justify-center transition-opacity duration-300 p-4">
    <div class="bg-white rounded-lg shadow-xl w-80 transform transition-all scale-100 p-4 relative">
        <div id="alertModalContent">
            <!-- Icon -->
            <div class="flex items-center justify-center w-8 h-8 mx-auto mb-2 rounded-full" id="alertIcon">
                <!-- Will be populated by JavaScript -->
            </div>

            <!-- Message -->
            <div class="text-center">
                <h3 class="text-sm font-semibold text-gray-900 mb-1" id="alertTitle">Notification</h3>
                <p class="text-xs text-gray-600 mb-3" id="alertMessage"></p>
            </div>

            <!-- Buttons -->
            <div class="flex justify-center space-x-3" id="alertButtons">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
    // Alert Modal Functions
    window.AlertModal = {
        modal: null,
        icon: null,
        title: null,
        message: null,
        buttons: null,

        init: function () {
            this.modal = document.getElementById('alertModal');
            this.icon = document.getElementById('alertIcon');
            this.title = document.getElementById('alertTitle');
            this.message = document.getElementById('alertMessage');
            this.buttons = document.getElementById('alertButtons');
        },

        show: function () {
            if (!this.modal) this.init();
            this.modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        },

        hide: function () {
            if (!this.modal) this.init();
            this.modal.classList.add('hidden');
            document.body.style.overflow = '';
        },

        showAlert: function (messageText, type = 'success', titleText = null) {
            if (!this.modal) this.init();

            // Set icon and colors based on type
            if (type === 'success') {
                this.icon.className = 'flex items-center justify-center w-8 h-8 mx-auto mb-2 rounded-full bg-green-100';
                this.icon.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                this.title.textContent = titleText || 'Success';
            } else if (type === 'error') {
                this.icon.className = 'flex items-center justify-center w-8 h-8 mx-auto mb-2 rounded-full bg-red-100';
                this.icon.innerHTML = '<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                this.title.textContent = titleText || 'Error';
            } else if (type === 'warning') {
                this.icon.className = 'flex items-center justify-center w-8 h-8 mx-auto mb-2 rounded-full bg-yellow-100';
                this.icon.innerHTML = '<svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                this.title.textContent = titleText || 'Warning';
            } else {
                this.icon.className = 'flex items-center justify-center w-8 h-8 mx-auto mb-2 rounded-full bg-blue-100';
                this.icon.innerHTML = '<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                this.title.textContent = titleText || 'Information';
            }

            this.message.textContent = messageText;

            // OK button only
            this.buttons.innerHTML = '<button onclick="AlertModal.hide()" class="px-5 py-1.5 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">OK</button>';

            this.show();
        },

        showConfirm: function (messageText, onConfirm, onCancel = null, titleText = 'Confirm Action') {
            if (!this.modal) this.init();

            // Warning style for confirmations
            this.icon.className = 'flex items-center justify-center w-8 h-8 mx-auto mb-2 rounded-full bg-yellow-100';
            this.icon.innerHTML = '<svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';

            this.title.textContent = titleText;
            this.message.textContent = messageText;

            // Confirm and Cancel buttons
            this.buttons.innerHTML = `
            <button onclick="AlertModal.cancel()" class="px-4 py-1.5 text-sm bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</button>
            <button onclick="AlertModal.confirm()" class="px-4 py-1.5 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Confirm</button>
        `;

            // Store callbacks
            this.onConfirmCallback = onConfirm;
            this.onCancelCallback = onCancel;

            this.show();
        },

        confirm: function () {
            this.hide();
            if (this.onConfirmCallback) {
                this.onConfirmCallback();
            }
        },

        cancel: function () {
            this.hide();
            if (this.onCancelCallback) {
                this.onCancelCallback();
            }
        }
    };

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        AlertModal.init();

        // Close modal when clicking outside
        document.getElementById('alertModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                AlertModal.hide();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !document.getElementById('alertModal').classList.contains('hidden')) {
                AlertModal.hide();
            }
        });
    });

    // Helper functions for easier access
    function showAlert(message, type = 'success') {
        AlertModal.showAlert(message, type);
    }

    function showConfirm(message, onConfirm, onCancel = null) {
        AlertModal.showConfirm(message, onConfirm, onCancel);
    }
</script>