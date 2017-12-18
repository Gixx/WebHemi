-- New application
INSERT INTO `webhemi_application` VALUES
    (3, 'thomas', 'Thomas von Fürstenfeld', '# Üdvözöllek a blogomon!

Iván Gábor a becses nevem, és ezt a blogot a megboldogult "München dosszié" c. másik blogom méltó (ha nem méltóbb) utódjának szánom.

Az úgynevezett "*blog motor*" még fejlesztési fázisban van, de jól haladok vele. A dizájnt illetően nem agyaltam túl sokat, erőteljes ihletet merítettem egy közösségi oldalról.

**Na de mégis mi lesz itt ?**- jöhet a jogos kérdés.

Annak idején, amikor kiköltöztem Németországba, első kézből származó, hasznos információkat írtam a kezdeti lépésekről, amik elég nagy népszerűségnek örvendtek. Legalábbis a látogatószám és a visszajelzések erre engedtek következtetni. De aztán gonosz egyének feltörték az akkori tárhelyszolgáltatómat (a Wordpress alkalmazás biztonsági résein keresztül), és telepakolták weboldalamat mindenféle vírussal. Sajnálatos dolog volt, de annyira felbőszített az eset, hogy leszedtem az egészet, és elhatároztam, hogy ha a Wordpress nem képes biztonságos blogmotort a rendelkezésemre bocsájtani, akkor inkább írok egy sajátot.

Kevéske szabadidőmet az elmúlt lassan három évben ennek szenteltem, és már közel van a vége. Egyébként a régi blogról sikerült megmenteni a "*Kezdő lépések*" sorozatot, ezt természetesen újra fel fogom tölteni alkalomadtán.',
        'Személyes blog cenzúrázatlanul.', 'Ez a blog a megboldogult "München dosszié" c. korábbi blog utódja.', 'blog,Magyaroszág,Németország,München,Fürstenfeldbruck,Thomas,határátkelés,diszidálás', 'Minden jog fenntartva. © 2017. Thomas von Fürstenfeld', 'thomas.von', 'thomas', 'domain', 'hu_HU.UTF-8', 'Europe/Budapest', 1, 1, NOW(), NULL);

INSERT INTO `webhemi_user` VALUES
    (3, 'gabor.ivan', 'navig80@gmail.com', '$2y$09$dmrDfcYZt9jORA4vx9MKpeyRt0ilCH/gxSbSHcfBtGaghMJ30tKzS', 'hash-thomas-blog', 1, 1, NOW(), NULL);

INSERT INTO `webhemi_user_meta` VALUES
    (NULL, 3, 'display_name', 'Iván Gábor', NOW(), NULL),
    (NULL, 3, 'gender', 'male', NOW(), NULL),
    (NULL, 3, 'avatar', '/data/upload/filesystem/avatar/gabor.ivan.jpg', NOW(), NULL),
    (NULL, 3, 'cover_image', '/data/upload/filesystem/avatar/gabor.ivan-cover.jpg', NOW(), NULL),
    (NULL, 3, 'birth', '1980-02-19', NOW(), NULL),
    (NULL, 3, 'location', 'München', NOW(), NULL),
    (NULL, 3, 'address', '82256, Fürstenfeldbruck', NOW(), NULL),
    (NULL, 3, 'public_email', '', NOW(), NULL),
    (NULL, 3, 'phone_numbers', '', NOW(), NULL),
    (NULL, 3, 'instant_messengers', '', NOW(), NULL),
    (NULL, 3, 'workplaces', '', NOW(), NULL),
    (NULL, 3, 'social_networks', '{"LinkedIn":"https://www.linkedin.com/in/gaborivan/", "Facebook":"https://www.facebook.com/ivan.gabor.80", "Xing":"https://www.xing.com/profile/Gabor_Ivan2"}', NOW(), NULL),
    (NULL, 3, 'websites', '{"Blog":"http://thomas.von.fuerstenfeld.blog","Referencia":"http://www.gixx-web.com"}', NOW(), NULL),
    (NULL, 3, 'introduction', 'Férje egy csodálatos asszonynak, apja egy nagyszerű fiúnak, gazdája egy gyönyörű macskának. Foglalkozását tekintve web-fejlesztő egy németországi, de nemzetközi vizekre merészkedő fiatal E-kereskedelemmel foglalkozó cégnél.', NOW(), NULL);

