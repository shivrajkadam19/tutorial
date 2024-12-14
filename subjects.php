<!DOCTYPE html>
<html lang="en">

<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  echo "<script>" . "window.location.href='auth-login.php';" . "</script>";
  exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Admin Dashboard - Subjects</title>
    <link rel="stylesheet" href="assets/css/app.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel='shortcut icon' type='image/x-icon' href='assets/img/favicon.ico' />
</head>

<body>
    <div class="loader"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <?php include './partial/header.php' ?>
            <?php include './partial/sidebar.php'; ?>

            <div class="main-content">
                <section class="section">
                    <div class="section-body">

                        <!-- Form to Add New Subject -->
                        <h2>Add New Subject</h2>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Subject Form</h4>
                                    </div>
                                    <div class="card-body">
                                        <form id="addSubjectForm">
                                            <div class="form-group">
                                                <label for="subjectName">Subject Name</label>
                                                <input type="text" id="subjectName" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="courseName">Course</label>
                                                <select id="courseName" class="form-control" required>
                                                    <!-- Dynamic Course Options -->
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Subject</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DataTable for Subjects -->
                        <h2>Subjects</h2>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Subject List</h4>
                                    </div>
                                    <div class="card-body">
                                        <table id="subjectsTable" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Subject Name</th>
                                                    <th>Course Name</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Dynamic content will go here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <?php include './partial/site-settings.php' ?>
            </div>
            <?php include './partial/footer.php'; ?>
        </div>
    </div>

    <script src="assets/js/app.min.js"></script>
    <script src="assets/js/scripts.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/bundles/jquery/jquery.min.js"></script>
    <script src="assets/bundles/datatables/datatables.min.js"></script>
    <script src="assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/bundles/sweetalert/sweetalert.min.js"></script>

    <script>
        $(document).ready(function() {
            // Populate Course Dropdown
            $.ajax({
                url: 'http://localhost/tutorial/admin/api/api-get-courses.php',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var courses = response.courses;
                        var courseSelect = $('#courseName');

                        // Populate dropdown with CourseID as value and CourseName as display
                        courses.forEach(function(course) {
                            courseSelect.append(`<option value="${course.CourseID}">${course.CourseName}</option>`);
                        });
                    } else {
                        console.error('Failed to load courses');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });

            // Initialize DataTable
            var table = $('#subjectsTable').DataTable({
                "ajax": {
                    "url": "http://localhost/tutorial/admin/api/api-get-subjects.php",
                    "dataSrc": function(json) {
                        if (json.success) {
                            return json.subjects;
                        } else {
                            console.error(json.message);
                            return [];
                        }
                    }
                },
                "columns": [{
                        "data": "SubjectName"
                    },
                    {
                        "data": "CourseName"
                    },
                    {
                        "data": null,
                        "defaultContent": "<button class='btn btn-danger delete'>Delete</button>"
                    }
                ]
            });


            // Handle Add Subject Form Submission
            $('#addSubjectForm').on('submit', function(e) {
                e.preventDefault();

                var subjectName = $('#subjectName').val();
                var courseId = $('#courseName').val();

                $.ajax({
                    url: 'http://localhost/tutorial/admin/api/api-add-subjects.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        subjectName: subjectName,
                        courseId: courseId
                    }),
                    success: function(response) {
                        if (response.success) {
                            swal({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                button: "OK"
                            });
                            table.ajax.reload();
                            $('#addSubjectForm')[0].reset();
                        } else {
                            swal({
                                title: "Error!",
                                text: response.message || 'An error occurred.',
                                icon: "error",
                                button: "OK"
                            });
                        }
                    },
                    error: function(xhr) {
                        swal({
                            title: "Error!",
                            text: "Failed to add subject. Please try again.",
                            icon: "error",
                            button: "OK"
                        });
                        console.error(xhr.responseText);
                    }
                });
            });

            // Handle Delete Subject
            $('#subjectsTable').on('click', '.delete', function() {
                var row = $(this).closest('tr');
                var subjectName = row.find('td:eq(0)').text();

                swal({
                    title: "Are you sure?",
                    text: `You are about to delete the subject: ${subjectName}`,
                    icon: "warning",
                    buttons: ["Cancel", "Yes, Delete"],
                    dangerMode: true
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: 'http://localhost/tutorial/admin/api/api-delete-subjects.php',
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                subjectName: subjectName
                            }),
                            success: function(response) {
                                if (response.success) {
                                    swal({
                                        title: "Deleted!",
                                        text: "The subject has been deleted.",
                                        icon: "success",
                                        button: "OK"
                                    });
                                    table.ajax.reload();
                                } else {
                                    swal({
                                        title: "Error!",
                                        text: response.message || 'Failed to delete subject.',
                                        icon: "error",
                                        button: "OK"
                                    });
                                }
                            },
                            error: function(xhr) {
                                swal({
                                    title: "Error!",
                                    text: "Failed to delete subject. Please try again.",
                                    icon: "error",
                                    button: "OK"
                                });
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });
        });
    </script>


</body>

</html>