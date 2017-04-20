CREATE TABLE events(
   aggregate_id   VARCHAR(255) PRIMARY KEY     NOT NULL,
   type           TEXT  NOT NULL,
   created_at     TEXT  NOT NULL,
   data           TEXT  NOT NULL
);