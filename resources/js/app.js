import './bootstrap';

import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

window.Alpine = Alpine;
window.flatpickr = flatpickr;

// Reusable popup date/time pickers (IECMS-wide). Keeps the underlying
// input's value in the machine-readable format (Y-m-d / H:i) so
// x-model bindings and server-side validation are unaffected, while
// showing a friendlier format to the user via Flatpickr's altInput.
window.iecmsDatePicker = function (el, options = {}) {
    // onChange alone only fires reliably when a date is picked from the
    // calendar. Typing into the altInput (allowInput: true) updates the
    // underlying value on blur/close instead, which onChange can miss —
    // leaving Alpine's x-model (and anything reactive off it, like the
    // age-category badges) stale. onValueUpdate/onClose cover those paths
    // too; the dispatch is idempotent so firing it more than once is safe.
    const syncToAlpine = () => el.dispatchEvent(new Event('input', { bubbles: true }));

    return window.flatpickr(el, Object.assign({
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true,
        disableMobile: true,
        onChange: syncToAlpine,
        onValueUpdate: syncToAlpine,
        onClose: syncToAlpine,
    }, options));
};

window.iecmsTimePicker = function (el, options = {}) {
    return window.flatpickr(el, Object.assign({
        noCalendar: true,
        enableTime: true,
        dateFormat: 'H:i',
        altInput: true,
        altFormat: 'h:i K',
        allowInput: true,
        disableMobile: true,
        onChange: function () {
            el.dispatchEvent(new Event('input', { bubbles: true }));
        },
    }, options));
};

Alpine.start();
