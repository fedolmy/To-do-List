<?php
if (isset($_POST['task'])) {
    require_once './pdo_ini.php';
    session_start();

    $task = htmlspecialchars(trim($_POST['task']));
    if (empty($task)) {
        header("Location: ../index.php?mess=add-empty-error");
    } else {
        $sth = $pdo->prepare("INSERT INTO `todo_tasks` (`todo_lists_id`, `title`, `created_at`) VALUES (" . $_SESSION['lastInsertedId'] . ",'$task', NOW())");
        $sth->execute();
        $sth = $pdo->prepare("SELECT `id` FROM `todo_tasks` WHERE `todo_lists_id` = " . $_SESSION['lastInsertedId']);
        $sth->setFetchMode(\PDO::FETCH_ASSOC);
        $sth->execute();
        $ids = $sth->fetchAll();
        $last_id = array_pop($ids);
        $sth = $pdo->prepare("UPDATE `todo_tasks` SET `position` = '" . $last_id['id'] . "' WHERE `id` = " . $last_id['id'] . " AND `todo_lists_id` = " . $_SESSION['lastInsertedId']);
        $res = $sth->execute();
        if ($res) {
            header("Location: ../index.php?mess=add-success");
        } else {
            header("Location: ../index.php?mess=add");
        }
        $pdo = null;
        exit;
    }
} else {
    header("Location: ../index.php?mess=add-error");
}
