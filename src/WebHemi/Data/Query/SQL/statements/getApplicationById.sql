SELECT
    `id_application`,
    `name`,
    `title`,
    `introduction`,
    `subject`,
    `description`,
    `keywords`,
    `copyright`,
    `path`,
    `theme`,
    `type`,
    `locale`,
    `timezone`,
    `is_read_only`,
    `is_enabled`,
    `date_created`,
    `date_modified`
FROM
    `webhemi_application`
WHERE
    `id_application` = :idApplication
