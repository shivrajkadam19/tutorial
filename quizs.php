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
    <title>Admin Dashboard - Quizzes</title>
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

                        <!-- Form to Add New Quiz -->
                        <h2>Add New Quiz</h2>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Quiz Form</h4>
                                    </div>
                                    <div class="card-body">
                                        <form id="addQuizForm">
                                            <div class="form-group">
                                                <label for="quizName">Quiz Name</label>
                                                <input type="text" id="quizName" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="topicName">Topic</label>
                                                <select id="topicName" class="form-control" required>
                                                    <!-- Dynamic Topic Options -->
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Quiz</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DataTable for Quizzes -->
                        <h2>Quizzes</h2>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Quiz List</h4>
                                    </div>
                                    <div class="card-body">
                                        <table id="quizzesTable" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Quiz Name</th>
                                                    <th>Topic Name</th>
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
            // Populate Topic Dropdown
            const baseUrl = '<?php echo $url; ?>';

            $.ajax({
                url: baseUrl + "/api-get-topics.php",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var topics = response.topics;
                        var topicSelect = $('#topicName');

                        topics.forEach(function(topic) {
                            topicSelect.append(`<option value="${topic.TopicID}">${topic.TopicName}</option>`);
                        });
                    } else {
                        console.error('Failed to load topics');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });

            // Initialize DataTable
            var table = $('#quizzesTable').DataTable({
                "ajax": {
                    "url": "http://localhost/tutorial/admin/api/api-get-quizs.php",
                    "dataSrc": function(json) {
                        if (json.success) {
                            return json.quizzes;
                        } else {
                            console.error(json.message);
                            return [];
                        }
                    }
                },
                "columns": [{
                        "data": "QuizName"
                    },
                    {
                        "data": "TopicName"
                    },
                    {
                        "data": "QuizID",
                        "render": function(data, type, row) {
                            return `<button class='btn btn-danger delete' data-id='${data}'>Delete</button>`;
                        }
                    }
                ]
            });

            // Handle Add Quiz Form Submission
            $('#addQuizForm').on('submit', function(e) {
                e.preventDefault();

                var quizName = $('#quizName').val();
                var topicId = $('#topicName').val();

                $.ajax({
                    url: baseUrl + "/api-add-quizs.php",
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        quizName: quizName,
                        topicId: topicId
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
                            $('#addQuizForm')[0].reset();
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
                            text: "Failed to add quiz. Please try again.",
                            icon: "error",
                            button: "OK"
                        });
                        console.error(xhr.responseText);
                    }
                });
            });

            // Handle Delete Quiz
            $('#quizzesTable').on('click', '.delete', function() {
                var quizId = $(this).data('id');

                swal({
                    title: "Are you sure?",
                    text: "You are about to delete this quiz.",
                    icon: "warning",
                    buttons: ["Cancel", "Yes, Delete"],
                    dangerMode: true
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: baseUrl + "/api-delete-quizs.php",
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                quizId: quizId
                            }),
                            success: function(response) {
                                if (response.success) {
                                    swal({
                                        title: "Deleted!",
                                        text: "The quiz has been deleted.",
                                        icon: "success",
                                        button: "OK"
                                    });
                                    table.ajax.reload();
                                } else {
                                    swal({
                                        title: "Error!",
                                        text: response.message || 'Failed to delete quiz.',
                                        icon: "error",
                                        button: "OK"
                                    });
                                }
                            },
                            error: function(xhr) {
                                swal({
                                    title: "Error!",
                                    text: "Failed to delete quiz. Please try again.",
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