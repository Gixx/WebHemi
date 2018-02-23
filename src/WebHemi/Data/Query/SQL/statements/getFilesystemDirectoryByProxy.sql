SELECT
    fsd.`id_filesystem_directory`,
    fsd.`description`,
    fsd.`directory_type`,
    fsd.`proxy`,
    fsd.`is_autoindex`,
    fsd.`date_created`,
    fsd.`date_modified`
FROM
    `webhemi_filesystem_directory` AS fsd
WHERE
    fsd.`proxy` = :proxy
