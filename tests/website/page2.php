<?php

require_once 'include.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hello world</title>
    <style>
        body {
            font-family: "Nunito", "Arial MT Rounded Bold", Arial, sans-serif;
            font-size: 1.3rem;
            font-style: normal;
            font-weight: 400;
            line-height: 1.7;
            background-color: #f0f0f0;
            margin: 0;
        }

        h1 {
            font-weight: 600;
            margin: 0;
            font-size: 2.2rem;
            line-height: 1.3;
        }

        main {
            margin: 1em auto;
            padding: 1.4em;
            max-width: 24em;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<?= \Studio24\StagingSite\StagingSite::banner() ?>
<main>

    <h1>This is page 2</h1>

    <p>All OK!</p>

    <p>See <a href="index.php">page 1</a></p>

    <p>You can test a <a href="production.php">production page</a></p>

    <p>You can <a href="?staging_site_logout">logout of the staging site</a></p>

</main>
</body>
</html>
