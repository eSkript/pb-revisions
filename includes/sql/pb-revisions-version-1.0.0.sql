
CREATE TABLE IF NOT EXISTS {$table_name} (
  ID INT NOT NULL AUTO_INCREMENT,
  date DATETIME NULL,
  number VARCHAR(45) NULL,
  type SET('major', 'minor', 'patch') NULL,
  author INT NOT NULL,
  draft TINYINT(1) NULL,
  comment LONGTEXT NULL,
  PRIMARY KEY  (ID)
  ) {$charset_collate};