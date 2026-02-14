<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Cross-domain authentication helper
 * Permite manter autenticação ao transitar entre APP_URL e APP_UPLOAD_URL
 */
final class CrossDomainAuth
{
    private const TOKEN_LIFETIME = 30; // 30 segundos para transição
    
    /**
     * Gera um token temporário para transição de domínio
     * @param int $userId ID do usuário autenticado
     * @return string Token de transição
     */
    public static function generateTransitionToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $expires = time() + self::TOKEN_LIFETIME;
        
        $data = json_encode([
            'user_id' => $userId,
            'expires' => $expires,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
        
        // Store in session for verification
        $_SESSION['_transition_tokens'][$token] = $data;
        
        // Cleanup expired tokens
        self::cleanupExpiredTokens();
        
        return $token;
    }
    
    /**
     * Valida e consome um token de transição
     * @param string $token Token a validar
     * @return int|null User ID se válido, null se inválido
     */
    public static function validateTransitionToken(string $token): ?int
    {
        if (empty($_SESSION['_transition_tokens'][$token])) {
            return null;
        }
        
        $data = json_decode($_SESSION['_transition_tokens'][$token], true);
        if (!$data) {
            unset($_SESSION['_transition_tokens'][$token]);
            return null;
        }
        
        // Check expiration
        if (time() > ($data['expires'] ?? 0)) {
            unset($_SESSION['_transition_tokens'][$token]);
            return null;
        }
        
        // Verify IP and UA for security
        $currentIp = $_SERVER['REMOTE_ADDR'] ?? '';
        $currentUa = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if ($currentIp !== ($data['ip'] ?? '') || $currentUa !== ($data['ua'] ?? '')) {
            unset($_SESSION['_transition_tokens'][$token]);
            return null;
        }
        
        // Token is valid, consume it (single use)
        $userId = (int)($data['user_id'] ?? 0);
        unset($_SESSION['_transition_tokens'][$token]);
        
        return $userId > 0 ? $userId : null;
    }
    
    /**
     * Limpa tokens expirados da sessão
     */
    private static function cleanupExpiredTokens(): void
    {
        if (empty($_SESSION['_transition_tokens'])) {
            return;
        }
        
        $now = time();
        foreach ($_SESSION['_transition_tokens'] as $token => $dataJson) {
            $data = json_decode($dataJson, true);
            if (!$data || $now > ($data['expires'] ?? 0)) {
                unset($_SESSION['_transition_tokens'][$token]);
            }
        }
    }
    
    /**
     * Verifica se deve usar URL de upload com token
     * @param string $path Path da rota
     * @return bool
     */
    public static function shouldUseUploadDomain(string $path): bool
    {
        $appUrl = rtrim((string)config('app.url', ''), '/');
        $uploadUrl = rtrim((string)config('app.upload_url', ''), '/');
        
        if ($appUrl === '' || $uploadUrl === '' || $appUrl === $uploadUrl) {
            return false;
        }
        
        // Apenas para rotas de upload e proof
        return preg_match('#^/(upload|loja/(proof|request))($|/|\?)#', $path) === 1;
    }
    
    /**
     * Constrói URL de upload com token de transição se necessário
     * @param string $path Path da rota
     * @return string URL completa
     */
    public static function buildUploadUrl(string $path): string
    {
        if (!self::shouldUseUploadDomain($path)) {
            return url($path);
        }
        
        $uploadUrl = rtrim((string)config('app.upload_url', ''), '/');
        $basePath = rtrim((string)config('app.base_path', ''), '/');
        $path = '/' . ltrim($path, '/');
        
        $user = Auth::user();
        if (!$user) {
            return $uploadUrl . $basePath . $path;
        }
        
        // Gera token de transição
        $token = self::generateTransitionToken((int)$user['id']);
        
        // Adiciona token à URL
        $separator = strpos($path, '?') !== false ? '&' : '?';
        return $uploadUrl . $basePath . $path . $separator . '_t=' . $token;
    }
}
