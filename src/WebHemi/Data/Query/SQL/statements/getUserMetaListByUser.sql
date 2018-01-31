SELECT
    um.`id_user_meta`,
    um.`fk_user`,
    um.`meta_key`,
    um.`meta_data`,
    um.`date_created`,
    um.`date_modified`
FROM
    `webhemi_user_meta` AS um
WHERE
    um.`fk_user` = :userId
ORDER BY
    um.`meta_key`
LIMIT
    :limit
OFFSET
    :offset
