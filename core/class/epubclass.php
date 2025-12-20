<?php
class EPubReader {
    private $extractedPath;
    private $opfPath;
    private $opfDir;
    private $opfData;
    private $manifest = [];
    private $spine = [];
    private $metadata = [];
    private $toc = [];

    public function __construct($extractedPath) {
        $this->extractedPath = str_replace('\\', '/', rtrim($extractedPath, '/'));

        if (!$this->findOPF()) {
            error_log("Failed to find OPF file in: " . $extractedPath);
        }

        if ($this->opfPath) {
            $this->parseOPF();
            $this->parseTOC();
        }
    }

    private function findOPF() {
        $containerPath = $this->extractedPath . '/META-INF/container.xml';
        
        if (!file_exists($containerPath)) {
            return $this->searchOPFFile($this->extractedPath);
        }

        $xml = @simplexml_load_file($containerPath);
        if ($xml) {
            $rootfile = $xml->rootfiles->rootfile;
            $opfFile = (string)$rootfile['full-path'];
            $this->opfPath = $this->extractedPath . '/' . $opfFile;
            $this->opfDir = dirname($this->opfPath);
            return file_exists($this->opfPath);
        }
        return false;
    }

    private function searchOPFFile($dir) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;
            $path = $dir . '/' . $file;
            $path = str_replace('\\', '/', $path);
            
