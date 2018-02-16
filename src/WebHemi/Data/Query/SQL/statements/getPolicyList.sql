SELECT
    p.`id_policy`,
    p.`fk_resource`,
    p.`fk_application`,
    p.`name`,
    p.`title`,
    p.`description`,
    p.`method`,
    p.`is_read_only`,
    p.`date_created`,
    p.`date_modified`
FROM
    `webhemi_policy` AS p
ORDER BY
    p.`name`
LIMIT
    :limit
OFFSET
    :offset
