create table account
	(email		varchar(50),
	 password	varchar(20) not null,
	 type		varchar(20),
	 primary key(email)
	);

create table department
	(dept_name	varchar(100),
	 location	varchar(100),
	 primary key (dept_name)
	);

create table instructor
	(instructor_id		varchar(10),
	 instructor_name	varchar(50) not null,
	 title 			varchar(30),
	 dept_name		varchar(100),
	 email			varchar(50) not null,
	 primary key (instructor_id)
	);

create table student
	(student_id		varchar(10),
	 name			varchar(20) not null,
	 email			varchar(50) not null,
	 dept_name		varchar(100),
	 primary key (student_id),
	 foreign key (dept_name) references department (dept_name)
		on delete set null
	);

create table PhD
	(student_id			varchar(10),
	 qualifier			varchar(30),
	 proposal_defence_date		date,
	 dissertation_defence_date	date,
	 primary key (student_id),
	 foreign key (student_id) references student (student_id)
		on delete cascade
	);

create table master
	(student_id		varchar(10),
	 total_credits		int,
	 primary key (student_id),
	 foreign key (student_id) references student (student_id)
		on delete cascade
	);

create table undergraduate
	(student_id		varchar(10),
	 total_credits		int,
	 class_standing		varchar(10)
		check (class_standing in ('Freshman', 'Sophomore', 'Junior', 'Senior')),
	 primary key (student_id),
	 foreign key (student_id) references student (student_id)
		on delete cascade
	);

create table classroom
	(classroom_id 		varchar(8),
	 building		varchar(15) not null,
	 room_number		varchar(7) not null,
	 capacity		numeric(4,0),
	 primary key (classroom_id)
	);

create table time_slot
	(time_slot_id		varchar(8),
	 day			varchar(10) not null,
	 start_time		time not null,
	 end_time		time not null,
	 primary key (time_slot_id)
	);

create table course
	(course_id		varchar(20),
	 course_name		varchar(50) not null,
	 credits		numeric(2,0) check (credits > 0),
	 primary key (course_id)
	);

create table section
	(course_id		varchar(20),
	 section_id		varchar(10),
	 semester		varchar(6)
			check (semester in ('Fall', 'Winter', 'Spring', 'Summer')),
	 year			numeric(4,0) check (year > 1990 and year < 2100),
	 instructor_id		varchar(10),
	 classroom_id   	varchar(8),
	 time_slot_id		varchar(8),
	 primary key (course_id, section_id, semester, year),
	 foreign key (course_id) references course (course_id)
		on delete cascade,
	 foreign key (instructor_id) references instructor (instructor_id)
		on delete set null,
	 foreign key (time_slot_id) references time_slot(time_slot_id)
		on delete set null
	);

create table prereq
	(course_id		varchar(20),
	 prereq_id		varchar(20) not null,
	 primary key (course_id, prereq_id),
	 foreign key (course_id) references course (course_id)
		on delete cascade,
	 foreign key (prereq_id) references course (course_id)
	);

create table advise
	(instructor_id		varchar(8),
	 student_id		varchar(10),
	 start_date		date not null,
	 end_date		date,
	 primary key (instructor_id, student_id),
	 foreign key (instructor_id) references instructor (instructor_id)
		on delete  cascade,
	 foreign key (student_id) references PhD (student_id)
		on delete cascade
);

create table TA
	(student_id		varchar(10),
	 course_id		varchar(8),
	 section_id		varchar(10),
	 semester		varchar(6),
	 year			numeric(4,0),
	 primary key (student_id, course_id, section_id, semester, year),
	 foreign key (student_id) references PhD (student_id)
		on delete cascade,
	 foreign key (course_id, section_id, semester, year) references
	     section (course_id, section_id, semester, year)
		on delete cascade
);

create table masterGrader
	(student_id		varchar(10),
	 course_id		varchar(8),
	 section_id		varchar(10),
	 semester		varchar(6),
	 year			numeric(4,0),
	 primary key (student_id, course_id, section_id, semester, year),
	 foreign key (student_id) references master (student_id)
		on delete cascade,
	 foreign key (course_id, section_id, semester, year) references
	     section (course_id, section_id, semester, year)
		on delete cascade
);

create table undergraduateGrader
	(student_id		varchar(10),
	 course_id		varchar(8),
	 section_id		varchar(10),
	 semester		varchar(6),
	 year			numeric(4,0),
	 primary key (student_id, course_id, section_id, semester, year),
	 foreign key (student_id) references undergraduate (student_id)
		on delete cascade,
	 foreign key (course_id, section_id, semester, year) references
	     section (course_id, section_id, semester, year)
		on delete cascade
);

