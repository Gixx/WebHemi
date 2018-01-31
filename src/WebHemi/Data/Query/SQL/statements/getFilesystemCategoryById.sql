SELECT
    fsc.`id_filesystem_category`,
    fsc.`fk_application`,
    fsc.`name`,
    fsc.`title`,
    fsc.`description`,
    fsc.`item_order`,
    fsc.`date_created`,
    fsc.`date_modified`
FROM
    `webhemi_filesystem_category` AS fsc
WHERE
    fsc.`id_filesystem_category` = :idCategory
