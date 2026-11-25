// Confirm destructive actions without inline handlers (keeps the CSP strict).
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
        event.preventDefault();
        return;
    }
    // Disable submit buttons after the first click: the server is idempotent anyway,
    // this just avoids confusing the user with duplicate requests.
    if (form.dataset.once === undefined) return;
    form.querySelectorAll('button[type=submit]').forEach((button) => {
        button.disabled = true;
        button.dataset.label && (button.textContent = button.dataset.label);
    });
});
