<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Mongo Playground</title>
</head>
<body>
    Libs and git init/ ignore and config sample
    <ul>
        <?php foreach($data['examples'] as $example): ?>
            <li><?= $example['name']; ?></li>
        <?php endforeach; ?>
    </ul>
    <?= $data['userProgress']['_id']; ?>
</body>
</html>