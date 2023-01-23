<?php

use DecodoMastodonService\Controller\Document\Document;

$document = new Document();
$document->setTopNavigation('')
    ->start($_ENV['APP_NAME'] . ' Imprint');
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