INSERT INTO `webhemi_user_to_user_group` VALUES
    (NULL, 3, 1);

INSERT INTO `webhemi_filesystem_category` VALUES
    (1, 3, 'muenchen',  'München', 'München.', NOW(), NULL),
    (2, 3, 'teszt',  'Teszt', 'Csak teszteles miatt fenntartva.', NOW(), NULL);

INSERT INTO `webhemi_filesystem_tag` VALUES
    (1, 3, 'markdown', 'Markdown', '', NOW(), NULL),
    (2, 3, 'php', 'PHP', '', NOW(), NULL),
    (3, 3, 'kodolas', 'Kódolás', '', NOW(), NULL);

INSERT INTO `webhemi_filesystem_document` VALUES
    (1, NULL, 3, 1,
     'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed est est, eleifend ut auctor ut, congue vitae odio. Pellentesque ut fermentum nisi. Nullam ac pharetra arcu. Curabitur eu dapibus mauris. Nunc dapibus magna justo, auctor pretium nibh auctor sit amet. Vivamus ac dapibus tortor. In eu ex maximus, dictum lectus ut, ultricies justo. Sed elementum nisi purus, at convallis purus elementum a. Nullam magna ligula, luctus sed dictum id, tempor a odio.',
     '## Lists

1. First ordered list item
1. Another item
* Unordered sub-list.
1. Actual numbers don\'t matter, just that it\'s a number
2. Ordered sub-list
2. Ordered sub-list
2. Ordered sub-list
1. And another item.

* Unordered list can use asterisks
- Or minuses
+ Or pluses
+ Or pluses

In hendrerit arcu vulputate semper sollicitudin. Phasellus rhoncus at lectus eget vehicula. Ut commodo pulvinar orci vel fermentum. Nam fringilla eget ex sollicitudin pellentesque. Mauris quis pretium turpis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Sed blandit lacinia risus et tincidunt. Quisque et maximus dui, eu consectetur justo. Interdum et malesuada fames ac ante ipsum primis in faucibus. Sed non lectus ultricies, venenatis velit sit amet, posuere purus. Proin tincidunt tortor interdum dolor dapibus, ac placerat tortor vulputate. Nullam tristique viverra sapien, quis maximus metus. Curabitur gravida purus non elit tincidunt finibus. Nunc nec quam mattis, placerat tortor ut, laoreet nibh. Duis accumsan dui in lorem sagittis, sit amet cursus lacus vehicula. Fusce in pharetra nisl.

Vestibulum pulvinar nisi nec odio ultricies cursus. Quisque turpis neque, porttitor a dui id, malesuada congue erat. Phasellus rutrum fringilla laoreet. In at tincidunt nunc, ac iaculis quam. Vivamus congue molestie vestibulum. Praesent quis aliquam magna, vitae sollicitudin nunc. Fusce gravida diam sed justo tincidunt mattis sit amet nec dolor. Vivamus nec odio eget diam auctor sodales non tristique sem. In maximus eros a ipsum placerat interdum. Phasellus vulputate bibendum leo fringilla consequat. Nunc convallis erat vitae nisl ultricies, in varius metus rhoncus. Fusce suscipit ultrices sapien, a posuere erat pulvinar sit amet. Ut luctus nunc in nisi hendrerit consectetur. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Cras a orci ex.

Nam nec tortor ut nisi porta sodales. Phasellus blandit ante eget neque scelerisque eleifend. Vivamus et tristique magna, vel imperdiet nibh. Integer interdum libero tempus sem fringilla, ac imperdiet odio condimentum. Cras pellentesque diam eget mauris semper pharetra. Donec neque enim, hendrerit ac tempus non, dictum quis dui. Nam mollis, justo id dapibus elementum, dolor tellus sagittis mauris, sit amet posuere tortor nunc eget dolor. In congue neque id metus semper hendrerit. Curabitur hendrerit tempus sapien egestas suscipit. Pellentesque ac mi vitae nulla elementum tempus. Pellentesque tempor, eros id ultricies faucibus, augue enim imperdiet elit, eu facilisis risus lectus ac massa. Quisque eget ante diam.

> Blockquotes are very handy in email to emulate reply text.
> This line is part of the same quote.

Aliquam ac volutpat enim, sit amet sollicitudin ipsum. Aliquam erat volutpat. Nulla vel pulvinar velit. Proin feugiat, ex eget sodales tristique, lacus ante laoreet augue, vitae sagittis metus ex ultricies risus. Fusce id laoreet risus. Ut non urna nec mauris rhoncus molestie et id sem. Integer tempor et quam id posuere.

Aenean aliquet ipsum lectus, mattis dignissim nibh iaculis sed. Fusce imperdiet mi metus, in dictum urna blandit eu. Aenean eget pulvinar leo. Phasellus blandit malesuada magna, ut mollis lacus iaculis vel. Morbi imperdiet at ante et mattis. Sed efficitur orci libero, a pharetra risus laoreet quis. Fusce sapien dui, gravida sodales hendrerit sed, mollis sit amet lectus. Nullam et pharetra velit. Integer sodales est eu sodales hendrerit.

Nulla congue volutpat nulla, sed ullamcorper eros aliquet quis. Mauris pellentesque vitae purus at varius. Vestibulum eleifend ipsum nulla, eget condimentum urna efficitur ac. Ut in nibh et lacus maximus vestibulum in eu sapien. Maecenas in est lectus. Donec id lacus ante. Praesent lobortis risus a sem rhoncus, id sagittis velit commodo. Praesent accumsan tincidunt augue.

## Code and Syntax Highlighting in short

Inline `code` has `back-ticks around` it.

```php
<?php
namespace WestWing\Middleware;

use WestWing\Http\ResponseInterface;
use WestWing\Http\ServerRequestInterface;

/**
* Interface MiddlewareInterface.
*/
interface MiddlewareInterface
{
 /**
  * A middleware is a callable. It can do whatever is appropriate with the Request and Response objects.
  *
  * @param ServerRequestInterface $request
  * @param ResponseInterface      $response
  * @return void
  */
 public function __invoke(ServerRequestInterface&$request, ResponseInterface&$response);
}
```

But the GPS-Tool also requires such functionality that depend on the `Request` with various attributes. These non-fix
middleware services are the **actions**. A Middleware is called as action middleware when it implements the
`WestWing\Middleware\ActionMiddlewareInterface`.

Since these actions depend on the request URI, we don\'t add them to the middleware pipeline - although  it\'s possible,
but it makes no sense - but we define them in the router configuration - which is module-based:

```php
<?php
$router = [
 \'router\' => [
     \'Website\' => [
         \'some-page-alias\' => [
             \'path\' => \'/path/to/pattern/{name:.+}/{id:\d+}[/optional]\',
             \'middleware\' => WebHemi\Middleware\Action\Website\IndexAction::class,
             \'allowed_methods\' => [\'GET\']
         ]
     ]
 ]
];
```

## Tables

| Attribute name | Attribute value (example) |
|:--- |:--- |
| ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS | WestWing\Middleware\Action\SomeAction::class |
| ServerRequestInterface::REQUEST_ATTR_ROUTING_PARAMETERS | `[\'name\' => \'any\', \'id\' => \'12\']` |

## Blockquotes

> Blockquotes are very handy in email.

Quote break.

> This is a very long line that will still be quoted properly when it wraps. Oh boy let\'s keep writing to make sure this is long enough to actually wrap for everyone. Oh, you can *put* **Markdown** into a blockquote.

## Inline HTML

<dl>
<dt>Definition list</dt>
<dd>Is something people use sometimes.</dd>

<dt>Markdown in HTML</dt>
<dd>Does *not* work **very** well. Use HTML <em>tags</em>.</dd>
</dl>

## Horizontal Rule

Three or more...

---

Hyphens

***

Asterisks

___

Underscores

## Line Breaks

Here\'s a line for us to start with.

This line is separated from the one above by two newlines, so it will be a *separate paragraph*.

This line is also a separate paragraph, but...
This line is only separated by a single newline, so it\'s a separate line in the *same paragraph*.',
     '2017-10-28 13:25:30',
     NULL
    ),
    (2, NULL, 3, 1,
         'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed est est, eleifend ut auctor ut, congue vitae odio. Pellentesque ut fermentum nisi. Nullam ac pharetra arcu. Curabitur eu dapibus mauris. Nunc dapibus magna justo, auctor pretium nibh auctor sit amet. Vivamus ac dapibus tortor. In eu ex maximus, dictum lectus ut, ultricies justo. Sed elementum nisi purus, at convallis purus elementum a. Nullam magna ligula, luctus sed dictum id, tempor a odio.',
        '## Lists

1. First ordered list item
1. Another item
   * Unordered sub-list.
1. Actual numbers don\'t matter, just that it\'s a number
   2. Ordered sub-list
   2. Ordered sub-list
   2. Ordered sub-list
1. And another item.

* Unordered list can use asterisks
- Or minuses
  + Or pluses
  + Or pluses

In hendrerit arcu vulputate semper sollicitudin. Phasellus rhoncus at lectus eget vehicula. Ut commodo pulvinar orci vel fermentum. Nam fringilla eget ex sollicitudin pellentesque. Mauris quis pretium turpis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Sed blandit lacinia risus et tincidunt. Quisque et maximus dui, eu consectetur justo. Interdum et malesuada fames ac ante ipsum primis in faucibus. Sed non lectus ultricies, venenatis velit sit amet, posuere purus. Proin tincidunt tortor interdum dolor dapibus, ac placerat tortor vulputate. Nullam tristique viverra sapien, quis maximus metus. Curabitur gravida purus non elit tincidunt finibus. Nunc nec quam mattis, placerat tortor ut, laoreet nibh. Duis accumsan dui in lorem sagittis, sit amet cursus lacus vehicula. Fusce in pharetra nisl.

Vestibulum pulvinar nisi nec odio ultricies cursus. Quisque turpis neque, porttitor a dui id, malesuada congue erat. Phasellus rutrum fringilla laoreet. In at tincidunt nunc, ac iaculis quam. Vivamus congue molestie vestibulum. Praesent quis aliquam magna, vitae sollicitudin nunc. Fusce gravida diam sed justo tincidunt mattis sit amet nec dolor. Vivamus nec odio eget diam auctor sodales non tristique sem. In maximus eros a ipsum placerat interdum. Phasellus vulputate bibendum leo fringilla consequat. Nunc convallis erat vitae nisl ultricies, in varius metus rhoncus. Fusce suscipit ultrices sapien, a posuere erat pulvinar sit amet. Ut luctus nunc in nisi hendrerit consectetur. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Cras a orci ex.

Nam nec tortor ut nisi porta sodales. Phasellus blandit ante eget neque scelerisque eleifend. Vivamus et tristique magna, vel imperdiet nibh. Integer interdum libero tempus sem fringilla, ac imperdiet odio condimentum. Cras pellentesque diam eget mauris semper pharetra. Donec neque enim, hendrerit ac tempus non, dictum quis dui. Nam mollis, justo id dapibus elementum, dolor tellus sagittis mauris, sit amet posuere tortor nunc eget dolor. In congue neque id metus semper hendrerit. Curabitur hendrerit tempus sapien egestas suscipit. Pellentesque ac mi vitae nulla elementum tempus. Pellentesque tempor, eros id ultricies faucibus, augue enim imperdiet elit, eu facilisis risus lectus ac massa. Quisque eget ante diam.

> Blockquotes are very handy in email to emulate reply text.
> This line is part of the same quote.

Aliquam ac volutpat enim, sit amet sollicitudin ipsum. Aliquam erat volutpat. Nulla vel pulvinar velit. Proin feugiat, ex eget sodales tristique, lacus ante laoreet augue, vitae sagittis metus ex ultricies risus. Fusce id laoreet risus. Ut non urna nec mauris rhoncus molestie et id sem. Integer tempor et quam id posuere.

Aenean aliquet ipsum lectus, mattis dignissim nibh iaculis sed. Fusce imperdiet mi metus, in dictum urna blandit eu. Aenean eget pulvinar leo. Phasellus blandit malesuada magna, ut mollis lacus iaculis vel. Morbi imperdiet at ante et mattis. Sed efficitur orci libero, a pharetra risus laoreet quis. Fusce sapien dui, gravida sodales hendrerit sed, mollis sit amet lectus. Nullam et pharetra velit. Integer sodales est eu sodales hendrerit.

Nulla congue volutpat nulla, sed ullamcorper eros aliquet quis. Mauris pellentesque vitae purus at varius. Vestibulum eleifend ipsum nulla, eget condimentum urna efficitur ac. Ut in nibh et lacus maximus vestibulum in eu sapien. Maecenas in est lectus. Donec id lacus ante. Praesent lobortis risus a sem rhoncus, id sagittis velit commodo. Praesent accumsan tincidunt augue.

## Code and Syntax Highlighting in short

Inline `code` has `back-ticks around` it.

```php
<?php
namespace WestWing\Middleware;

use WestWing\Http\ResponseInterface;
use WestWing\Http\ServerRequestInterface;

/**
 * Interface MiddlewareInterface.
 */
interface MiddlewareInterface
{
    /**
     * A middleware is a callable. It can do whatever is appropriate with the Request and Response objects.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return void
     */
    public function __invoke(ServerRequestInterface&$request, ResponseInterface&$response);
}
```

But the GPS-Tool also requires such functionality that depend on the `Request` with various attributes. These non-fix
middleware services are the **actions**. A Middleware is called as action middleware when it implements the
`WestWing\Middleware\ActionMiddlewareInterface`.

Since these actions depend on the request URI, we don\'t add them to the middleware pipeline - although  it\'s possible,
but it makes no sense - but we define them in the router configuration - which is module-based:

```php
<?php
$router = [
    \'router\' => [
        \'Website\' => [
            \'some-page-alias\' => [
                \'path\' => \'/path/to/pattern/{name:.+}/{id:\d+}[/optional]\',
                \'middleware\' => WebHemi\Middleware\Action\Website\IndexAction::class,
                \'allowed_methods\' => [\'GET\']
            ]
        ]
    ]
];
```

## Tables

| Attribute name | Attribute value (example) |
|:--- |:--- |
| ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS | WestWing\Middleware\Action\SomeAction::class |
| ServerRequestInterface::REQUEST_ATTR_ROUTING_PARAMETERS | `[\'name\' => \'any\', \'id\' => \'12\']` |

## Blockquotes

> Blockquotes are very handy in email.

Quote break.

> This is a very long line that will still be quoted properly when it wraps. Oh boy let\'s keep writing to make sure this is long enough to actually wrap for everyone. Oh, you can *put* **Markdown** into a blockquote.

## Inline HTML

<dl>
  <dt>Definition list</dt>
  <dd>Is something people use sometimes.</dd>

  <dt>Markdown in HTML</dt>
  <dd>Does *not* work **very** well. Use HTML <em>tags</em>.</dd>
</dl>

## Horizontal Rule

Three or more...

---

Hyphens

***

Asterisks

___

Underscores

## Line Breaks

Here\'s a line for us to start with.

This line is separated from the one above by two newlines, so it will be a *separate paragraph*.

This line is also a separate paragraph, but...
This line is only separated by a single newline, so it\'s a separate line in the *same paragraph*.',
        NOW(),
        NULL
    );

