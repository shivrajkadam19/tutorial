<!DOCTYPE html>
<html lang="en">


<!-- blank.html  21 Nov 2019 03:54:41 GMT -->

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
    <!-- Page Specific JS File -->
    <script src="assets/js/page/sweetalert.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#coursesTable').DataTable({
                "ajax": {
                    "url": "http://localhost/tutorial/admin/api/api-get-courses.php",
                    "dataSrc": "courses"
                },
                "columns": [{
                        "data": "CourseName"
                    },
                    {
                        "data": "Description"
                    },
                    {
                        "data": null,
                        "defaultContent": "<button class='btn btn-danger delete'>Delete</button>"
                    }
                ]
            });

            // Handle Add Course Form Submission
            $('#addCourseForm').on('submit', function(e) {
                e.preventDefault();

                // Get form values
                var courseName = $('#courseName').val();
                var description = $('#description').val();

                $.ajax({
                    url: 'http://localhost/tutorial/admin/api/api-add-courses.php',
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
                            table.ajax.reload(); // Reload table data
                            $('#addCourseForm')[0].reset(); // Reset form
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
                var row = $(this).closest('tr');
                var courseName = row.find('td:eq(0)').text(); // Get course name

                swal({
                    title: "Are you sure?",
                    text: `You are about to delete the course: ${courseName}`,
                    icon: "warning",
                    buttons: ["Cancel", "Yes, Delete"],
                    dangerMode: true
                }).then((willDelete) => {
                    if (willDelete) {
                        // Simulate a delete AJAX call
                        $.ajax({
                            url: 'http://localhost/tutorial/admin/api/api-delete-courses.php',
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                courseName: courseName
                            }),
                            success: function(response) {
                                if (response.success) {
                                    swal({
                                        title: "Deleted!",
                                        text: "The course has been deleted.",
                                        icon: "success",
                                        button: "OK"
                                    });
                                    table.ajax.reload(); // Reload table data
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