SELECT
    fs.*
FROM
    webhemi_filesystem AS fs
    INNER JOIN webhemi_filesystem_to_filesystem_tag AS fst ON fs.id_filesystem = fst.fk_filesystem
WHERE
    fs.fk_application = :applicationId AND
    fst.fk_filesystem_tag = :tagId AND
    fs.fk_filesystem_document IS NOT NULL AND
    fs.is_hidden = 0 AND
    fs.is_deleted = 0 AND
    fs.date_published IS NOT NULL
ORDER BY :orderBy