INSERT INTO `webhemi_filesystem` VALUES
    (7, 3, NULL, NULL, NULL, NULL, 1,    NULL, '/', 'kategoriak', 'Kategóriák', '', 1, 1, 0, NOW(), NULL, NOW()),
    (8, 3, NULL, NULL, NULL, NULL, 2,    NULL, '/', 'cimkek', 'Címkék', '', 1, 1, 0, NOW(), NULL, NOW()),
    (9, 3, NULL, NULL, NULL, NULL, 3,    NULL, '/', 'archivum', 'Archívum', '', 1, 1, 0, NOW(), NULL, NOW()),
    (10, 3, NULL, NULL, NULL, NULL, 4,    NULL, '/', 'kepek', 'Feltöltött képek', '', 1, 1, 0, NOW(), NULL, NOW()),
    (11, 3, NULL, NULL, NULL, NULL, 5,    NULL, '/', 'fajlok', 'Feltöltött fájlok', '', 1, 1, 0, NOW(), NULL, NOW()),
    (12, 3, NULL, NULL, NULL, NULL, 6,    NULL, '/', 'felhasznalo', 'Felhasználó', '', 1, 1, 0, NOW(), NULL, NOW()),
    (13, 3, 1,    NULL, 1,    NULL, NULL, NULL, '/', 'muenchen.html', 'München',  'Jó kis város, szeretem.', 0, 0, 0, NOW(), NULL, '2017-10-28 20:20:20'),
    (14, 3, 1,    NULL, 2,    NULL, NULL, NULL, '/', 'a_perfect_day.html', 'Hogy indítsuk jól a napot: egy finom, gőzőlgő tea esete',  'Jó tudni...', 0, 0, 0, NOW(), NULL, NOW());

