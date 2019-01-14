SELECT
    a.`id_application`,
    a.`fk_domain`,
    a.`path`,
    a.`theme`,
    a.`locale`,
    a.`timezone`,
    a.`name`,
    a.`title`,
    a.`introduction`,
    a.`subject`,
    a.`description`,
    a.`keywords`,
    a.`copyright`,
    a.`is_read_only`,
    a.`is_enabled`,
    a.`date_created`,
    a.`date_modified`
FROM
    `webhemi_application` AS a
ORDER BY
    a.`name`
LIMIT
    :limit
OFFSET
    :offset
