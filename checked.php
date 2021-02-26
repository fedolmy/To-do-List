<?php
if (isset($_GET['done']) && isset($_GET['id'])) {
    require_once './pdo_ini.php';
    session_start();

    $page = $_SESSION['page'];

    $id = htmlspecialchars(trim($_GET['id']));
    $is_done = $_GET['done'] == 0 ? 1 : 0;
    if (empty($id) || empty($id)) {
        header("Location: ../index.php?mess=check-empty-errors");
    } else {
        $sth = $pdo->prepare("UPDATE `todo_tasks` SET `is_done` = '$is_done' WHERE `id` = $id AND `todo_lists_id`=" . $_SESSION['lastInsertedId']);
        $res = $sth->execute();
        if ($res) {
            header("Location: ../index.php?mess=checked-success&page=$page");
        } else {
            header("Location: ../index.php?mess=checked");
        }
        $pdo = null;
        exit;
    }
} else {
    header("Location: ../index.php?mess=checked-errors");
}
