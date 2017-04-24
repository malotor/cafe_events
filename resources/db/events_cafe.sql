CREATE TABLE events(
   aggregate_id   VARCHAR(255) PRIMARY KEY     NOT NULL,
   type           TEXT  NOT NULL,
   created_at     TEXT  NOT NULL,
   data           TEXT  NOT NULL
);


CREATE TABLE tabs(
   tab_id   VARCHAR(255) PRIMARY KEY     NOT NULL,
   waiter   VARCHAR(50) NOT NULL,
   'table'    VARCHAR(50)  NOT NULL
);