CREATE TABLE IF NOT EXISTS monolog
(
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  channel    VARCHAR(255),
  level      VARCHAR(255),
  message    TEXT,
  context    TEXT,
  extra      TEXT,
  created_at TEXT
)