create table take
	(student_id		varchar(10),
	 course_id		varchar(8),
	 section_id		varchar(10),
	 semester		varchar(6),
	 year			numeric(4,0),
	 grade		    	varchar(2)
		check (grade in ('A+', 'A', 'A-','B+', 'B', 'B-','C+', 'C', 'C-','D+', 'D', 'D-','F')),
	 primary key (student_id, course_id, section_id, semester, year),
	 foreign key (course_id, section_id, semester, year) references
	     section (course_id, section_id, semester, year)
		on delete cascade,
	 foreign key (student_id) references student (student_id)
		on delete cascade
	);

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

CREATE TABLE days (
  day_id int(11) NOT NULL,
  day_name enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  primary key(day_id),
  unique key(day_name)
);

CREATE TABLE hours (
  hour_id int(11) NOT NULL,
  hour_label time DEFAULT NULL,
  primary key (hour_id),
  unique key hour_label (hour_label)
);

CREATE TABLE tutor (
  tutor_id int(11) NOT NULL,
  student_id varchar(10) DEFAULT NULL,
  notes text DEFAULT NULL,
  hourly_rate decimal(6,2) DEFAULT NULL,
  is_active tinyint(1) DEFAULT 1,
  primary key(tutor_id)
);

CREATE TABLE tutor_assignments (
  assignment_id int(11) NOT NULL,
  tutor_id int(11) NOT NULL,
  course_id varchar(20) NOT NULL,
  classroom_id varchar(8) NOT NULL,
  year year(4) NOT NULL,
  semester enum('Spring','Summer','Fall','Winter') NOT NULL,
  day_id int(11) NOT NULL,
  start_hour_id int(11) NOT NULL,
  end_hour_id int(11) NOT NULL,
  primary key(assignment_id)
);

INSERT INTO account (email, password, type) VALUES
('student1@uml.edu', 'pass1', 'student'),
('student2@uml.edu', 'pass2', 'student'),
('student3@uml.edu', 'pass3', 'student'),
('student4@uml.edu', 'pass4', 'student'),
('student5@uml.edu', 'pass5', 'student'),
('student6@uml.edu', 'pass6', 'student'),
('student7@uml.edu', 'pass7', 'student'),
('student8@uml.edu', 'pass8', 'student'),
('student9@uml.edu', 'pass9', 'student'),
('student10@uml.edu', 'pass10', 'student'),
('student11@uml.edu', 'pass11', 'student'),
('student12@uml.edu', 'pass12', 'student'),
('student13@uml.edu', 'pass13', 'student'),
('student14@uml.edu', 'pass14', 'student'),
('student15@uml.edu', 'pass15', 'student'),
('instructor1@uml.edu', 'pass16', 'instructor'),
('instructor2@uml.edu', 'pass17', 'instructor'),
('instructor3@uml.edu', 'pass18', 'instructor'),
('tutor1@uml.edu', 'pass19', 'tutor'),
('tutor2@uml.edu', 'pass20', 'tutor'),
('tutor3@uml.edu', 'pass21', 'tutor'),
('tutor4@uml.edu', 'pass22', 'tutor'),
('tutor5@uml.edu', 'pass23', 'tutor'),
('tutor6@uml.edu', 'pass24', 'tutor');

INSERT INTO department (dept_name, location) VALUES
('Miner School of Computer & Information Sciences', 'Dandeneau Hall, 1 University Avenue, Lowell, MA 01854'),
('Department of Mathematics', 'Mathematics Building, 2 University Avenue, Lowell, MA 01854'),
('Department of Physics', 'Physics Building, 3 University Avenue, Lowell, MA 01854');

INSERT INTO instructor (instructor_id, instructor_name, title, dept_name, email) VALUES
('1', 'David Adams', 'Teaching Professor', 'Miner School of Computer & Information Sciences', 'dbadams@cs.uml.edu'),
('2', 'Sirong Lin', 'Associate Teaching Professor', 'Miner School of Computer & Information Sciences', 'slin@cs.uml.edu'),
('3', 'Yelena Rykalova', 'Associate Teaching Professor', 'Miner School of Computer & Information Sciences', 'Yelena_Rykalova@uml.edu'),
('4', 'Johannes Weis', 'Assistant Teaching Professor', 'Miner School of Computer & Information Sciences', 'Johannes_Weis@uml.edu'),
('5', 'Tom Wilkes', 'Assistant Teaching Professor', 'Miner School of Computer & Information Sciences', 'Charles_Wilkes@uml.edu');