            if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'opf') {
                $this->opfPath = $path;
                $this->opfDir = dirname($path);
                return true;
            }
            if (is_dir($path)) {
                if ($this->searchOPFFile($path)) return true;
            }
        }
        return false;
    }

    private function parseOPF() {
        if (!file_exists($this->opfPath)) return false;
        
        $this->opfData = @simplexml_load_file($this->opfPath);
        if (!$this->opfData) return false;

        $this->parseMetadata();
        $this->parseManifest();
        $this->parseSpine();
        return true;
    }

    private function parseMetadata() {
        global $lang;

        try {
            $ns = $this->opfData->getNamespaces(true);
            $dc = $this->opfData->metadata->children($ns['dc'] ?? 'http://purl.org/dc/elements/1.1/');
            
            $defaultTitle = isset($lang['epub_untitled']) ? $lang['epub_untitled'] : 'Untitled';
            $defaultAuthor = isset($lang['epub_unknown_author']) ? $lang['epub_unknown_author'] : 'Unknown';

            $this->metadata['title'] = (string)$dc->title ?: $defaultTitle;
            $this->metadata['author'] = (string)$dc->creator ?: $defaultAuthor;
        } catch (Exception $e) {
            $this->metadata = ['title' => 'Untitled', 'author' => 'Unknown'];
        }
    }

    private function parseManifest() {
        if (!isset($this->opfData->manifest)) return;

        foreach ($this->opfData->manifest->item as $item) {
            $id = (string)$item['id'];
            $this->manifest[$id] = [
                'href' => (string)$item['href'],
                'type' => (string)$item['media-type']
            ];
        }
    }

    private function parseSpine() {
        if (!isset($this->opfData->spine)) return;

        foreach ($this->opfData->spine->itemref as $itemref) {
            $idref = (string)$itemref['idref'];
            if (isset($this->manifest[$idref])) {
                $this->spine[] = [
                    'id' => $idref,
                    'href' => $this->manifest[$idref]['href']
                ];
            }
        }
    }

    private function parseTOC() {
        $ncxFile = null;
        foreach ($this->manifest as $id => $item) {
            if (strpos($item['type'], 'dtbncx') !== false || strpos($item['href'], '.ncx') !== false) {
                $ncxFile = $item['href'];
                break;
            }
        }
        
        if (!$ncxFile && isset($this->opfData->spine['toc'])) {
             $tocId = (string)$this->opfData->spine['toc'];
             if(isset($this->manifest[$tocId])) {
                 $ncxFile = $this->manifest[$tocId]['href'];
             }
        }

        if ($ncxFile) {
            $this->parseTOCFromNCX($ncxFile);
        } else {
            $this->generateTOCFromSpine();
        }
    }

    private function parseTOCFromNCX($ncxFile) {
        $ncxPath = $this->opfDir . '/' . $ncxFile;
        if (!file_exists($ncxPath)) {
            $this->generateTOCFromSpine();
            return;
        }

        $ncx = @simplexml_load_file($ncxPath);
        if (!$ncx || !isset($ncx->navMap)) {
            $this->generateTOCFromSpine();
            return;
        }

        $this->toc = $this->parseRecursiveNavPoints($ncx->navMap->navPoint);
        $this->mapTOCToSpine();
    }

    private function parseRecursiveNavPoints($navPoints) {
        $items = [];
        foreach ($navPoints as $navPoint) {
            $src = (string)$navPoint->content['src'];
            
            $items[] = [
                'label' => trim((string)$navPoint->navLabel->text),
                'src'   => $src,
            ];

            if (isset($navPoint->navPoint)) {
                $children = $this->parseRecursiveNavPoints($navPoint->navPoint);
                $items = array_merge($items, $children);
            }
        }
        return $items;
    }

    private function mapTOCToSpine() {
        foreach ($this->toc as &$tocItem) {
            $tocItem['spine_index'] = 0; 
            
            $parts = explode('#', $tocItem['src']);
            $tocPathRaw = $parts[0]; 
            $tocFilename = basename($tocPathRaw);

            foreach ($this->spine as $index => $spineItem) {
                $spineFilename = basename($spineItem['href']);

                if ($tocFilename === $spineFilename) {
                    $tocItem['spine_index'] = $index;
                    break; 
                }
                
                $decodedToc = urldecode($tocPathRaw);
                $decodedSpine = urldecode($spineItem['href']);
                
                if (stripos($decodedSpine, $decodedToc) !== false || stripos($decodedToc, $decodedSpine) !== false) {
                    $tocItem['spine_index'] = $index;
                    break;
                }
            }
        }
    }

    private function generateTOCFromSpine() {
        global $lang; 
        $prefix = isset($lang['epub_chapter_prefix']) ? $lang['epub_chapter_prefix'] : 'Chapter ';

        foreach ($this->spine as $index => $item) {
            $this->toc[] = [
                'label' => $prefix . ($index + 1),
                'src' => $item['href'],
                'spine_index' => $index,
                'anchor' => ''
            ];
        }
    }

    public function getChapterContent($chapterIndex) {
        global $lang; 

        if (!isset($this->spine[$chapterIndex])) {
            return null; 
        }

        $href = $this->spine[$chapterIndex]['href'];
        $chapterPath = $this->resolvePath($href);

        if (!$chapterPath || !file_exists($chapterPath)) {
             // Fallback: coba cari di root folder
             $altPath = $this->extractedPath . '/' . basename($href);
             if(file_exists($altPath)){
                 $chapterPath = $altPath;
             } else {
                 $errTitle = isset($lang['epub_err_file_not_found_title']) ? $lang['epub_err_file_not_found_title'] : "File Not Found";
                 $errMsgFormat = isset($lang['epub_err_file_not_found_msg']) ? $lang['epub_err_file_not_found_msg'] : "File: %s is missing.";
                 return $this->createErrorHTML($errTitle, sprintf($errMsgFormat, $href));
             }
        }

        $content = file_get_contents($chapterPath);
        if (!$content) {
            $errTitle = isset($lang['epub_err_read_title']) ? $lang['epub_err_read_title'] : "Read Error";
            $errMsg = isset($lang['epub_err_read_msg']) ? $lang['epub_err_read_msg'] : "Cannot read file.";
            return $this->createErrorHTML($errTitle, $errMsg);
        }

        $basePath = dirname($href);
        if ($basePath == '.') $basePath = '';
        
        $content = $this->fixResourcePaths($content, $basePath);
        
        try {
            $content = $this->sanitizeContent($content);
        } catch (Exception $e) {
            error_log("Sanitize Error: " . $e->getMessage());
        }

        return $content;
    }

    private function sanitizeContent($html) {
        if (empty($html)) return "";

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new DOMXPath($dom);

        // HAPUS Tag Sampah
        $garbage = $xpath->query('//script | //style | //link | //meta | //head | //title');
        foreach ($garbage as $node) {
            $node->parentNode->removeChild($node);
        }

        // STRIPPING ATRIBUT
        $allElements = $xpath->query('//*');
        $allowedAttrs = ['src', 'href', 'id', 'name']; 

        foreach ($allElements as $element) {
            if (!$element->hasAttributes()) continue;

            $attrsToRemove = [];
            foreach ($element->attributes as $attrName => $attrNode) {
                if (!in_array(strtolower($attrName), $allowedAttrs)) {
                    $attrsToRemove[] = $attrName;
                }
            }
            foreach ($attrsToRemove as $attr) {
                $element->removeAttribute($attr);
            }
        }

        libxml_clear_errors();
        $body = $dom->getElementsByTagName('body')->item(0);
        if ($body) {
            $content = '';
            foreach ($body->childNodes as $node) {
                $content .= $dom->saveHTML($node);
            }
            return $content;
        }
        
        return $dom->saveHTML();
    }

    private function resolvePath($href) {
        $opfDir = rtrim($this->opfDir, '/');
        
        $candidates = [
            $opfDir . '/' . $href,
            $opfDir . '/' . urldecode($href),
            $this->extractedPath . '/' . $href
        ];
        
        foreach($candidates as $path) {
            $path = str_replace('\\', '/', $path);
            if(file_exists($path)) return $path;
        }
        return null;
    }

    private function fixResourcePaths($content, $basePath) {
        $serverRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        $opfDirFixed = str_replace('\\', '/', $this->opfDir);
        $webBaseUrl = str_replace($serverRoot, '', $opfDirFixed); 
        
        if(substr($webBaseUrl, 0, 1) !== '/') {
            $webBaseUrl = '/' . $webBaseUrl;
        }
        
        return preg_replace_callback('/(src|href)=("|\')([^"\']+)("|\')/i', function($m) use ($webBaseUrl, $basePath) {
            $attr = $m[1];
            $q = $m[2]; 
            $url = $m[3];
            
            if (preg_match('/^(http|https|mailto|data:|#|\/)/', $url)) return $m[0];

            $finalUrl = $webBaseUrl . ($basePath ? '/' . $basePath : '') . '/' . $url;
            $finalUrl = str_replace('//', '/', $finalUrl);
            
            return "$attr=$q$finalUrl$q";
        }, $content);
    }

    private function createErrorHTML($title, $msg) {
        return "<div style='padding:50px; text-align:center; color:red'><h2>⚠️ $title</h2><p>$msg</p></div>";
    }

    public function searchText($query) {
        $results = [];
        $query = trim($query);
        if(strlen($query) < 2) return [];

        foreach ($this->spine as $index => $chapter) {
            $href = $chapter['href'];
            $path = $this->resolvePath($href);
            if(!$path) continue;
            
            $rawContent = file_get_contents($path);
            $cleanText = strip_tags($rawContent);
            $cleanText = html_entity_decode($cleanText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $cleanText = preg_replace('/\s+/', ' ', $cleanText);
            
            $pos = stripos($cleanText, $query);
            if ($pos !== false) {
                $start = max(0, $pos - 50);
                $excerpt = substr($cleanText, $start, 150);
                $results[] = [
                    'chapter' => $index,
                    'excerpt' => '...' . trim($excerpt) . '...',
                    'title'   => $this->getChapterTitle($index)
                ];
                if(count($results) >= 20) break; 
            }
        }
        return $results;
    }

    public function getChapterTitle($index) {
        global $lang; 
        $prefix = isset($lang['epub_chapter_prefix']) ? $lang['epub_chapter_prefix'] : 'Chapter ';

        foreach($this->toc as $item) {
            if(isset($item['spine_index']) && $item['spine_index'] == $index) {
                return $item['label'];
            }
        }
        return $prefix . ($index + 1);
    }

    public function getMetadata() { return $this->metadata; }
    public function getTOC() { return $this->toc; }
    public function getSpine() { return $this->spine; }
    public function getTotalChapters() { return count($this->spine); }
}
?>