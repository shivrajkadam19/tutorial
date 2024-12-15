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
    <title>Admin Dashboard - Topics</title>
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
            <?php include './partial/header.php'; ?>
            <?php include './partial/sidebar.php'; ?>

            <div class="main-content">
                <section class="section">
                    <div class="section-body">

                        <!-- Form to Add New Topic -->
                        <h2>Add New Topic</h2>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Topic Form</h4>
                                    </div>
                                    <div class="card-body">
                                        <form id="addTopicForm">
                                            <div class="form-group">
                                                <label for="topicName">Topic Name</label>
                                                <input type="text" id="topicName" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="subjectName">Subject</label>
                                                <select id="subjectName" class="form-control" required>
                                                    <!-- Dynamic Subject Options -->
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Topic</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DataTable for Topics -->
                        <h2>Topics</h2>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Topic List</h4>
                                    </div>
                                    <div class="card-body">
                                        <table id="topicsTable" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Topic Name</th>
                                                    <th>Subject Name</th>
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

                <?php include './partial/site-settings.php'; ?>
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
            // Populate Subject Dropdown
            const baseUrl = '<?php echo $url; ?>';
            
            $.ajax({
                url: baseUrl + "/api-get-subjects.php",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var subjects = response.subjects;
                        var subjectSelect = $('#subjectName');

                        subjects.forEach(function(subject) {
                            subjectSelect.append(`<option value="${subject.SubjectID}">${subject.SubjectName}</option>`);
                        });
                    } else {
                        console.error('Failed to load subjects');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });

            // Initialize DataTable
            var table = $('#topicsTable').DataTable({
                "ajax": {
                    "url": "http://localhost/tutorial/admin/api/api-get-topics.php",
                    "dataSrc": function(json) {
                        if (json.success) {
                            return json.topics;
                        } else {
                            console.error(json.message);
                            return [];
                        }
                    }
                },
                "columns": [
                    { "data": "TopicName" },
                    { "data": "SubjectName" },
                    {
                        "data": "TopicID",
                        "render": function(data, type, row) {
                            return `<button class='btn btn-danger delete' data-id='${data}'>Delete</button>`;
                        }
                    }
                ]
            });

            // Handle Add Topic Form Submission
            $('#addTopicForm').on('submit', function(e) {
                e.preventDefault();

                var topicName = $('#topicName').val();
                var subjectId = $('#subjectName').val();

                $.ajax({
                    url: baseUrl + "/api-add-topics.php",
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        topicName: topicName,
                        subjectId: subjectId
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
                            $('#addTopicForm')[0].reset();
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
                            text: "Failed to add topic. Please try again.",
                            icon: "error",
                            button: "OK"
                        });
                        console.error(xhr.responseText);
                    }
                });
            });

            // Handle Delete Topic
            $('#topicsTable').on('click', '.delete', function() {
                var topicId = $(this).data('id');

                swal({
                    title: "Are you sure?",
                    text: `You are about to delete this topic.`,
                    icon: "warning",
                    buttons: ["Cancel", "Yes, Delete"],
                    dangerMode: true
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: baseUrl + "/api-delete-topics.php",
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                topicId: topicId
                            }),
                            success: function(response) {
                                if (response.success) {
                                    swal({
                                        title: "Deleted!",
                                        text: "The topic has been deleted.",
                                        icon: "success",
                                        button: "OK"
                                    });
                                    table.ajax.reload();
                                } else {
                                    swal({
                                        title: "Error!",
                                        text: response.message || 'Failed to delete topic.',
                                        icon: "error",
                                        button: "OK"
                                    });
                                }
                            },
                            error: function(xhr) {
                                swal({
                                    title: "Error!",
                                    text: "Failed to delete topic. Please try again.",
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
