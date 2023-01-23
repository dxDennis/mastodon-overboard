<?php

namespace DecodoMastodonService\Controller\Document;

use JetBrains\PhpStorm\NoReturn;

class Document
{
    public string $content = '';
    public string $title = '';
    public string $css = '';
    public string $js = '';
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

    public static function toJson(string $data, $encode = false)
    {
        $JSON = ($encode ? json_encode($data) : $data);
        if (!$JSON) {
            die('<pre>' . print_r(['JSON_RESULT_ERROR', 'DATA' => $data, 'JSON' => $JSON], 1) . __FILE__ . ' ' . __LINE__ . '</pre>');
        }

        @ob_end_clean();
        header('Content-type: application/json');
        die($JSON);
    }

    /**
     * @param $title
     * @return $this
     */
    public function start($title = NULL): Document
    {
        if (!empty($title)) {
            $this->title = $title;
        }
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

    /**
     * @param string $title
     * @return Document
     */
    public function setTitle(string $title): Document
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $parseInstanceLinks
     * @return Document
     */
    public function setTopNavigation(string $parseInstanceLinks): Document
    {
        $this->topNavigation = $parseInstanceLinks;
        return $this;
    }

    /**
     * @param string $string
     * @return Document
     */
    public function addJs(string $string)
    {
        $this->js .= '<script src="' . $string . '"></script>'."\n";
        return $this;
    }

    /**
     * @param string $string
     * @return Document
     */
    public function addCss(string $string)
    {
        $this->css .= '<link href="' . $string . '" rel="stylesheet">';
        return $this;
    }

}
