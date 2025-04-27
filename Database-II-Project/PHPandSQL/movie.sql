
-- create datebases movie;

CREATE TABLE movies (
  id int,
  title varchar(20),
  year int,
  length int,
  genre varchar(20),
  PRIMARY KEY (id)
);

INSERT INTO movies (id, title, year, length, genre) VALUES (1, 'Gone With the Wind', 1939, 231, 'drama');
INSERT INTO movies (id, title, year, length, genre) VALUES (2, 'Star Wars', 1977, 124, "sciFi");