INSERT INTO student (student_id, name, email, dept_name) VALUES
('student1', 'Alice', 'student1@uml.edu', 'Department of Mathematics'),
('student2', 'Bob', 'student2@uml.edu', 'Department of Physics'),
('student3', 'Charlie', 'student3@uml.edu', 'Miner School of Computer & Information Sciences'),
('student4', 'David', 'student4@uml.edu', 'Department of Mathematics'),
('student5', 'Eve', 'student5@uml.edu', 'Department of Physics'),
('student6', 'Frank', 'student6@uml.edu', 'Miner School of Computer & Information Sciences'),
('student7', 'Grace', 'student7@uml.edu', 'Department of Mathematics'),
('student8', 'Heidi', 'student8@uml.edu', 'Department of Physics'),
('student9', 'Ivan', 'student9@uml.edu', 'Miner School of Computer & Information Sciences'),
('student10', 'Judy', 'student10@uml.edu', 'Miner School of Computer & Information Sciences'),
('student11', 'Mallory', 'student11@uml.edu', 'Miner School of Computer & Information Sciences'),
('student12', 'Niaj', 'student12@uml.edu', 'Miner School of Computer & Information Sciences'),
('student13', 'Olivia', 'student13@uml.edu', 'Miner School of Computer & Information Sciences'),
('student14', 'Peggy', 'student14@uml.edu', 'Miner School of Computer & Information Sciences'),
('student15', 'Sybil', 'student15@uml.edu', 'Miner School of Computer & Information Sciences');

INSERT INTO undergraduate (student_id, total_credits, class_standing) VALUES
('student4', 6, 'Junior'),
('student5', 7, 'Senior'),
('student6', 4, 'Sophomore'),
('student13', 3, 'Junior'),
('student14', 0, 'Senior'),
('student15', 3, 'Sophomore');

INSERT INTO master (student_id, total_credits) VALUES
('student1', 30),
('student2', 36),
('student3', 40),
('student4', 32),
('student5', 38),
('student6', 42);

INSERT INTO phd (student_id, qualifier, proposal_defence_date, dissertation_defence_date) VALUES
('student7', 'Qualifier1', '2025-05-01', '2025-12-01'),
('student8', 'Qualifier2', '2025-06-01', '2025-11-01'),
('student9', 'Qualifier3', '2025-07-01', '2025-12-15'),
('student10', 'Qualifier4', '2025-08-01', '2025-11-30'),
('student11', 'Qualifier5', '2025-09-01', '2025-12-20'),
('student12', 'Qualifier6', '2025-10-01', '2025-12-25');

INSERT INTO classroom (classroom_id, building, room_number, capacity) VALUES
('1', 'Ball Hall', '203', 35),
('2', 'Ball Hall', '302', 30),
('3', 'Ball Hall', '303', 40),
('4', 'Ball Hall', '304', 50),
('5', 'Ball Hall', '209', 25),
('6', 'Ball Hall', '201', 45),
('11', 'Olsen Hall', '110', 20),
('12', 'Olsen Hall', '101', 30),
('13', 'Olsen Hall', '102', 35),
('14', 'Olsen Hall', '103', 40),
('15', 'Olsen Hall', '104', 45),
('16', 'Olsen Hall', '105', 50),
('17', 'Southwick Hall', '201', 25),
('18', 'Southwick Hall', '202', 30),
('19', 'Southwick Hall', '203', 35),
('20', 'Southwick Hall', '204', 40),
('21', 'Southwick Hall', '205', 45),
('22', 'Shah Hall', '301', 30),
('23', 'Shah Hall', '302', 35),
('24', 'Shah Hall', '303', 40),
('25', 'Shah Hall', '304', 45),
('26', 'Shah Hall', '305', 50),
('27', 'Falmouth Hall', '301', 25),
('28', 'Falmouth Hall', '302', 30),
('29', 'Falmouth Hall', '303', 35),
('30', 'Falmouth Hall', '304', 40),
('31', 'Falmouth Hall', '305', 45),
('32', 'Falmouth Hall', '309', 23),
('50', 'Tutoring Center', '101', 20),
('51', 'Tutoring Center', '102', 25),
('52', 'Tutoring Center', '103', 30),
('53', 'Tutoring Center', '104', 35),
('54', 'Tutoring Center', '105', 40),
('55', 'Tutoring Center', '106', 22),
('56', 'Tutoring Center', '107', 18),
('57', 'Tutoring Center', '108', 28),
('58', 'Tutoring Center', '109', 26),
('59', 'Tutoring Center', '110', 32);

