SELECT
    fsd.`id_filesystem_directory`,
    fsd.`description`,
    fsd.`directory_type` AS type,
    fsd.`is_autoindex`,
    fs.`fk_application` AS id_application,
    fs.`id_filesystem`,
    fs.`path`,
    fs.`basename`,
    REPLACE(CONCAT(fs.`path`,'/',fs.`basename`), '//', '/') AS uri,
    fs.`title`
FROM
    `webhemi_filesystem_directory` AS fsd
    INNER JOIN `webhemi_filesystem` AS fs ON fsd.`id_filesystem_directory` = fs.`fk_filesystem_directory`
WHERE
    fs.`fk_application` = :idApplication AND
    fsd.`proxy` = :proxy
