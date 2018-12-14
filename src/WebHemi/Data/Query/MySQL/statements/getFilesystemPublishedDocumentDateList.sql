SELECT
    fs.`date_published_archive`,
    COUNT(*) AS number_of_publications
FROM
    `webhemi_filesystem` AS fs
WHERE
    fs.`fk_application` = :idApplication AND
    fs.`fk_filesystem_document` IS NOT NULL AND
    fs.`is_hidden` = 0 AND
    fs.`is_deleted` = 0 AND
    fs.`date_published` IS NOT NULL
GROUP BY
    fs.`date_published_archive`
ORDER BY
    fs.`date_published_archive` DESC
LIMIT
    :limit
OFFSET
    :offset
