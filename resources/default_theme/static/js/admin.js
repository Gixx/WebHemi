/**
 * WebHemi
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */
(function() {
    document.querySelectorAll('body > header a').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();

            var drawerElementTag = e.target.getAttribute('class');
            document.querySelector('body > '+drawerElementTag).classList.toggle('open');
        });
    });

    document.addEventListener('click', function(event) {
        for (var i = 0, num = event.path.length; i < num; i++) {
            if (typeof event.path[i].tagName === 'undefined') {
                continue;
            }

            if (event.path[i].tagName.toLowerCase() === 'div' &&
                event.path[i].classList.contains('container')
            ){
                if (typeof event.path[i+1] !== 'undefined' &&
                    (event.path[i+1].tagName.toLowerCase() === 'nav' || event.path[i+1].tagName.toLowerCase() === 'aside') &&
                    event.path[i+1].classList.contains('open')
                ) {
                    break;
                }
            }

            if ((event.path[i].tagName.toLowerCase() === 'nav' || event.path[i].tagName.toLowerCase() === 'aside') &&
                event.path[i].classList.contains('open')
            ) {
                var menu = event.path[i];
                event.preventDefault();
                menu.classList.remove('open');
                menu.classList.add('closing');
                setTimeout(function(){
                    menu.classList.remove('closing');
                }, 1000);
                break;
            }
        }
    });
})();
