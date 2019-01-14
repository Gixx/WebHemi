SELECT
    p.`id_policy`,
    p.`fk_resource`,
    p.`fk_application`,
    p.`name`,
    p.`title`,
    p.`description`,
    p.`is_read_only`,
    p.`date_created`,
    p.`date_modified`
FROM
    `webhemi_policy` AS p
    INNER JOIN `webhemi_user_to_policy` AS utp ON p.id_policy = utp.fk_policy
WHERE
    utp.`fk_user` = :idUser
ORDER BY
    p.`name`
LIMIT
    :limit
OFFSET
    :offset