INSERT INTO `webhemi_filesystem_to_filesystem_tag` VALUES
    (NULL, 13, 1),
    (NULL, 13, 2),
    (NULL, 13, 3),
    (NULL, 14, 1),
    (NULL, 14, 3);

INSERT INTO `webhemi_filesystem_meta` VALUES
    (NULL, 13, 'subject', 'test subject', NOW(), NULL),
    (NULL, 13, 'description', 'test description', NOW(), NULL),
    (NULL, 13, 'keywords', 'lorem,ipsum,dolor,sit', NOW(), NULL),
    (NULL, 13, 'illustration', '/data/upload/filesystem/images/Nature.jpg', NOW(), NULL),
    (NULL, 13, 'share_image', '/data/upload/filesystem/images/Nature_600x600.png', NOW(), NULL),
    (NULL, 13, 'author_mood_key', 'hugging', NOW(), NULL),
    (NULL, 13, 'author_mood_name', 'feels beloved', NOW(), NULL),
    (NULL, 13, 'location', 'München', NOW(), NULL);

/*
SELECT
    tag.name,
    tag.title,
    (
        SELECT
            fs.basename
        FROM
            webhemi_filesystem AS fs
            INNER JOIN webhemi_filesystem_directory AS fsd ON fs.fk_filesystem_directory = fsd.id_filesystem_directory
        WHERE
            fsd.proxy = 'list-tag'
            AND fs.fk_application = 3
    ) AS path
FROM
    webhemi_filesystem_tag AS tag
WHERE
    tag.fk_application = 3;


SELECT * FROM webhemi_filesystem WHERE fk_application = 3 AND is_hidden = 0 AND is_deleted = 0 AND date_published IS NOT NULL AND fk_filesystem_document IS NOT NULL GROUP BY YEAR(date_published), MONTH(date_published) ORDER BY date_published;
*/
