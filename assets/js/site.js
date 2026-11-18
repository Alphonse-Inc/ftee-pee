// Small site script: confirm destructive admin actions. Navigation and FAQs use HTML only.
(function () {
    var forms = document.querySelectorAll('form[data-confirm]');
    Array.prototype.forEach.call(forms, function (form) {
        form.addEventListener('submit', function (event) {
            var message = form.getAttribute('data-confirm') || 'Are you sure?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
}());
