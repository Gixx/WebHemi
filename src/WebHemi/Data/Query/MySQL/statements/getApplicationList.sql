SELECT
    a.`id_application`,
    a.`name`,
    a.`title`,
    a.`introduction`,
    a.`subject`,
    a.`description`,
    a.`keywords`,
    a.`copyright`,
    a.`domain`,
    a.`path`,
    a.`theme`,
    a.`type`,
    a.`locale`,
    a.`timezone`,
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
