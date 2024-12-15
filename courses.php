<!DOCTYPE html>
<html lang="en">

<?php
session_start();
include './partial/key.php';
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "<script>window.location.href='auth-login.php';</script>";
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Otika - Admin Dashboard Template</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="assets/css/app.min.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel='shortcut icon' type='image/x-icon' href='assets/img/favicon.ico' />
</head>

<body>
    <div class="loader"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <?php include './partial/header.php' ?>
            <?php include './partial/sidebar.php'; ?>
            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-body">
                        <!-- Form to Add New Course -->
                        <h2>Add New Course</h2>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Course Form</h4>
                                    </div>
                                    <div class="card-body">
                                        <form id="addCourseForm">
                                            <div class="form-group">
                                                <label for="courseName">Course Name</label>
                                                <input type="text" id="courseName" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea id="description" class="form-control" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Course</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- DataTable for Courses -->
                        <h2>Courses</h2>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Course List</h4>
                                    </div>
                                    <div class="card-body">
                                        <table id="coursesTable" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Course Name</th>
                                                    <th>Description</th>
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
    <!-- General JS Scripts -->
    <script src="assets/js/app.min.js"></script>
    <!-- JS Libraies -->
    <!-- Page Specific JS File -->
    <!-- Template JS File -->
    <script src="assets/js/scripts.js"></script>
    <!-- Custom JS File -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/bundles/jquery/jquery.min.js"></script>
    <script src="assets/bundles/datatables/datatables.min.js"></script>
    <script src="assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/bundles/sweetalert/sweetalert.min.js"></script>

    <script>
        $(document).ready(function() {
            const baseUrl = '<?php echo $url; ?>';
            // Initialize DataTable
            var table = $('#coursesTable').DataTable({
                "ajax": {
                    "url": "http://localhost/tutorial/admin/api/api-get-courses.php",
                    "dataSrc": function(json) {
                        if (json.success) {
                            return json.courses;
                        } else {
                            console.error(json.message);
                            return [];
                        }
                    }
                },
                "columns": [
                    { "data": "CourseName" },
                    { "data": "Description" },
                    { "data": "CourseID", 
                      "render": function(data, type, row) {
                          return `<button class='btn btn-danger delete' data-id='${data}'>Delete</button>`;
                      }
                    }
                ]
            });

            // Handle Add Course Form Submission
            $('#addCourseForm').on('submit', function(e) {
                e.preventDefault();

                var courseName = $('#courseName').val();
                var description = $('#description').val();

                $.ajax({
                    url: baseUrl + "/api-add-courses.php",
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        courseName: courseName,
                        description: description
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
                            $('#addCourseForm')[0].reset();
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
                            text: "Failed to add course. Please try again.",
                            icon: "error",
                            button: "OK"
                        });
                        console.error(xhr.responseText);
                    }
                });
            });

            // Handle Delete Course
            $('#coursesTable').on('click', '.delete', function() {
                var courseId = $(this).data('id');

                swal({
                    title: "Are you sure?",
                    text: "You are about to delete this course.",
                    icon: "warning",
                    buttons: ["Cancel", "Yes, Delete"],
                    dangerMode: true
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: baseUrl + "/api-delete-courses.php",
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                courseId: courseId
                            }),
                            success: function(response) {
                                if (response.success) {
                                    swal({
                                        title: "Deleted!",
                                        text: "The course has been deleted.",
                                        icon: "success",
                                        button: "OK"
                                    });
                                    table.ajax.reload();
                                } else {
                                    swal({
                                        title: "Error!",
                                        text: response.message || 'Failed to delete course.',
                                        icon: "error",
                                        button: "OK"
                                    });
                                }
                            },
                            error: function(xhr) {
                                swal({
                                    title: "Error!",
                                    text: "Failed to delete course. Please try again.",
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
