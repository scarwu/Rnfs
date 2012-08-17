CREATE TABLE files (
	path TEXT NOT NULL,
	type TEXT NOT NULL,
	size INTEGER,
	version INTEGER,
	hash TEXT,
	PRIMARY KEY(path ASC)
);
