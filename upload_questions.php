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
    <title>Quiz Editor - Otika</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="assets/css/app.min.css">
    <link rel="stylesheet" href="assets/bundles/datatables/datatables.min.css">
    <link rel="stylesheet" href="assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/bundles/summernote/summernote-bs4.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel='shortcut icon' type='image/x-icon' href='assets/img/favicon.ico' />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .action-btn {
            cursor: pointer;
            color: white;
            padding: 5px 10px;
            margin: 5px;
            border: none;
        }

        .delete-btn {
            background-color: red;
        }

        .upload-btn {
            background-color: blue;
        }

        .add-btn {
            background-color: green;
        }
    </style>
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
                        <h1>Quiz Editor</h1>

                        <!-- File Upload Form -->
                        <form id="uploadForm" enctype="multipart/form-data">
                            <input type="file" name="csv_file" id="csvFile" accept=".csv" required>
                            <button type="submit">Upload CSV</button>
                        </form>

                        <hr>

                        <!-- Preview Section -->
                        <div id="previewSection" style="display:none;">
                            <h2>Preview and Edit Questions</h2>
                            <table id="questionsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Question Text (EN)</th>
                                        <th>Question Text (HI)</th>
                                        <th>Option 1 (EN)</th>
                                        <th>Option 1 (HI)</th>
                                        <th>Option 2 (EN)</th>
                                        <th>Option 2 (HI)</th>
                                        <th>Option 3 (EN)</th>
                                        <th>Option 3 (HI)</th>
                                        <th>Option 4 (EN)</th>
                                        <th>Option 4 (HI)</th>
                                        <th>Correct Option</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <button class="action-btn upload-btn" id="finalUpload">Upload to Database</button>
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
    <script src="assets/bundles/datatables/datatables.min.js"></script>
    <script src="assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/bundles/summernote/summernote-bs4.js"></script>
    <script src="assets/js/scripts.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            const table = $("#questionsTable").DataTable({
                paging: true,
                searching: true,
                ordering: true,
                lengthMenu: [[1], [1]], // Pagination options
                autoWidth: false,
                drawCallback: function() {
                    // Reinitialize Summernote when a new page is rendered
                    initializeSummernote();
                },
            });

            // Function to Initialize Summernote
            function initializeSummernote() {
                $('.summernote-full').summernote({
                    height: 150,
                    placeholder: 'Enter question text...',
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview']],
                    ],
                });
                $('.summernote-simple').summernote({
                    height: 150,
                    placeholder: 'Enter option text...',
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol']],
                    ],
                });
            }

            // Handle CSV Upload
            $("#uploadForm").submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: "http://localhost/tutorial/admin/api/api-process-upload.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            renderTable(data.questions);
                            $("#previewSection").show();
                        } else {
                            Swal.fire("Error", data.message, "error");
                        }
                    },
                });
            });

            // Render Editable Table with DataTables
            function renderTable(questions) {
                table.clear();
                questions.forEach((question) => {
                    table.row.add([
                        `<textarea class="summernote-full" name="QuestionText_EN[]">${question.QuestionText_EN}</textarea>`,
                        `<textarea class="summernote-full" name="QuestionText_HI[]">${question.QuestionText_HI}</textarea>`,
                        `<textarea class="summernote-simple" name="Option1_EN[]">${question.Option1_EN}</textarea>`,
                        `<textarea class="summernote-simple" name="Option1_HI[]">${question.Option1_HI}</textarea>`,
                        `<textarea class="summernote-simple" name="Option2_EN[]">${question.Option2_EN}</textarea>`,
                        `<textarea class="summernote-simple" name="Option2_HI[]">${question.Option2_HI}</textarea>`,
                        `<textarea class="summernote-simple" name="Option3_EN[]">${question.Option3_EN}</textarea>`,
                        `<textarea class="summernote-simple" name="Option3_HI[]">${question.Option3_HI}</textarea>`,
                        `<textarea class="summernote-simple" name="Option4_EN[]">${question.Option4_EN}</textarea>`,
                        `<textarea class="summernote-simple" name="Option4_HI[]">${question.Option4_HI}</textarea>`,
                        `<input type="number" value="${question.CorrectOption}" name="CorrectOption[]">`,
                        `<button class="action-btn delete-btn deleteRow">Delete</button>`,
                    ]);
                });
                table.draw();
                // Initialize editors for the first page
                initializeSummernote();
            }

            // Delete Row
            $(document).on("click", ".deleteRow", function() {
                table.row($(this).parents("tr")).remove().draw();
            });

            // Upload Questions to Database
            $("#finalUpload").click(function() {
                const questions = [];
                $("#questionsTable tbody tr").each(function() {
                    const row = $(this);
                    questions.push({
                        QuestionText_EN: row.find('textarea[name="QuestionText_EN[]"]').val(),
                        QuestionText_HI: row.find('textarea[name="QuestionText_HI[]"]').val(),
                        Option1_EN: row.find('textarea[name="Option1_EN[]"]').val(),
                        Option1_HI: row.find('textarea[name="Option1_HI[]"]').val(),
                        Option2_EN: row.find('textarea[name="Option2_EN[]"]').val(),
                        Option2_HI: row.find('textarea[name="Option2_HI[]"]').val(),
                        Option3_EN: row.find('textarea[name="Option3_EN[]"]').val(),
                        Option3_HI: row.find('textarea[name="Option3_HI[]"]').val(),
                        Option4_EN: row.find('textarea[name="Option4_EN[]"]').val(),
                        Option4_HI: row.find('textarea[name="Option4_HI[]"]').val(),
                        CorrectOption: row.find('input[name="CorrectOption[]"]').val(),
                    });
                });
                $.ajax({
                    url: "http://localhost/tutorial/admin/api/api-save-questions.php",
                    type: "POST",
                    data: {
                        questions: JSON.stringify(questions)
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            Swal.fire("Success", "Questions uploaded successfully!", "success");
                            $("#previewSection").hide();
                            table.clear().draw();
                        } else {
                            Swal.fire("Error", data.message, "error");
                        }
                    },
                });
            });
        });
    </script>
</body>

</html>
