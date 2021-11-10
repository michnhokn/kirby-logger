CREATE TABLE IF NOT EXISTS logs
(
    id        INTEGER PRIMARY KEY,
    type      TEXT NOT NULL,
    action    TEXT NOT NULL,
    user      TEXT NULL,
    old       TEXT NOT NULL,
    new       TEXT NOT NULL,
    timestamp TEXT NOT NULL DEFAULT current_timestamp
);