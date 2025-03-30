<?php include 'item2.php'; ?>
<?php include 'item3.php'; ?>
<?php include 'item10_assign_tutor.php'; ?>
<html>

<head>
  <title>DB2 Group 1 Phase 2</title>
</head>

<body>
  <h1>University Database</h1>

  <!--  1. A student can create an account and modify their information later. -->
  <h3>Create or modify a student account</h3>
  <form action="item1.php" method="post">
    <table border="0">
      <tr bgcolor="#cccccc">
        <td>Student ID:</td>
        <td align="left">
          <input type="text" name="student_id" size="32" maxlength="32" />
        </td>
      </tr>
      <tr bgcolor="#cccccc">
        <td>Email:</td>
        <td align="left">
          <input type="text" name="email" size="32" maxlength="32" />
        </td>
      </tr>
      <tr bgcolor="#cccccc">
        <td>Password:</td>
        <td align="left">
          <input type="text" name="password" size="32" maxlength="32" />
        </td>
      </tr>
      <tr bgcolor="#cccccc">
        <td>Name:</td>
        <td align="left">
          <input type="text" name="name" size="32" maxlength="32" />
        </td>
      </tr>
      <tr bgcolor="#cccccc">
        <td>Degree:</td>
        <td align="left">
          <select name="degree">
            <option value="undergraduate">Undergraduate</option>
            <option value="master">Master</option>
            <option value="phd">PhD</option>
          </select>
        </td>
      </tr>
      <tr bgcolor="#cccccc">
        <td>Deptartment:</td>
        <td align="left">
          <select name="dept">
            <option value="Miner School of Computer & Information Sciences">
              Computer Science
            </option>
          </select>
        </td>
      </tr>
      <tr bgcolor="#cccccc">
        <td colspan="2" align="center">
          <input type="submit" value="Create or Modify Account" />
        </td>
      </tr>
    </table>
  </form>
  <!--  2. The admin will be able to create a new course section and appoint instructor to teach the
        section. Every course section is scheduled to meet at a specific time slot, with a limit of
        two sections per time slot. Each instructor teaches one or two sections per semester.
        Should an instructor be assigned two sections, the two sections must be scheduled in
        consecutive time slots. -->
  <h3>Create A new course section</h3>
  <form action="item2.php" method="POST">
    <table border="0">
      <tr>
        <td bgcolor="#cccccc"><label for="course">Course:</label></td>
        <td bgcolor="#cccccc">
          <select name="course" id="course" required>
            <?php echo $course_names; ?>
          </select>
        </td>
      </tr>

      <tr>
        <td bgcolor="#cccccc"><label for="section">Section:</label></td>
        <td bgcolor="#cccccc">
          <input type="number" name="section" id="section" required />
        </td>
      </tr>

      <tr>
        <td bgcolor="#cccccc"><label for="semester">Semester:</label></td>
        <td bgcolor="#cccccc">
          <select name="semester" id="semester" required>
            <option value="Fall">Fall</option>
            <option value="Spring">Spring</option>
          </select>
        </td>
      </tr>

      <tr>
        <td bgcolor="#cccccc"><label for="year">Year:</label></td>
        <td bgcolor="#cccccc">
          <input type="number" name="year" id="year" min="2025" required />
        </td>
      </tr>

      <tr>
        <td bgcolor="#cccccc">
          <label for="instructor_id">Instructor:</label>
        </td>
        <td bgcolor="#cccccc">
          <select name="instructor" id="instructor">
            <?php echo $instructor_names; ?>
          </select>
        </td>
      </tr>



      <tr>


        <td bgcolor="#cccccc">
          <label for="classroom">Classroom:</label>
        </td>
        <td bgcolor="#cccccc">
          <select name="classroom" id="classroom">
            <?php echo $classrooms; ?>
          </select>
        </td>


      </tr>

      <tr>

        <td bgcolor="#cccccc">
          <label for="timeslot">Timeslot:</label>
        </td>

        <td bgcolor="#cccccc">
          <select name="timeslot" id="timeslot">
            <?php echo $timeslots; ?>
          </select>
        </td>


      </tr>



      <tr>
        <td bgcolor="#cccccc">
          <button type="submit">Create Section</button>
        </td>
      </tr>
      </tr>
    </table>
  </form>

  <!--  3. A student can browse all the courses offered in the current semester and can register for
        a specific section of a course if they satisfy the prerequisite conditions and there is
        available space in the section. (Assume each section is limited to 15 students). -->
  <h3>Register for a section</h3>
  <form action="item3.php" method="post">
    <table border="0">
      <tr>
        <td align="left" bgcolor="#cccccc"><label for="year">Year:</label></td>
        <td>
          <?= $current_year ?>
        </?=>
      </tr>
      <tr>
        <td align="left" bgcolor="#cccccc"><label for="semester">Semester:</label></td>
        <td>
          <?= $current_semester ?>
        </td>
      </tr>

      <tr>
        <td bgcolor="#cccccc"><label for="student_id">Student ID:</label></td>
        <td bgcolor="#cccccc">
          <input name="student_id" id="student_id" required />
        </td>
      </tr>


      <tr>
        <td bgcolor="#cccccc">
          <label for="sections">Sections:</label>
        </td>
        <td>
          <select name="sections" id="sections" required>
            <?php echo $sections; ?>
          </select>
        </td>
      </tr>

      <tr>
        <td bgcolor="#cccccc">
          <button type="submit">Register</button>
        </td>
      </tr>

    </table>
  </form>


  <!--  4. A student can view a list of all courses they have taken and are currently taking, along
        with the total number of credits earned and their cumulative GPA. -->
  <h3>View courses</h3>
  <form action="item4.php" method="post">
    <table border="0">
      <tr bgcolor="#cccccc">
        <td>Student ID:</td>
        <td align="left">
          <input type="text" name="student_id" size="32" maxlength="32" />
        </td>
      </tr>
      <tr bgcolor="#cccccc">
        <td colspan="2" align="center">
          <input type="submit" value="View courses" />
        </td>
      </tr>
    </table>
  </form>

    <!--  5. Instructors have access to records of all course sections they have taught, including
        names of current semester's enrolled students and the names and grades of students
        from past semesters. -->

        <h3>View Instructor Records</h3>
    <form action="item5.php" method="post">
        <table border="0">
            <tr bgcolor="#cccccc">
                <td>Instructor ID:</td>
                <td align="left">
                    <input type="text" name="instructor_id" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>Password:</td>
                <td align="left">
                    <input type="text" name="password" size="32" maxlength="32" />
                </td>
            </tr>
            <tr bgcolor="#cccccc">
                <td colspan="2" align="center">
                    <input type="submit" value="View instructor records" />
                </td>
            </tr>
        </table>
    </form>

  <!--  6. Teaching Assistants (TAs), who are PhD students, will be assigned by the admin to
        sections with more than 10 students. A PhD student is eligible to be a TA for only one
        section. -->

    <h3>Add PhD Student as TA</h3>
    <form action="item6.php" method="post">
        <table border="0">
            <tr bgcolor="#cccccc">
                <td>Admin Email:</td>
                <td align="left">
                    <input type="text" name="email" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>Password:</td>
                <td align="left">
                    <input type="text" name="password" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>Student ID:</td>
                <td align="left">
                    <input type="text" name="student_id" size="32" maxlength="32" />
                </td>
            </tr>

            <tr>
                <td align="left" bgcolor="#cccccc">
                    <label for="year">Year:</label></td>
                <td><?= $this_year ?></td>
            </tr>

            <tr>
                <td align="left" bgcolor="#cccccc">
                    <label for="semester">Semester:</label></td>
                <td><?= $this_semester ?></td>
            </tr>

            <tr>
                <td bgcolor="#cccccc">
                    <label for="sections">Sections:</label>
                </td>
                <td>
                    <select name="section" id="section" required>
                        <?php echo $these_sections; ?>
                    </select>
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td colspan="2" align="center">
                    <input type="submit" value="Add TA"/>
                </td>
            </tr>
        </table>
    </form>

    <!--  7. Grader positions for sections with 5 to 10 students will be assigned by the admin with
        either MS students or undergraduate students who have got A- or A in the course. If
        there are more than one qualified candidates, the admin will choose one as the grader.
        A student may serve as a grader for only one section. -->

    <h3>Add Masters or Undergraduate Student as Grader</h3>
    <form action="item7.php" method="post">
        <table border="0">
            <tr bgcolor="#cccccc">
                <td>Admin Email:</td>
                <td align="left">
                    <input type="text" name="email" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>Password:</td>
                <td align="left">
                    <input type="text" name="password" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>Student ID:</td>
                <td align="left">
                    <input type="text" name="student_id" size="32" maxlength="32" />
                </td>
            </tr>

            <tr>
                <td align="left" bgcolor="#cccccc">
                    <label for="year">Year:</label></td>
                <td><?= $this_year ?></td>
            </tr>

            <tr>
                <td align="left" bgcolor="#cccccc">
                    <label for="semester">Semester:</label></td>
                <td><?= $this_semester ?></td>
            </tr>

            <tr>
                <td bgcolor="#cccccc">
                    <label for="sections">Sections:</label>
                </td>
                <td>
                    <select name="section" id="section" required>
                        <?php echo $these_sections; ?>
                    </select>
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td colspan="2" align="center">
                    <input type="submit" value="Add Grader"/>
                </td>
            </tr>
        </table>
    </form>

    <!--  8. The admin or instructor can appoint one or two instructors as advisor(s) for PhD
        students, including a start date, and optional end date. The advisor will be able to view
        the course history of their advisees, and update their advisees’ information. -->

    <h3>Add Advisor(s) to PhD Student</h3>
    <form action="item8.php" method="post">
        <table border="0">
            <tr bgcolor="#cccccc">
                <td>Admin/Instructor Email:</td>
                <td align="left">
                    <input type="text" name="email" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>Password:</td>
                <td align="left">
                    <input type="text" name="password" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>Student ID:</td>
                <td align="left">
                    <input type="text" name="student_id" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>Instructor ID:</td>
                <td align="left">
                    <input type="text" name="instructor_id" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>(Optional) 2nd Instructor ID:</td>
                <td align="left">
                    <input type="text" name="instructor_id_2" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>Start Date:</td>
                <td align="left">
                    <input type="date" name="start_date" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>(Optional) End Date:</td>
                <td align="left">
                    <input type="date" name="end_date" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td colspan="2" align="center">
                    <input type="submit" value="Add Advisor(s)"/>
                </td>
            </tr>
        </table>
    </form>

    <h3>View Advisee Course History</h3>
    <form action="item8view.php" method="post">
        <table border="0">
            <tr bgcolor="#cccccc">
                <td>Advisee Email:</td>
                <td align="left">
                    <input type="text" name="email" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>Password:</td>
                <td align="left">
                    <input type="text" name="password" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td>Student ID:</td>
                <td align="left">
                    <input type="text" name="student_id" size="32" maxlength="32" />
                </td>
            </tr>

            <tr bgcolor="#cccccc">
                <td colspan="2" align="center">
                    <input type="submit" value="View Info"/>
                </td>
            </tr>
        </table>
    </form>

    <!--  9. Student-proposed functionality #1 - Club stuff -->
        <!--  9. Student-proposed functionality #1 - Club stuff -->
    <h3>Create a Club</h3>
    <form action="item9create.php" method="post">
        <table border="0">
            <tr bgcolor="#cccccc">
                <td>Advisor's Instructor ID:</td>
                <td align="left">
                    <input type="text" name="instructor_id" size="32" maxlength="32" />
                </td>
            </tr>
            <tr bgcolor="#cccccc">
                <td>Password:</td>
                <td align="left">
                    <input type="text" name="password" size="32" maxlength="32" />
                </td>
            </tr>
            <tr bgcolor="#cccccc">
                <td>Club Name:</td>
                <td align="left">
                    <input type="text" name="club_name" size="32" maxlength="32" />
                </td>
            </tr>
            <tr bgcolor="#cccccc">
                <td>President's Student ID:</td>
                <td align="left">
                    <input type="text" name="president_id" size="32" maxlength="32" />
                </td>
            </tr>
            <tr bgcolor="#cccccc">
                <td colspan="2" align="center">
                    <input type="submit" value="Create Club" />
                </td>
            </tr>
        </table>
    </form>

    <h3>Join a Club</h3>
    <form action="item9join.php" method="post">
        <table border="0">
            <tr bgcolor="#cccccc">
                <td>Club Name:</td>
                <td align="left">
                    <input type="text" name="club_name" size="32" maxlength="32" />
                </td>
            </tr>
            <tr bgcolor="#cccccc">
                <td>Student ID:</td>
                <td align="left">
                    <input type="text" name="student_id" size="32" maxlength="32" />
                </td>
            </tr>
            <tr bgcolor="#cccccc">
                <td>Password:</td>
                <td align="left">
                    <input type="text" name="password" size="32" maxlength="32" />
                </td>
            </tr>
            <tr bgcolor="#cccccc">
                <td colspan="2" align="center">
                    <input type="submit" value="Join Club" />
                </td>
            </tr>
        </table>
    </form>

    <h3>Leave a Club</h3>
    <form action="item9leave.php" method="post">
        <table border="0">
            <tr bgcolor="#cccccc">
                <td>Club Name:</td>
                <td align="left">
                    <input type="text" name="club_name" size="32" maxlength="32" />
                </td>
            </tr>
            <tr bgcolor="#cccccc">
                <td>Student ID:</td>
                <td align="left">
                    <input type="text" name="student_id" size="32" maxlength="32" />
                </td>
            </tr>
            <tr bgcolor="#cccccc">
                <td>Password:</td>
                <td align="left">
                    <input type="text" name="password" size="32" maxlength="32" />
                </td>
            </tr>
            <tr bgcolor="#cccccc">
                <td colspan="2" align="center">
                    <input type="submit" value="Leave Club" />
                </td>
            </tr>
        </table>
    </form>

  <!--  10. Student-proposed functionality #2 (Tutoring) -->

  <!-- Add tutor -->
  <h3>Add Tutor</h3>
  <form action="item10_add_tutor.php" method="post">
    <table border="0">
      <tr>
        <td bgcolor="#cccccc"><label for="student_id">Student ID:</label></td>
        <td bgcolor="#cccccc">
          <input name="student_id" id="student_id" required />
        </td>
      </tr>

      <tr>
        <td bgcolor="#cccccc"><label for="hourly_rate">Hourly Rate:</label></td>
        <td bgcolor="#cccccc">
          <input type="number" name="hourly_rate" id="hourly_rate" step="0.01" min="1" />

        </td>
      </tr>


      <tr>
        <td bgcolor="#cccccc"><label for="notes">Notes:</label></td>
        <td bgcolor="#cccccc">
          <textarea name="notes" id="notes" rows="4" cols="40"></textarea>
        </td>
      </tr>

      <tr>
        <td bgcolor="#cccccc"><label for="is_active">Is Active?</label></td>
        <td bgcolor="#cccccc">
          <select name="is_active" id="is_active">
            <option value="1" selected>Yes</option>
            <option value="0" selected>No</option>
          </select>
        </td>
      </tr>

      <tr bgcolor="#cccccc">
        <td colspan="2">
          <input type="submit" value="Save" />
        </td>
      </tr>
    </table>
  </form>

  <!-- Assign tutor -->
    <!-- After adding a new tutor,the admin can now assign hours and update hours for tutors based on multiple checks
    The user can save a tutor assignment (insert or update) based on the following : 
    Is the tutor active?
    Did the tutor get a good grade (A- or better )in the course?
    Is there any time conflict with tutor classes?
    Is the tutor working more than the allowed weekly hours (Assumed to be 22 hours in this case)?
    Does the new time overlap with another tutoring session?
    If one of the requirments above are not met the user cannot assign new hours or update  -->
   <h3>Assign Tutor</h3>
   <form action="item10_assign_tutor.php" method="post">
    <table border="0">
    <tr>
        <td bgcolor="#cccccc"><label for="tutor_names">Tutor:</label></td>
        <td bgcolor="#cccccc">
        <select name="tutor_names" id="tutor_names" required>
            <?php echo $tutor_names; ?>
          </select>
        </td>
      </tr>

      <tr>
        <td bgcolor="#cccccc"><label for="course_names">Course:</label></td>
        <td bgcolor="#cccccc">
        <select name="course_names" id="course_names" required>
            <?php echo $course_names; ?>
          </select>
        </td>
      </tr>

      <tr>
        <td bgcolor="#cccccc"><label for="locations">Location:</label></td>
        <td bgcolor="#cccccc">
        <select name="locations" id="locations" required>
            <?php echo $locations; ?>
          </select>
        </td>
      </tr>

      <tr>
        <td bgcolor="#cccccc"><label for="year">Year:</label></td>
        <td bgcolor="#cccccc">
        <input type="number" name="year" id="year">
           
          </input>
        </td>
      </tr>

      <tr>
  <td bgcolor="#cccccc"><label for="semester">Semester:</label></td>
  <td bgcolor="#cccccc">
    <select name="semester" id="semester" required>
      <option value="">-- Select Semester --</option>
      <option value="Spring">Spring</option>
      <option value="Fall">Fall</option>
    </select>
  </td>

  <tr>
        <td bgcolor="#cccccc"><label for="days">Day:</label></td>
        <td bgcolor="#cccccc">
        <select name="days" id="days" required>
            <?php echo $days; ?>
          </select>
        </td>
  </tr>

  <tr>
        <td bgcolor="#cccccc"><label for="start_times">Start Time :</label></td>
        <td bgcolor="#cccccc">
        <select name="start_times" id="start_times" required>
            <?php echo $start_times; ?>
          </select>
        </td>
  </tr>

  <tr>
        <td bgcolor="#cccccc"><label for="end_times">End Time :</label></td>
        <td bgcolor="#cccccc">
        <select name="end_times" id="end_times" required>
            <?php echo $end_times; ?>
          </select>
        </td>
  </tr>

  <tr bgcolor="#cccccc">
        <td colspan="2">
          <input type="submit" value="Save" />
        </td>
    </tr>


      
    </table>
   </form>

</body>

</html>