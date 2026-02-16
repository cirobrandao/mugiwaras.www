<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Models\Connector;

final class ConnectorsController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        
        // Permite acesso para upload, moderador e admin
        $canView = Auth::isUploader($user) || Auth::isModerator($user) || Auth::isAdmin($user) || Auth::isEquipe($user);
        if (!$canView) {
            Response::redirect(base_path('/dashboard'));
        }
        
        if (!Connector::isReady()) {
            echo $this->view('admin/connectors', [
                'items' => [],
                'csrf' => Csrf::token(),
                'setupError' => true,
                'canCreate' => Auth::isModerator($user) || Auth::isAdmin($user),
                'canDelete' => Auth::isAdmin($user),
            ]);
            return;
        }
        
        $items = Connector::all();
        
        echo $this->view('admin/connectors', [
            'items' => $items,
            'csrf' => Csrf::token(),
            'canCreate' => Auth::isModerator($user) || Auth::isAdmin($user),
            'canDelete' => Auth::isAdmin($user),
        ]);
    }

    public function detect(Request $request): void
    {
        header('Content-Type: application/json');
        
        $url = trim((string)($request->post['url'] ?? ''));
        
        if ($url === '') {
            echo json_encode(['error' => 'URL é obrigatória']);
            return;
        }
        
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            echo json_encode(['error' => 'URL inválida']);
            return;
        }
        
        // Try to detect WordPress theme
        $detection = $this->detectWordPressTheme($url);
        
        // Adicionar mensagem se não for confiança alta
        if (isset($detection['confidence']) && $detection['confidence'] !== 'high') {
            $detection['warning'] = 'Confiança baixa/média. Só é possível criar conectores com confiança alta.';
        }
        
        echo json_encode($detection);
    }

    public function create(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/connectors'));
        }
        
        // Verificar permissão: moderador ou admin
        $user = Auth::user();
        if (!$user || (!Auth::isModerator($user) && !Auth::isAdmin($user))) {
            Response::redirect(base_path('/admin/connectors?error=permission'));
        }
        
        $url = trim((string)($request->post['url'] ?? ''));
        $label = trim((string)($request->post['label'] ?? ''));
        $template = trim((string)($request->post['template'] ?? 'WordPressMadara'));
        $tagsRaw = trim((string)($request->post['tags'] ?? ''));
        $customSelector = trim((string)($request->post['custom_selector'] ?? ''));
        $customPath = trim((string)($request->post['custom_path'] ?? ''));
        
        if ($url === '' || $label === '') {
            Response::redirect(base_path('/admin/connectors?error=required'));
        }
        
        // Validar se o site é WordPress e tem confiança alta
        $detection = $this->detectWordPressTheme($url);
        if (isset($detection['error'])) {
            Response::redirect(base_path('/admin/connectors?error=notwp'));
        }
        if (!isset($detection['confidence']) || $detection['confidence'] !== 'high') {
            Response::redirect(base_path('/admin/connectors?error=lowconfidence'));
        }
        
        // Parse tags
        $tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
        if (empty($tags)) {
            $tags = ['manga'];
        }
        
        // Generate identifier and class name
        $identifier = Connector::generateIdentifier($url);
        $className = Connector::generateClassName($url);
        
        // Check if already exists
        if (Connector::findByName($identifier)) {
            Response::redirect(base_path('/admin/connectors?error=exists'));
        }
        
        // Build custom config
        $customConfig = [];
        if ($customSelector !== '') {
            $customConfig['queryTitleForURI'] = $customSelector;
        }
        if ($customPath !== '') {
            $customConfig['path'] = $customPath;
        }
        // Auto-detect path for MangaStream
        if ($template === 'WordPressMangaStream' && $customPath === '' && isset($detection['path'])) {
            $customConfig['path'] = $detection['path'];
        }
        
        // Generate code
        $code = $this->generateConnectorCode($className, $identifier, $label, $url, $template, $tags, $customConfig);
        
        $user = Auth::user();
        $userId = $user ? (int)$user['id'] : null;
        
        Connector::create(
            $identifier,
            $label,
            $url,
            $template,
            $className,
            $tags,
            $customConfig,
            $code,
            $userId
        );
        
        Response::redirect(base_path('/admin/connectors?created=1'));
    }

    public function update(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/connectors'));
        }
        
        // Verificar permissão: moderador ou admin
        $user = Auth::user();
        if (!$user || (!Auth::isModerator($user) && !Auth::isAdmin($user))) {
            Response::redirect(base_path('/admin/connectors?error=permission'));
        }
        
        $id = (int)($request->post['id'] ?? 0);
        $label = trim((string)($request->post['label'] ?? ''));
        $url = trim((string)($request->post['url'] ?? ''));
        $template = trim((string)($request->post['template'] ?? 'WordPressMadara'));
        $tagsRaw = trim((string)($request->post['tags'] ?? ''));
        $customSelector = trim((string)($request->post['custom_selector'] ?? ''));
        $customPath = trim((string)($request->post['custom_path'] ?? ''));
        
        if ($id <= 0 || $label === '' || $url === '') {
            Response::redirect(base_path('/admin/connectors?error=required'));
        }
        
        $connector = Connector::findById($id);
        if (!$connector) {
            Response::redirect(base_path('/admin/connectors?error=notfound'));
        }
        
        // Parse tags
        $tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
        if (empty($tags)) {
            $tags = ['manga'];
        }
        
        // Build custom config
        $customConfig = [];
        if ($customSelector !== '') {
            $customConfig['queryTitleForURI'] = $customSelector;
        }
        if ($customPath !== '') {
            $customConfig['path'] = $customPath;
        }
        
        // Regenerate code with existing class name and identifier
        $className = (string)$connector['class_name'];
        $identifier = (string)$connector['name'];
        $code = $this->generateConnectorCode($className, $identifier, $label, $url, $template, $tags, $customConfig);
        
        Connector::update($id, $label, $url, $template, $tags, $customConfig, $code);
        
        Response::redirect(base_path('/admin/connectors?updated=1'));
    }

    public function delete(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/connectors'));
        }
        
        // Verificar permissão: apenas admin
        $user = Auth::user();
        if (!$user || !Auth::isAdmin($user)) {
            Response::redirect(base_path('/admin/connectors?error=permission'));
        }
        
        $id = (int)($request->post['id'] ?? 0);
        
        if ($id > 0) {
            Connector::delete($id);
        }
        
        Response::redirect(base_path('/admin/connectors?deleted=1'));
    }

    public function download(Request $request): void
    {
        $id = (int)($request->get['id'] ?? 0);
        
        $connector = Connector::findById($id);
        if (!$connector) {
            Response::redirect(base_path('/admin/connectors?error=notfound'));
        }
        
        $className = (string)$connector['class_name'];
        $code = (string)$connector['generated_code'];
        
        header('Content-Type: application/javascript; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $className . '.mjs"');
        header('Content-Length: ' . strlen($code));
        header('Cache-Control: no-cache, must-revalidate');
        
        echo $code;
        exit;
    }

    public function downloadAll(Request $request): void
    {
        $connectors = Connector::all();
        
        if (empty($connectors)) {
            Response::redirect(base_path('/admin/connectors?error=empty'));
        }
        
        // Create a temporary zip file
        $zipFile = tempnam(sys_get_temp_dir(), 'connectors_');
        $zip = new \ZipArchive();
        
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            Response::redirect(base_path('/admin/connectors?error=zip'));
        }
        
        foreach ($connectors as $connector) {
            $className = (string)$connector['class_name'];
            $code = (string)$connector['generated_code'];
            $zip->addFromString($className . '.mjs', $code);
        }
        
        $zip->close();
        
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="connectors_' . date('Y-m-d_His') . '.zip"');
        header('Content-Length: ' . filesize($zipFile));
        header('Cache-Control: no-cache, must-revalidate');
        
        readfile($zipFile);
        unlink($zipFile);
        exit;
    }

    private function detectWordPressTheme(string $url): array
    {
        // Ensure URL has proper scheme
        if (!preg_match('~^https?://~i', $url)) {
            $url = 'https://' . $url;
        }
        
        // Normalize URL
        $parsed = parse_url($url);
        if (!$parsed || empty($parsed['host'])) {
            return ['error' => 'URL inválida'];
        }
        
        $baseUrl = ($parsed['scheme'] ?? 'https') . '://' . $parsed['host'];
        
        try {
            // Set up cURL with timeout
            $ch = curl_init($baseUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 3,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ]);
            
            $html = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200 || !$html) {
                return ['error' => 'Não foi possível acessar o site'];
            }
            
            $detected = [
                'isWordPress' => false,
                'theme' => 'unknown',
                'template' => 'WordPressMadara',
                'confidence' => 'low',
                'path' => '',
            ];
            
            // Check for WordPress - múltiplas verificações
            $wpIndicators = 0;
            if (stripos($html, 'wp-content') !== false) $wpIndicators++;
            if (stripos($html, 'wp-includes') !== false) $wpIndicators++;
            if (stripos($html, 'wordpress') !== false) $wpIndicators++;
            if (preg_match('~/wp-json/~i', $html)) $wpIndicators++;
            if (preg_match('~<meta[^>]+generator[^>]+WordPress~i', $html)) $wpIndicators += 2;
            
            if ($wpIndicators >= 2) {
                $detected['isWordPress'] = true;
                $detected['confidence'] = 'medium';
            } elseif ($wpIndicators === 1) {
                $detected['isWordPress'] = true;
                $detected['confidence'] = 'low';
            }
            
            // Se não for WordPress, retornar erro
            if (!$detected['isWordPress']) {
                return ['error' => 'Site não é WordPress'];
            }
            
            // Check for Madara theme
            $madaraIndicators = 0;
            if (stripos($html, 'madara') !== false) $madaraIndicators += 2;
            if (stripos($html, 'manga-action') !== false) $madaraIndicators++;
            if (stripos($html, 'c-chapter') !== false) $madaraIndicators++;
            if (stripos($html, 'post-title') !== false) $madaraIndicators++;
            if (preg_match('~/wp-content/themes/[^/]*madara/~i', $html)) $madaraIndicators += 2;
            
            if ($madaraIndicators >= 3) {
                $detected['theme'] = 'Madara';
                $detected['template'] = 'WordPressMadara';
                $detected['confidence'] = 'high';
            }
            
            // Check for MangaStream theme
            $streamIndicators = 0;
            if (stripos($html, 'mangastream') !== false) $streamIndicators += 2;
            if (preg_match('~/read/list-mode/?~i', $html)) $streamIndicators += 2;
            if (stripos($html, 'wp-manga') !== false) $streamIndicators++;
            if (preg_match('~class=["\'].*?manga.*?list.*?["\']~i', $html)) $streamIndicators++;
            if (preg_match('~/wp-content/themes/[^/]*mangastream/~i', $html)) $streamIndicators += 2;
            
            if ($streamIndicators >= 3) {
                $detected['theme'] = 'MangaStream';
                $detected['template'] = 'WordPressMangaStream';
                $detected['confidence'] = 'high';
                $detected['path'] = '/read/list-mode/';
            }
            
            // Try to extract theme name from HTML
            if (preg_match('~/themes/([^/\'"]+)~i', $html, $matches)) {
                $detected['themeFolder'] = $matches[1];
            }
            
            return $detected;
            
        } catch (\Throwable $e) {
            return ['error' => 'Erro ao detectar: ' . $e->getMessage()];
        }
    }

    private function generateConnectorCode(
        string $className,
        string $identifier,
        string $label,
        string $url,
        string $template,
        array $tags,
        array $customConfig
    ): string
    {
        $tagsJson = json_encode($tags);
        
        $code = "import {$template} from './templates/{$template}.mjs';\n\n";
        $code .= "export default class {$className} extends {$template} {\n\n";
        $code .= "    constructor() {\n";
        $code .= "        super();\n";
        $code .= "        super.id = '{$identifier}';\n";
        $code .= "        super.label = '{$label}';\n";
        $code .= "        this.tags = {$tagsJson};\n";
        $code .= "        this.url = '{$url}';\n";
        
        // MangaStream precisa do path antes do requestOptions
        if ($template === 'WordPressMangaStream' && isset($customConfig['path'])) {
            $pathValue = json_encode($customConfig['path']);
            $code .= "        this.path = {$pathValue};\n";
            unset($customConfig['path']); // Remove para não duplicar depois
        }
        
        // Só Madara usa x-referer
        if ($template === 'WordPressMadara') {
            $code .= "        this.requestOptions.headers.set('x-referer', this.url);\n";
        }
        
        // Add custom configurations
        foreach ($customConfig as $key => $value) {
            $valueJson = json_encode($value);
            $code .= "\n        this.{$key} = {$valueJson};\n";
        }
        
        $code .= "    }\n";
        $code .= "}\n";
        
        return $code;
    }
}