INSERT INTO time_slot (time_slot_id, day, start_time, end_time) VALUES
('TS1', 'MoWeFr', '11:00:00', '11:50:00'),
('TS2', 'MoWeFr', '12:00:00', '12:50:00'),
('TS3', 'MoWeFr', '13:00:00', '13:50:00'),
('TS4', 'TuTh', '11:00:00', '12:15:00'),
('TS5', 'TuTh', '12:30:00', '13:45:00');

INSERT INTO course (course_id, course_name, credits) VALUES
('COMP1010', 'Computing I', 3),
('COMP1020', 'Computing II', 3),
('COMP2010', 'Computing III', 3),
('COMP2040', 'Computing IV', 3);

INSERT INTO section (course_id, section_id, semester, year, instructor_id, classroom_id, time_slot_id) VALUES
('COMP1010', 'Section109', 'Fall', 2024, '5', '5', 'TS5'),
('COMP1010', 'Section111', 'Fall', 2024, '4', '9', 'TS4'),
('COMP1020', 'Section110', 'Spring', 2025, '1', '6', 'TS1'),
('COMP2010', 'Section202', 'Fall', 2024, '3', '3', 'TS3'),
('COMP2010', 'Section203', 'Fall', 2024, '2', '7', 'TS2'),
('COMP2040', 'Section402', 'Spring', 2025, '4', '4', 'TS4'),
('COMP2040', 'Section403', 'Spring', 2025, '3', '8', 'TS3');

INSERT INTO prereq (course_id, prereq_id) VALUES
('COMP1020', 'COMP1010'),
('COMP2010', 'COMP1010'),
('COMP2010', 'COMP1020'),
('COMP2040', 'COMP1010'),
('COMP2040', 'COMP1020'),
('COMP2040', 'COMP2010');

INSERT INTO advise (instructor_id, student_id, start_date, end_date) VALUES
('1', 'student7', '2024-01-01', NULL),
('2', 'student8', '2024-02-01', NULL),
('3', 'student9', '2024-03-01', NULL),
('4', 'student10', '2024-04-01', NULL),
('5', 'student11', '2024-05-01', NULL),
('1', 'student12', '2024-06-01', NULL),
('2', 'student7', '2024-07-01', NULL),
('3', 'student8', '2024-08-01', NULL),
('4', 'student9', '2024-09-01', NULL);

INSERT INTO ta (student_id, course_id, section_id, semester, year) VALUES
('student7', 'COMP1010', 'Section109', 'Fall', 2024),
('student9', 'COMP2010', 'Section202', 'Fall', 2024),
('student10', 'COMP2040', 'Section402', 'Spring', 2025),
('student11', 'COMP1010', 'Section109', 'Fall', 2024),
('student12', 'COMP1020', 'Section110', 'Spring', 2025);

INSERT INTO mastergrader (student_id, course_id, section_id, semester, year) VALUES
('student1', 'COMP1010', 'Section109', 'Fall', 2024),
('student2', 'COMP1020', 'Section110', 'Spring', 2025),
('student3', 'COMP2010', 'Section202', 'Fall', 2024),
('student4', 'COMP2040', 'Section402', 'Spring', 2025),
('student5', 'COMP1010', 'Section111', 'Fall', 2024),
('student6', 'COMP1020', 'Section110', 'Spring', 2025);

INSERT INTO undergraduategrader (student_id, course_id, section_id, semester, year) VALUES
('student4', 'COMP2010', 'Section202', 'Fall', 2024),
('student5', 'COMP2040', 'Section402', 'Spring', 2025),
('student6', 'COMP1010', 'Section109', 'Fall', 2024),
('student13', 'COMP1020', 'Section110', 'Spring', 2025),
('student14', 'COMP2010', 'Section203', 'Fall', 2024),
('student15', 'COMP2040', 'Section403', 'Spring', 2025);

INSERT INTO take (student_id, course_id, section_id, semester, year, grade) VALUES
('student4', 'COMP2040', 'Section402', 'Spring', 2025, 'A'),
('student5', 'COMP1010', 'Section109', 'Fall', 2024, 'A-'),
('student7', 'COMP2010', 'Section203', 'Fall', 2024, 'A-'),
('student8', 'COMP2040', 'Section403', 'Spring', 2025, 'A'),
('student9', 'COMP1010', 'Section109', 'Fall', 2024, 'A-'),
('student10', 'COMP1020', 'Section110', 'Spring', 2025, 'A'),
('student11', 'COMP2010', 'Section202', 'Fall', 2024, 'A-'),
('student12', 'COMP2040', 'Section402', 'Spring', 2025, 'A'),
('student13', 'COMP1010', 'Section111', 'Fall', 2024, 'A-'),
('student15', 'COMP2010', 'Section203', 'Fall', 2024, 'A-');

