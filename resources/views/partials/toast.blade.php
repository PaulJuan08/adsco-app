{{-- Global Toast Container --}}
<div id="toast-container" aria-live="polite" aria-atomic="true"></div>

@php
    $toastMessages = [];
    if (session('success'))  $toastMessages[] = ['type' => 'success', 'msg' => session('success')];
    if (session('error'))    $toastMessages[] = ['type' => 'error',   'msg' => session('error')];
    if (session('warning'))  $toastMessages[] = ['type' => 'warning', 'msg' => session('warning')];
    if (session('info'))     $toastMessages[] = ['type' => 'info',    'msg' => session('info')];
@endphp

<script>
(function () {
    var DURATION = 3500; // ms visible before auto-dismiss

    var icons = {
        success: '<i class="fas fa-check-circle"></i>',
        error:   '<i class="fas fa-times-circle"></i>',
        warning: '<i class="fas fa-exclamation-triangle"></i>',
        info:    '<i class="fas fa-info-circle"></i>',
    };
    var titles = { success: 'Success', error: 'Error', warning: 'Warning', info: 'Info' };

    window.showToast = function (message, type, duration) {
        type = type || 'info';
        duration = duration || DURATION;
        var container = document.getElementById('toast-container');
        if (!container) return;

        var el = document.createElement('div');
        el.className = 'toast-item toast-' + type;
        el.style.setProperty('--toast-duration', (duration / 1000) + 's');
        el.innerHTML =
            '<span class="toast-icon">' + (icons[type] || icons.info) + '</span>' +
            '<div class="toast-body">' +
                '<div class="toast-title">' + (titles[type] || 'Notice') + '</div>' +
                '<div class="toast-msg">' + message + '</div>' +
            '</div>' +
            '<button class="toast-close" onclick="dismissToast(this.parentElement)" aria-label="Close"><i class="fas fa-times"></i></button>';

        container.appendChild(el);

        var timer = setTimeout(function () { dismissToast(el); }, duration);
        el._toastTimer = timer;
    };

    window.dismissToast = function (el) {
        if (!el || el._dismissing) return;
        el._dismissing = true;
        clearTimeout(el._toastTimer);
        el.classList.add('toast-hiding');
        el.addEventListener('animationend', function () { el.remove(); }, { once: true });
    };

    var INLINE_SELECTORS = [
        '.flash-alert', '.flash-success', '.flash-error', '.flash-warning', '.flash-info',
        '.alert-success', '.alert-error', '.alert-danger', '.alert-warning', '.alert-info',
        '.alert.alert-success', '.alert.alert-error', '.alert.alert-danger',
        '.alert.alert-warning', '.alert.alert-info',
    ].join(', ');

    // Immediately hide inline session alerts that duplicate the toast
    function removeInlineSessionAlerts() {
        document.querySelectorAll(INLINE_SELECTORS).forEach(function (el) {
            el.style.display = 'none';
        });
    }

    // Fire session toasts + remove duplicates on DOM ready
    document.addEventListener('DOMContentLoaded', function () {
        @foreach($toastMessages as $t)
        showToast(@json($t['msg']), '{{ $t['type'] }}');
        @endforeach

        removeInlineSessionAlerts();

        // Watch for dynamically injected inline alerts and remove them too
        if (window.MutationObserver) {
            var obs = new MutationObserver(removeInlineSessionAlerts);
            obs.observe(document.body, { childList: true, subtree: true });
        }
    });
})();
</script>
