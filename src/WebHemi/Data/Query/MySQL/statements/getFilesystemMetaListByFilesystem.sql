SELECT
    fsm.`id_filesystem_meta`,
    fsm.`fk_filesystem`,
    fsm.`meta_key`,
    fsm.`meta_data`,
    fsm.`date_created`,
    fsm.`date_modified`
FROM
    `webhemi_filesystem_meta` AS fsm
WHERE
    fsm.`fk_filesystem` = :idFilesystem
ORDER BY
    fsm.`meta_key`
LIMIT
    :limit
OFFSET
    :offset
