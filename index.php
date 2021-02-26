<?php
session_start();
require_once './pdo_ini.php';

define('TASKS_PER_PAGE', 5);

if (!isset($_SESSION['lastInsertedId'])) {
    $sth = $pdo->prepare('INSERT INTO todo_lists (created_at) VALUES (NOW())');
    $sth->execute();
    $sth = $pdo->prepare('SELECT `id` FROM `todo_lists`');
    $sth->execute();
    $_SESSION['lastInsertedId'] = $sth->fetch()['id'];
} else {
    $sth_count = $pdo->query('SELECT COUNT(*) AS `total` FROM `todo_tasks` WHERE `todo_lists_id`=' . $_SESSION['lastInsertedId']);
    $sth_count->execute();
    $total_value = intval($sth_count->fetch()['total']);
    $page = $_GET['page'] ?? 1;
    $_SESSION['page'] = $page;
    $start = ($page * TASKS_PER_PAGE) - TASKS_PER_PAGE;
    $total = intval((($total_value - 1) / TASKS_PER_PAGE) + 1);
    $total = ($page > $total) ? $page : $total;

    $sql = 'SELECT `id`,`title`, `is_done`, `position` FROM `todo_tasks` WHERE `todo_lists_id`=' . $_SESSION['lastInsertedId'] . ' ORDER BY `position` DESC';
    $sql .= " limit $start , " . TASKS_PER_PAGE;
    $tasks = [];
    $sth = $pdo->prepare($sql);
    $sth->setFetchMode(\PDO::FETCH_ASSOC);
    $res = $sth->execute();
    $tasks = $sth->fetchAll();
    $_SESSION['tasks'] = $tasks;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>To-do List</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="main">
        <header class="head-section">
            <h2> To-Do List </h2>
        </header>
        <form method="post" action="./add_task.php" class="add-section">
            <?php $placeholder = (isset($_GET['mess']) && $_GET['mess'] == 'error') ? "You must fill this field!!!" : "What do you need to do?"?>
                <input type="text" name="task" class="task_input" placeholder="<?= $placeholder ?>">
            <button type="submit" name="submit" id="add_btn" class="add_btn">Add Task</button>
        </form>

        <div class="output-section">
            <h3 class="output-title">You need to do<h3>
            <?php if (isset($tasks)): ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="show-items">
                        <form method="post" action="./move.php" class="buttons-item">
                            <button type = "submit" class="button move up" name="move-up" value='<?= $task['position'] ?>'>&#9650;</button>
                            <button type = "submit" class="button move down" name="move-down" value='<?= $task['position'] ?>'>&#9660;</button>
                        </form>
                        <h3 class="title-todo"><?=$task['title']?></h3>
                        <a href="./checked.php?<?=http_build_query(['id' => $task['id'], 'done' => $task['is_done']])?>" class="checked"><?= $task['is_done'] == 1 ? 'Done' : 'Plan' ?></a>
                        <form method="post" action="./delete.php" class="buttons-item">
                            <button type = "submit" class="button delete" name="delete" value='<?= $task['id'] ?>'>&#128465; </button>
                        </form>
                    </div>
                <?php endforeach?>
            <?php endif?>
        </div>

        <?php if (isset($page) && $page > 0): ?>
            <nav class="navigation">
                <ul class="pagination">
                    <?php if ($page > 2): ?>
                            <li class='page-item start'><a class='page-link' href="/?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">1</a></li>
                        <?php endif;?>
                        <?php if ($page > 3 && $total > 4): ?>
                            <li class='page-item prev'><a class='page-link' href="/?<?= http_build_query(array_merge($_GET, ['page' => ($page - 1)])) ?>"><</a>
                        <?php endif;?>
                        <?php if ($page - 1 > 0): ?>
                            <li class='page-item'><a class='page-link' href="/?<?= http_build_query(array_merge($_GET, ['page' => ($page - 1)])) ?>"><?= ($page - 1) ?></a>
                        <?php endif;?>
                            <li class='page-item active'><a class='page-link' href="/?<?= http_build_query(array_merge($_GET, ['page' => $page])) ?>"><?= ($page) ?></a>
                        <?php if ($page + 1 <= $total): ?>
                            <li class='page-item'><a class='page-link' href="/?<?= http_build_query(array_merge($_GET, ['page' => ($page + 1)])) ?>"><?= ($page + 1) ?></a>
                        <?php endif;?>
                        <?php if ($page < $total - 2 && $total > 4): ?>
                            <li class='page-item next'><a class='page-link' href="/?<?= http_build_query(array_merge($_GET, ['page' => ($page + 1)])) ?>">></a>
                        <?php endif;?>
                        <?php if ($page < $total - 1): ?>
                            <li class='page-item end'><a class='page-link' href="/?<?= http_build_query(array_merge($_GET, ['page' => $total])) ?>"><?= $total ?></a></li>
                    <?php endif;?>
                </ul>
            </nav>
        <?php endif;?>
    </div>
</body>
</html>

