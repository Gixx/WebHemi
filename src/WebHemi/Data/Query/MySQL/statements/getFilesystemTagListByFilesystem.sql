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
    INNER JOIN `webhemi_filesystem_to_filesystem_tag` AS fstfst ON fst.`id_filesystem_tag` = fstfst.`fk_filesystem_tag`
WHERE
    fstfst.`fk_filesystem` = :idFilesystem
ORDER BY
    `name`
LIMIT
    :limit
OFFSET
    :offset
