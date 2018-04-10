CREATE TABLE IF NOT EXISTS {$table_name} (
  ID INT NOT NULL AUTO_INCREMENT,
  version INT NULL,
  chapter INT NULL,
  content_draft_hash VARCHAR(32) NULL,
  title_comment LONGTEXT NULL,
  comments LONGTEXT NULL,
  PRIMARY KEY  (ID),
  KEY version_idx (version ASC)
  ) {$charset_collate};