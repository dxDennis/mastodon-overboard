<?php

namespace DecodoMastodonService\Controller\Document;

class Document
{
    public string $content = '';
    public string $title = '';
    public string $topNavigation = '';
    public static string $templateFile = '';

    public static function debugModal($chunk): string
    {
        return '<a style="position:absolute; right:15px; bottom: 15px; z-index: 9999" type="button" data-bs-toggle="modal" data-bs-target="#debugModal">
                <i class="fa fa-bug"></i>
                    </a><div class="modal fade" id="debugModal" tabindex="-1" aria-labelledby="debugModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body" style="max-height: 85vh; overflow:auto;"><pre>' . print_r($chunk, 1) . '<pre></div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>';
    }

    /**
     * @return Document
     */
    public function start(): Document
    {
        ob_start();
        return $this;
    }

    /**
     * @return Document
     */
    public function send(): Document
    {
        $this->content = ob_get_contents();
        @ob_end_clean();
        include_once self::getTemplateFile();
        return $this;
    }

    /**
     * @return string
     */
    public static function getTemplateFile(): string
    {
        if (!self::$templateFile || empty(self::$templateFile)) {
            self::$templateFile = TEMPLATE_DIRECTORY . (!empty($_ENV['TEMPLATE']) ? $_ENV['TEMPLATE'] : 'template.php');
        }
        return self::$templateFile;
    }

}
