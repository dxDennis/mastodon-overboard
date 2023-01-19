<?php

use DecodoMastodonService\Controller\Document\Document;

global $routingData;

$document = new Document();
$document->title = $_ENV['APP_NAME'];
$document->topNavigation = '';
$document->start();
?>
    <div class="container">
    <div class="card card-body">
        <strong>Publisher</strong>
        <hr>
        <address>
            <?php echo $_ENV['IMPRINT_PUBLISHER']; ?>
        </address>
    </div>
    </div><?php
$document->send();
