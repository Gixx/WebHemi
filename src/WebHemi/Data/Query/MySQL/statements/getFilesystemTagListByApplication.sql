SELECT
    fst.`id_filesystem_tag`,
    fst.`fk_application`,
    fst.`name`,
    fst.`title`,
    fst.`description`,
    fst.`date_created`,
    fst.`date_modified`
FROM
    `webhemi_filesystem_tag` AS fst
WHERE
    fst.`fk_application` = :idApplication
ORDER BY
    `name`
LIMIT
    :limit
OFFSET
    :offset
