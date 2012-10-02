CREATE TABLE files (
	path TEXT NOT NULL,
	type TEXT NOT NULL,
	size INTEGER,
	hash TEXT,
	version INTEGER,
	revision TEXT
);

CREATE TABLE IF NOT EXISTS files (
	instance TEXT NOT NULL,
	path TEXT NOT NULL,
	type text NOT NULL,
	size INT(10),
	hash TEXT,
	time INT(10),
	version INT(10),
	revision TEXT
) ENGINE=INNODB DEFAULT CHARSET=utf8;
