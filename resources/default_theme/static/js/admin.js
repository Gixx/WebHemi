/**
 * WebHemi
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */
// Set the general event listeners only, when the WebHemi is ready.
document.addEventListener('WebHemi.Ready', function () {
    document.querySelectorAll('button.add').forEach(function (element) {
        element.addEventListener('click', function (event) {
            let buttonElement = event.target;
        });
    });

    document.querySelectorAll('button.delete').forEach(function (element) {
        element.addEventListener('click', function (event) {
            let buttonElement = event.target;

        });
    });

    document.querySelectorAll('button.view').forEach(function (element) {
        element.addEventListener('click', function (event) {
            let buttonElement = event.target;
            let subject = buttonElement.dataset.name;
            let url = window.location.href;

            if (url.substr(-1) !== '/') {
                url += '/';
            }

            let ajaxOptions = {
                url: url + subject,
                method: 'GET',
                successCallback: function(data) {
                    console.log(data);
                }
            };

            WebHemi.components.Util.ajax(ajaxOptions);
        });
    });
});
