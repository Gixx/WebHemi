SELECT
    r.`id_resource`,
    r.`name`,
    r.`title`,
    r.`description`,
    r.`type`,
    r.`is_read_only`,
    r.`date_created`,
    r.`date_modified`
FROM
    `webhemi_resource` AS r
ORDER BY
    r.`name`
LIMIT
    :limit
OFFSET
    :offset
