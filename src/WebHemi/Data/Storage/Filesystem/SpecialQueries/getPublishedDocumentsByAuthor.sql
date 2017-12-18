SELECT
    fs.*
FROM
    webhemi_filesystem AS fs
    INNER JOIN webhemi_filesystem_document AS fsd ON fs.fk_filesystem_document = fsd.id_filesystem_document
WHERE
    fs.fk_application = :applicationId AND
    fsd.fk_author = :userId AND
    fs.fk_filesystem_document IS NOT NULL AND
    fs.is_hidden = 0 AND
    fs.is_deleted = 0 AND
    fs.date_published IS NOT NULL
ORDER BY :orderBy
