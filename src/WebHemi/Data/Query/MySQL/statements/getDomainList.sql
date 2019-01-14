SELECT
  d.`id_domain`,
  d.`schema`,
  d.`domain`,
  d.`title`,
  d.`is_default`,
  d.`is_read_only`,
  d.`date_created`,
  d.`date_modified`
FROM
  `webhemi_domain` AS d
ORDER BY
  d.`domain`
LIMIT
  :limit
OFFSET
  :offset