INSERT INTO club (name, club_id, advisor_id, president_id) VALUES
('Math Club', 'CLUB001', '1', 'student1'),
('Science Club', 'CLUB002', '2', 'student2'),
('Chess Club', 'CLUB003', '3', 'student3'),
('Debate Club', 'CLUB004', '4', 'student4'),
('Art Club', 'CLUB005', '5', 'student5'),
('Music Club', 'CLUB006', '1', 'student6');

INSERT INTO clubparticipants (student_id, club_id) VALUES
('student1', 'CLUB001'),
('student2', 'CLUB002'),
('student3', 'CLUB001'),
('student4', 'CLUB002'),
('student5', 'CLUB001'),
('student6', 'CLUB003'),
('student7', 'CLUB004'),
('student8', 'CLUB003'),
('student9', 'CLUB004'),
('student10', 'CLUB005'),
('student11', 'CLUB006'),
('student12', 'CLUB005'),
('student13', 'CLUB006'),
('student14', 'CLUB003'),
('student15', 'CLUB004');

INSERT INTO days (day_id, day_name) VALUES
(1, 'Monday'),
(2, 'Tuesday'),
(3, 'Wednesday'),
(4, 'Thursday'),
(5, 'Friday'),
(6, 'Saturday'),
(7, 'Sunday');

INSERT INTO hours (hour_id, hour_label) VALUES
(72, '00:00:00'),
(73, '00:30:00'),
(74, '01:00:00'),
(75, '01:30:00'),
(76, '02:00:00'),
(77, '02:30:00'),
(78, '03:00:00'),
(79, '03:30:00'),
(80, '04:00:00'),
(81, '04:30:00'),
(82, '05:00:00'),
(83, '05:30:00'),
(84, '06:00:00'),
(85, '06:30:00'),
(86, '07:00:00'),
(87, '07:30:00'),
(88, '08:00:00'),
(89, '08:30:00'),
(90, '09:00:00'),
(91, '09:30:00'),
(92, '10:00:00'),
(93, '10:30:00'),
(94, '11:00:00'),
(95, '11:30:00'),
(96, '12:00:00'),
(97, '12:30:00'),
(98, '13:00:00'),
(99, '13:30:00'),
(100, '14:00:00'),
(101, '14:30:00'),
(102, '15:00:00'),
(103, '15:30:00'),
(104, '16:00:00'),
(105, '16:30:00'),
(106, '17:00:00'),
(107, '17:30:00'),
(108, '18:00:00'),
(109, '18:30:00'),
(110, '19:00:00'),
(111, '19:30:00'),
(112, '20:00:00'),
(113, '20:30:00'),
(114, '21:00:00'),
(115, '21:30:00'),
(116, '22:00:00'),
(117, '22:30:00'),
(118, '23:00:00'),
(119, '23:30:00');

INSERT INTO tutor (tutor_id, student_id, notes, hourly_rate, is_active) VALUES
(1, 'student1', 'Good tutor.', 20.00, 1),
(2, 'student2', 'Cool tutor.', 25.00, 1),
(3, 'student3', 'Funny tutor.', 30.00, 1),
(4, 'student4', 'Im a tutor.', 22.00, 1),
(5, 'student5', 'Hello, I am the good cool funny tutor.', 24.00, 1),
(6, 'student6', 'Example note', 26.00, 1);

INSERT INTO tutor_assignments (assignment_id, tutor_id, course_id, classroom_id, year, semester, day_id, start_hour_id, end_hour_id) VALUES
(1, 1, 'COMP1010', '1', 2025, 'Fall', 1, 90, 92),
(2, 2, 'COMP1020', '2', 2025, 'Spring', 2, 94, 96),
(3, 3, 'COMP2010', '3', 2025, 'Fall', 3, 98, 100),
(4, 4, 'COMP2040', '4', 2025, 'Spring', 4, 102, 104),
(5, 5, 'COMP1010', '5', 2025, 'Fall', 5, 106, 108),
(6, 6, 'COMP1020', '6', 2025, 'Spring', 6, 110, 112);
