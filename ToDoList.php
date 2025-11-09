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
    $sql = "DELETE FROM task where id=?";
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
    <link rel="stylesheet" href="style.css">
    <!-- <style>
        :root {
            --page-bg: linear-gradient(180deg, #eef2ff 0%, #f6f8fa 100%);
            --card-bg: #ffffff;
            --accent: #2563eb;
            --accent-dark: #1e40af;
            --muted: #6b7280;
            --success: #16a34a;
            --danger: #dc2626;
            --radius: 12px;
            --max-width: 820px;
            --shadow: 0 10px 30px rgba(2, 6, 23, 0.08);
            font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            background: var(--page-bg);
            color: #0f172a;
        }

        .site {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .container {
            width: 100%;
            max-width: var(--max-width);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(250, 250, 250, 0.98));
            border-radius: var(--radius);
            padding: 28px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(16, 24, 40, 0.03);
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .title {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .logo {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.12);
            flex-shrink: 0;
        }

        h1 {
            margin: 0;
            font-size: 1.25rem;
        }

        p.lead {
            margin: 4px 0 0 0;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .controls {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .show-tasks-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 10px;
            border: 0;
            background: linear-gradient(90deg, var(--accent), var(--accent-dark));
            color: #fff;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.12);
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .show-tasks-btn.secondary {
            background: transparent;
            color: var(--accent-dark);
            border: 1px solid rgba(37, 99, 235, 0.08);
            box-shadow: none;
        }

        .show-tasks-btn:hover {
            transform: translateY(-3px);
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 16px;
            align-items: end;
        }

        label {
            display: block;
            font-size: 0.9rem;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .field {
            display: flex;
            flex-direction: column;
        }

        input[type="text"],
        select {
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid #e6e9ef;
            background: #ffffff;
            font-size: 1rem;
            outline: none;
            transition: box-shadow 160ms ease, border-color 160ms ease;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: var(--accent);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.06);
        }

        .actions {
            grid-column: 1 / -1;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            padding: 12px 16px;
            border-radius: 10px;
            border: 0;
            cursor: pointer;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(90deg, var(--accent), var(--accent-dark));
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.08);
        }

        .btn.secondary {
            background: #efefef;
            color: var(--accent-dark);
            border: 1px solid rgba(16, 24, 40, 0.04);
            box-shadow: none;
        }

        .message {
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 700;
            display: inline-block;
        }

        .message.success {
            background: rgba(16, 185, 129, 0.08);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.08);
        }

        .message.error {
            background: rgba(220, 38, 38, 0.06);
            color: var(--danger);
            border: 1px solid rgba(220, 38, 38, 0.06);
        }

        .muted-note {
            color: var(--muted);
            font-size: 0.92rem;
        }

        @media (max-width:720px) {
            form {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .controls {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>  -->
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
    <table border="2px" cellpadding="5px" cellspacing="5px" align='center'>
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
                        <td> <?php echo  htmlspecialchars($data['id']) ?></td>
                        <td> <?php echo  htmlspecialchars($data['task_name']) ?></td>
                        <td> <?php $status_class = strtolower($data['status']); // e.g., 'done', 'ongoing', 'undone'
                                echo "<span class='status-cell status-{$status_class}'><span class='status-dot'></span>" . htmlspecialchars($data['status']) . "</span>"; ?> </td>
                        <td> <a href="ToDoList.php?delete=true&id=<?php echo $data['id'] ?>">Delete</a>
                            <a href="ToDoList.php?edit=true&id=<?php echo $data['id'] ?>">Edit</a>
                        </td>
                <?php }
            } else {
                echo "<tr><td colspan='4'><span class='empty-table-message'><b>No Tasks Found.</b></span></td></tr>";
            }            ?>
                    </tr>
        </tbody>
    </table>
</body>

</html>