<?php
if (isset($_POST['delete'])) {
    require_once './pdo_ini.php';
    session_start();

    $page = $_SESSION['page'];
    if (count($_SESSION['tasks']) == 1) {
        $page = $page - 1;
    }

    $id = htmlspecialchars(trim($_POST['delete']));
    if (empty($id)) {
        header("Location: ../index.php?mess=del-empty-errors");
    } else {
        $sth = $pdo->prepare("DELETE FROM `todo_tasks` WHERE `id`= $id AND `todo_lists_id`=" . $_SESSION['lastInsertedId']);
        $res = $sth->execute();
        if ($res) {
            header("Location: ../index.php?mess=del-success&page=$page");
        } else {
            header("Location: ../index.php?mess=del");
        }
        $pdo = null;
        exit;
    }
} else {
    header("Location: ../index.php?mess=del-errors");
}
