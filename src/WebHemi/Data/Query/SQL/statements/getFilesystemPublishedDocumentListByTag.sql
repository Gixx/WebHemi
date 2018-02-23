SELECT
    fs.`id_filesystem`,
    fsd.`id_filesystem_document`,
    fs.`fk_application`,
    fs.`fk_category`,
    fs.`path`,
    fs.`basename`,
    REPLACE(CONCAT(fs.`path`,'/',fs.`basename`), '//', '/') AS uri,
    fs.`title`,
    fs.`description`,
    fsd.`fk_author`,
    fsd.`content_lead`,
    fsd.`content_body`,
    fs.`date_published`
FROM
    `webhemi_filesystem` AS fs
    INNER JOIN `webhemi_filesystem_document` AS fsd ON fs.`fk_filesystem_document` = fsd.`id_filesystem_document`
    INNER JOIN `webhemi_filesystem_to_filesystem_tag` AS fstt ON fs.`id_filesystem` = fstt.`fk_filesystem`
WHERE
    fs.`fk_application` = :idApplication AND
    fstt.`fk_filesystem_tag` = :idTag AND
    fs.`fk_filesystem_document` IS NOT NULL AND
    fs.`is_hidden` = 0 AND
    fs.`is_deleted` = 0 AND
    fs.`date_published` IS NOT NULL
ORDER BY
    :orderBy
LIMIT
    :limit
OFFSET
    :offset
