<?php
/**
 * @var $routingData array
 */

use DecodoMastodonService\Component\Main\MainHelper;
use DecodoMastodonService\Controller\Document\Document;
use DecodoMastodonService\Controller\Request;

$helper = new MainHelper();

$statusId = $routingData['action'] ?? '';
$instance = Request::get('instance');
$ajax = Request::get('ajax');

if (empty($statusId) || empty($instance)) {
    $routingData = [
        'viewport' => 'error',
        'statusId' => $statusId,
        'instance' => $instance,
        'error' => 'PostId or Instance cannot be empty',
    ];
    include_once __DIR__ . '/error.php';
    exit;
}

$statusContext = $helper->getStatusContext($statusId, $instance);

$html = '<div class="list-group-item list-group-item-action d-flex gap-3 py-3 text-center" aria-current="true">Keine Antworten</div>';
if (isset($statusContext['data']['descendants']) && count($statusContext['data']['descendants']) > 0) {

    $html = '';
    foreach ($statusContext['data']['descendants'] as $descendant) {

        $html = '<div class="p-2 d-flex gap-3 py-3" aria-current="true">' . //list-group-item list-group-item-action
            '<a href="' . trim($descendant['account']['url']) . '" target="_blank">' .
            '<img src="' . trim($descendant['account']['avatar']) . '" alt="' . trim($descendant['account']['username']) . '" class="rounded-circle flex-shrink-0" width="32" height="32">' .
            '</a>' .
            '<div class="d-flex gap-2 w-100 justify-content-between">' .
            '<div class=" position-relative">' .
            '<h6 class="mb-0">' . trim($descendant['account']['display_name']) . ' <small>' . trim($descendant['account']['username']) . '</small></h6>' .
            '<p class="mb-0 opacity-75">' . trim($descendant['content']) . '</p>' .
            '<a target="_blank" class="stretched-link" href="' . $descendant['url'] . '">&nbsp;</a>' .
            '</div>' .
            '<small title="' . $descendant['created_at'] . '" class="opacity-50 text-nowrap">' . $helper::timeAgo($descendant['created_at']) . '</small>' .

            '</div>' .
            '</div>' . $html;
    }
}
if (!$ajax) {
    $document = new Document();
    $document->addJs('/mastodon/assets/js/main.js')
        ->setTopNavigation($helper->parseInstanceLinks())
        ->start($_ENV['APP_NAME']);
}
?>
    <div class="xlist-group w-auto mt-4">
        <div style="max-height: 45vh; overflow-y:auto "><?php echo $html; ?></div>
    </div>
<?php
if (!$ajax) {
    echo Document::debugModal($statusContext);
    $document->send();
}
