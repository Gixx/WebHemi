/**
 * WebHemi
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */
(function() {
    hljs.configure({
        tabReplace: '    '
    });
    hljs.initHighlighting();

    var moods = document.querySelectorAll('span.mood');
    for (var i = 0, num = moods.length; i < num; i++) {
        moods[i].innerHTML = emojione.toImage(moods[i].innerText);
    }

    document.querySelectorAll('body > header a').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();

            var drawerElementTag = e.target.classList[0];
            document.querySelector('body > '+drawerElementTag).classList.toggle('open');
        });
    });

    document.addEventListener('click', function(event) {
        var path = getEventPath(event);

        for (var i = 0, num = path.length; i < num; i++) {
            if (typeof path[i].tagName === 'undefined') {
                continue;
            }

            if (path[i].tagName.toLowerCase() === 'div' &&
                path[i].classList.contains('container')
            ){
                if (typeof path[i+1] !== 'undefined' &&
                    (path[i+1].tagName.toLowerCase() === 'nav' || path[i+1].tagName.toLowerCase() === 'aside') &&
                    path[i+1].classList.contains('open')
                ) {
                    break;
                }
            }

            if ((path[i].tagName.toLowerCase() === 'nav' || path[i].tagName.toLowerCase() === 'aside') &&
                path[i].classList.contains('open')
            ) {
                var menu = path[i];
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
