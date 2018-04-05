SELECT
    fsd.`id_filesystem_document`,
    fsd.`fk_parent_revision`,
    fsd.`fk_author`,
    fsd.`content_revision`,
    fsd.`content_lead`,
    fsd.`content_body`,
    fsd.`date_created`,
    fsd.`date_modified`
FROM
    `webhemi_filesystem_document` AS fsd
ORDER BY
    fsd.`id_filesystem_document`
LIMIT
    :limit
OFFSET
    :offset
