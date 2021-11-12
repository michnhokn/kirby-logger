CREATE TABLE IF NOT EXISTS logs
(
    id        INTEGER PRIMARY KEY,
    type      TEXT NOT NULL,
    action    TEXT NOT NULL,
    user      TEXT NULL,
    slug      TEXT NOT NULL,
    language  TEXT NOT NULL,
    oldData   TEXT NULL,
    newData   TEXT NOT NULL,
    timestamp TEXT NOT NULL DEFAULT current_timestamp
);