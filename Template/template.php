<?php
/**
 * @var $document Document
 */

use DecodoMastodonService\Controller\Document\Document;

global $document;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="application-name" content="<?php echo $document->title; ?>">
    <meta name="keywords" content="Mastodon, Servers, Directory, List, Overview, Overboard">
    <meta name="description"
          content="The purpose of What`s Mastogoing is simply to combine several instances into one overboard.">
    <meta name="author" content="Dennis Decker">
    <base href="https://www.decodo.de/mastodon/"/>
    <link rel="icon" href="/assets/img/logo-symbol-icon.svg" type="image/svg+xml"/>
    <link rel="icon" type="image/svg+xml" sizes="192x192" href="/mastodon/assets/img/logo-symbol-icon.svg">
    <link rel="icon" type="image/svg+xml" sizes="32x32" href="/mastodon/assets/img/logo-symbol-icon.svg">
    <link rel="icon" type="image/svg+xml" sizes="96x96" href="/mastodon/assets/img/logo-symbol-icon.svg">
    <link rel="icon" type="image/svg+xml" sizes="16x16" href="/mastodon/assets/img/logo-symbol-icon.svg">
    <title><?php echo $document->title; ?></title>
    <link rel="canonical" href="https://www.decodo.de/mastodon/">
    <link href="/mastodon/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/mastodon/vendor/fortawesome/font-awesome/css/all.min.css" rel="stylesheet">
    <link href="/mastodon/assets/css/style.css" rel="stylesheet">
    <?php echo $document->css; ?>
    <link rel="apple-touch-icon" href="/mastodon/assets/img/logo-symbol-icon.svg" sizes="180x180" type="image/svg+xml">
    <link rel="icon" href="/mastodon/assets/img/logo-symbol-icon.svg" sizes="32x32" type="image/svg+xml">
    <link rel="icon" href="/mastodon/assets/img/logo-symbol-icon.svg" sizes="16x16" type="image/svg+xml">
    <link rel="mask-icon" href="/mastodon/assets/img/logo-symbol-icon.svg" color="#6364FF" type="image/svg+xml">
    <link rel="icon" href="/mastodon/assets/img/logo-symbol-icon.svg" type="image/svg+xml">
    <meta name="theme-color" content="#191b22">
</head>
<body>
<header>
    <div class="collapse bg-dark" id="navbarHeader">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-md-7 py-4">
                    <h4 class="text-white">About</h4>
                    <p class="text-muted">This is a "just for fun" project.
                        The purpose of "<?php echo $document->title; ?>" is simply to combine several instances into one
                        overboard.</p>
                </div>
                <div class="col-sm-4 offset-md-1 py-4">
                    <h4 class="text-white">Mastodon Instances</h4>
                    <?php echo $document->topNavigation; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a href="#" class="navbar-brand d-flex align-items-center">
                <img src="/mastodon/assets/img/logo-symbol-icon.svg" style="width: 29px; margin-right: 5px;">
                <strong><?php echo $document->title; ?></strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader"
                    aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
</header>
<main>
    <div class="py-4 bg-light">
        <?php echo $document->content; ?>
    </div>
</main>
<footer class="text-muted py-5">
    <div class="container">
        <p class="float-end mb-1">
            <a href="#">Back to top</a>
        </p>
        <p class="mb-0">created by: <a href="https://connectit.social/@dDennis">Dennis Decker</a>.</p>
    </div>
</footer>
<script src="/mastodon/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/mastodon/vendor/components/jquery/jquery.min.js"></script>
<?php echo $document->js; ?>
</body>
</html>
