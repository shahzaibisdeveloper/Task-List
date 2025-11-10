<?php
session_start();
$message = '';
$message_type = '';
$tasktoedit = null;
$conn = mysqli_connect("localhost", "root", "", "todolist") or die("Connection Error!" . mysqli_connect_error());

if (isset($_GET['edit']) && isset($_GET['id'])) {

    $id = intval($_GET['id']);
    $sql = "SELECT * FROM task where id=?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt):
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0):
            $tasktoedit = mysqli_fetch_assoc($result);
        else:
            $message = "Error Fetching Data!" . mysqli_error($conn);
            $message_type = "error";
        endif;
        mysqli_stmt_close($stmt);
    else:
        $message = "Error preparing statement." . mysqli_errno($conn);
        $message_type = "error";
    endif;
}
if (isset($_POST['submit']) || isset($_POST['update'])) {

    if (!empty($_POST['taskname']) && !empty($_POST['status'])):

        if (isset($_POST['update']) && isset($_POST['id'])):

            $id = intval($_POST['id']);
            $newtask = mysqli_real_escape_string($conn, $_POST['taskname']);
            $status = mysqli_real_escape_string($conn, $_POST['status']);

            $update = "UPDATE task SET task_name= ?, status=? where id=?";

            $result = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($result, "ssi", $newtask, $status, $id);
            $stmt_execute = mysqli_stmt_execute($result);

            if ($stmt_execute):
                $message = "Task Updated Successfully!";
                $message_type = "success";
                header("location: ToDoList.php");
                exit;
            endif;
            mysqli_stmt_close($result);
        else:
            $task = mysqli_real_escape_string($conn, $_POST['taskname']);
            $status = mysqli_real_escape_string($conn, $_POST['status']);

            $insert = "INSERT INTO task (task_name, status) VALUES (?, ?)";

            $result = mysqli_prepare($conn, $insert);
            mysqli_stmt_bind_param($result, "ss", $task, $status);
            $stmt_execute = mysqli_stmt_execute($result);

            if ($stmt_execute):
                $message = "Task Added Successfully!";
                $message_type = "success";
                header("location: ToDoList.php");
                exit;
            endif;
        endif;
    else:
        $message = "Task cannot be empty!";
        $message_type = "error";
    endif;
    mysqli_stmt_close($result);
}
$sql = "SELECT * FROM task";
$all_results = mysqli_query($conn, $sql);
/*if (mysqli_num_rows($result1) > 0):
    while ($result2 = mysqli_fetch_assoc($result1)):
        print_r($result2);
    endwhile;
endif;*/

if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM task where id = '$id'";
    $result = mysqli_query($conn, $sql) or die("Query Failed!");
    header("location: ToDoList.php");
    exit;
}
/*if (isset($_POST['update']) && !empty($_POST['taskname'])) {
    $id = intval($_GET['id']);
    $task = mysqli_real_escape_string($conn, $_POST['taskname']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $sql = "UPDATE task set task_name='$task', status='$status' where id='$id'";
    $result = mysqli_query($conn, $sql) or die("Query Failed!");
    header("location: todolist.php");
    exit;
}*/

?>
<!-- <!DOCTYPE HTML>
<html>
    <head>
        <title>*</title>
</head>
<body>
    <h1>Add Tasks</h1>
    <form method="POST">
        <label for="taskname">Task: </label>
        <input type="text" name="taskname" required>
        <label for="status">Task Status: </label>
        <select name="status" id="status">
        <option value="done">Done</option>
        <option value="ongoing">Ongoing</option>
        <option value="undone">Undone</option></select><br>
        <button type="submit" name="submit">ADD</button>
    </form>
</body>
</html> -->
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Todo List</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Link to your external CSS file -->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="site">
        <div class="container" role="main" aria-labelledby="page-title">
            <div class="header">
                <div class="title">
                    <div class="logo">TD</div>
                    <div>
                        <h1 id="page-title"><?php echo ($tasktoedit) ?  "Modify" : "Add Tasks"; ?></h1>
                        <p class="lead"><?php echo ($tasktoedit) ? "Modify Your Existing Tasks." : "Quickly add tasks to your todo list."; ?></p>
                    </div>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo ($message_type === 'success') ? 'success' : 'error'; ?>" role="status" aria-live="polite">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="field">
                    <label for="taskname">Task</label>
                    <input type="text" name="taskname" id="taskname" required value="<?php echo htmlspecialchars($tasktoedit['task_name'] ?? ""); ?>">
                </div>

                <div class="field">
                    <label for="status">Task Status</label>
                    <select name="status" id="status">
                        <option value="done" <?php echo (isset($tasktoedit['status']) && ($tasktoedit['status']) == "done") ? "selected" : ""; ?>>Done</option>
                        <option value="ongoing" <?php echo (isset($tasktoedit['status']) && ($tasktoedit['status']) == "ongoing") ? "selected" : ""; ?>>Ongoing</option>
                        <option value="undone" <?php echo (isset($tasktoedit['status']) && ($tasktoedit['status']) == "undone") ? "selected" : ""; ?>>Undone</option>
                    </select>
                </div>

                <div class="actions">
                    <?php if ($tasktoedit): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($tasktoedit['id']); ?>">
                        <button type="submit" name="update" class="btn">Update</button>
                        <button type="button" class="btn secondary" onclick="location.href='ToDoList.php'">Cancel Edit</button>
                    <?php else: ?>
                        <button name="submit" type="submit" class="btn">Add Task</button>
                    <?php endif; ?>
                    <span class="muted-note">Tasks are stored in MySQL.</span>
                </div>
            </form>
        </div>
    </div>
    <table align="center" >
        <thead>
            <tr>
                <th>ID</th>
                <th>Task</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($all_results) > 0) {
                while ($data = mysqli_fetch_assoc($all_results)) { ?>
                    <tr>
                        <td data-label="ID"> <?php echo  htmlspecialchars($data['id']) ?></td>
                        <td data-label="Task"> <?php echo  htmlspecialchars($data['task_name']) ?></td>
                        <td data-label="Status"> <?php $status_class = strtolower($data['status']);
                                                    echo "<span class='status-cell status-{$status_class}'><span class='status-dot'></span>" . htmlspecialchars($data['status']) . "</span>"; ?> </td>
                        <td data-label="Actions">
                            <a href="ToDoList.php?edit=true&id=<?php echo $data['id'] ?>">Edit</a>
                            <a href="ToDoList.php?delete=true&id=<?php echo $data['id'] ?>" class="btn-link danger">Delete</a>
                        </td>
                <?php }
            } else {
                echo "<tr><td colspan='4'><span class='empty-table-message'><b>No Tasks Found.</b></span></td></tr>";
            }            ?>
                    </tr>
        </tbody>
    </table>
</body>
<?php mysqli_close($conn); ?>

</html>