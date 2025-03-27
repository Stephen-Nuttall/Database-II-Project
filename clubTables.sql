-- This only has the SQL to add the new club-related tables to the schema in phpMyAdmin.
-- Don't submit this file. Submit DB2-tables.sql instead.
-- Make sure if you create new tables to make the SQL code for them in DB-talbes.sql.

create table club
	(name		    varchar(32),
     club_id        varchar(10) not null,
	 advisor_id     varchar(10),
     president_id   varchar(10),
	 primary key(club_id),
     foreign key (advisor_id) references instructor (instructor_id)
	    on delete set null,
     foreign key (president_id) references student (student_id)
	    on delete set null
	);

create table clubParticipants
	(student_id     varchar(10) not null,
     club_id        varchar(10) not null,
	 primary key(student_id, club_id),
     foreign key (student_id) references student (student_id)
		on delete cascade,
     foreign key (club_id) references club (club_id)
		on delete cascade
	);