<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mongo Playground</title>
    <link href="/components/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <script type="text/javascript" src="/components/jquery/dist/jquery.js"></script>
    <script type="text/javascript" src="/components/bootstrap/dist/js/bootstrap.js"></script>
    <script type="text/javascript" src="/components/angular/angular.js"></script>
    <script type="text/javascript" src="/components/angular-route/angular-route.js"></script>
    <script type="text/javascript" src="/components/angular-resource/angular-resource.js"></script>
    <script type="text/javascript" src="/components/angular-contenteditable/angular-contenteditable.js"></script>
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