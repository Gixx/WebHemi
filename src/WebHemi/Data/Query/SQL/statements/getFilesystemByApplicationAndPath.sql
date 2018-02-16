SELECT
    fs.`id_filesystem`,
    fs.`fk_application`,
    fs.`fk_category`,
    fs.`fk_parent_node`,
    fs.`fk_filesystem_document`,
    fs.`fk_filesystem_file`,
    fs.`fk_filesystem_directory`,
    fs.`fk_filesystem_link`,
    fs.`path`,
    fs.`basename`,
    REPLACE(CONCAT(fs.`path`,'/',fs.`basename`), '//', '/') AS uri,
    fs.`title`,
    fs.`description`,
    fs.`is_hidden`,
    fs.`is_read_only`,
    fs.`is_deleted`,
    fs.`date_created`,
    fs.`date_modified`,
    fs.`date_published`
FROM
    `webhemi_filesystem` AS fs
WHERE
    fs.`fk_application` = :idApplication AND
    fs.`path` = :path AND
    fs.`basename` = :baseName
