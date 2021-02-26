<?php
if (isset($_POST['move-up']) || isset($_POST['move-down'])) {
    require_once './pdo_ini.php';
    session_start();

    $list_id = $_SESSION['lastInsertedId'];
    $page = $_SESSION['page'];

    if (empty($_POST['move-down']) && empty($_POST['move-up'])) {
        header("Location: ../index.php?mess=move-empty-errors");
    } elseif (isset($_POST['move-down'])) {
        $pos = htmlspecialchars(trim($_POST['move-down']));
        $sql = <<<"SQL"
            SELECT `id`, `position`
            FROM `todo_tasks`
            WHERE `position` = $pos AND `todo_lists_id` = $list_id
            UNION
            SELECT `id`, `position`
            FROM `todo_tasks` WHERE `position` < $pos AND `todo_lists_id` = $list_id
            ORDER BY `position` DESC LIMIT 2
        SQL;
        $sth = $pdo->prepare($sql);
        $sth->setFetchMode(\PDO::FETCH_ASSOC);
        $sth->execute();
        $change_arr = $sth->fetchAll();
        $curr_arr = array_shift($change_arr);
        $prev_arr = array_shift($change_arr);
        $curr_id = $curr_arr['id'];
        $curr_pos = $curr_arr['position'];
        $prev_pos = $prev_arr['position'];
        $prev_id = $prev_arr['id'];
        $sth = $pdo->prepare("UPDATE `todo_tasks` SET `position`= $curr_pos WHERE `id`= $prev_id AND `todo_lists_id` = $list_id");
        $res = $sth->execute();
        $sth = $pdo->prepare("UPDATE `todo_tasks` SET `position`= $prev_pos WHERE `id`= $curr_id AND `todo_lists_id` = $list_id");
        $res = $sth->execute();

        if ($res) {
            header("Location: ../index.php?mess=down-success&page=$page");
        } else {
            header("Location: ../index.php?mess=down&page=$page");
        }
        $pdo = null;
        exit;
    }

    if (empty($_POST['move-up'])) {
        header("Location: ../index.php?mess=move-up-errors");
    } elseif (isset($_POST['move-up'])) {
        $pos = htmlspecialchars(trim($_POST['move-up']));
        $sql = <<<"SQL"
            SELECT `id`, `position`
            FROM `todo_tasks`
            WHERE `position` = $pos AND `todo_lists_id` = $list_id
            UNION
            SELECT `id`, `position`
            FROM `todo_tasks` WHERE `position` > $pos AND `todo_lists_id` = $list_id
            ORDER BY `position` ASC LIMIT 2
        SQL;
        $sth = $pdo->prepare($sql);
        $sth->setFetchMode(\PDO::FETCH_ASSOC);
        $sth->execute();
        $change_arr = $sth->fetchAll();
        $curr_arr = array_shift($change_arr);
        $next_arr = array_shift($change_arr);
        $curr_id = $curr_arr['id'];
        $curr_pos = $curr_arr['position'];
        $next_pos = $next_arr['position'];
        $next_id = $next_arr['id'];
        $sth = $pdo->prepare("UPDATE `todo_tasks` SET `position`= $curr_pos WHERE `id`= $next_id AND `todo_lists_id` = $list_id");
        $res = $sth->execute();
        $sth = $pdo->prepare("UPDATE `todo_tasks` SET `position`= $next_pos WHERE `id`= $curr_id AND `todo_lists_id` = $list_id");
        $res = $sth->execute();

        if ($res) {
            header("Location: ../index.php?mess=up-success&page=$page");
        } else {
            header("Location: ../index.php?mess=up");
        }
        $pdo = null;
        exit;
    }
} else {
    header("Location: ../index.php?mess=up-errors");
}
