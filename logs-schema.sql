CREATE TABLE IF NOT EXISTS logs
(
    id           INTEGER PRIMARY KEY,
    type         TEXT NOT NULL,
    action       TEXT NOT NULL,
    userId       TEXT NULL,
    oldValue     TEXT NULL,
    currentValue TEXT NOT NULL,
    timestamp    TEXT NOT NULL DEFAULT current_timestamp
);

CREATE INDEX index_users ON logs (userId);
CREATE INDEX index_actions ON logs (action);
CREATE INDEX index_types ON logs (type);