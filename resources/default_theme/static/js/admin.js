/**
 * WebHemi
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */
// Set the general event listeners only, when the WebHemi is ready.
document.addEventListener('WebHemi.Ready', function () {
    document.querySelectorAll('button.add').forEach(function (element) {
        element.addEventListener('click', function (event) {
            let buttonElement = event.target;
            let dialogId = buttonElement.dataset.dialog || 'dialog';
            let dialogElement = document.querySelector('dialog#'+dialogId);

            if (dialogElement && typeof dialogElement.component !== 'undefined') {
                /** @type {DialogElement} */
                let component = dialogElement.component;

                component.open();
            }
        });
    });
});


/*
(function() {

    document.querySelectorAll('button.add').forEach(function (element) {
        element.addEventListener('click', function (event) {
            location.href = _WebHemiApplication.currentUri+'/add';
        });
    });
    document.querySelectorAll('button.edit').forEach(function (element) {
        element.addEventListener('click', function (event) {
            location.href = _WebHemiApplication.currentUri+'/edit';
        });
    });

    document.querySelectorAll('button.view').forEach(function (element) {
        element.addEventListener('click', function (event) {
            location.href = _WebHemiApplication.currentUri+'/'+event.target.dataset.name;
        });
    });

    document.querySelectorAll('button.delete').forEach(function (element) {
        element.addEventListener('click', function (event) {
            if (confirm('Are you sure to delete this item?')) {
                let deleteEndpoint = _WebHemiApplication.currentUri+'/'+event.target.dataset.name+'/';
                fetch(deleteEndpoint, {
                    method: 'delete',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({csrf: _CSRF})
                }).then(function(response) {
                    if (response.ok) {
                        return response
                    } else {
                        console.log('Looks like there was a problem.', response);
                        let error = new Error(response.statusText || response.status);
                        error.response = response;
                        throw error
                    }
                }).then(function(response) {
                    return response.json();
                }).then(function(data) {
                    if (data.result) {
                        event.target.closest('tr').remove();
                    } else {
                        event.target.style.border = '1px solid red';
                    }
                }).catch(function(err) {
                    console.log('Fetch Error :-S', err);
                });
            } else {
                return false;
            }
        });
    });
})();
*/
