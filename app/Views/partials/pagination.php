<?php
/**
 * Partial para renderizar paginação com ellipsis
 * 
 * Variáveis esperadas:
 * - $currentPage: Página atual (int)
 * - $totalPages: Total de páginas (int)
 * - $baseUrl: URL base sem query string (string)
 * - $queryParams: Array de parâmetros adicionais (array, opcional)
 */

use App\Core\View;

$currentPage = (int)($currentPage ?? 1);
$totalPages = (int)($totalPages ?? 1);
$baseUrl = (string)($baseUrl ?? '');
$queryParams = (array)($queryParams ?? []);

if ($totalPages <= 1) {
    return;
}

// Função para construir URL com parâmetros
$buildUrl = function(int $page) use ($baseUrl, $queryParams): string {
    $params = $queryParams;
    $params['page'] = $page;
    $query = http_build_query($params);
    return $baseUrl . ($query ? '?' . $query : '');
};

// Lógica de paginação: mostrar no máximo 7 itens
// Padrão: [Prev] 1 2 3 ... 48 49 50 [Next]
$range = [];
$delta = 2; // Quantas páginas mostrar ao redor da atual

if ($totalPages <= 7) {
    // Mostrar todas se forem poucas
    $range = range(1, $totalPages);
} else {
    // Paginação com ellipsis
    $range = [1]; // Sempre mostrar primeira
    
    $start = max(2, $currentPage - $delta);
    $end = min($totalPages - 1, $currentPage + $delta);
    
    // Adicionar ellipsis inicial se necessário
    if ($start > 2) {
        $range[] = '...';
    }
    
    // Adicionar páginas do meio
    for ($i = $start; $i <= $end; $i++) {
        $range[] = $i;
    }
    
    // Adicionar ellipsis final se necessário
    if ($end < $totalPages - 1) {
        $range[] = '...';
    }
    
    // Sempre mostrar última
    $range[] = $totalPages;
}
?>

<nav aria-label="Navegação de páginas" class="mt-3">
    <ul class="pagination pagination-sm justify-content-center flex-wrap">
        <?php if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $buildUrl($currentPage - 1) ?>" aria-label="Anterior">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link" aria-label="Anterior">
                    <span aria-hidden="true">&laquo;</span>
                </span>
            </li>
        <?php endif; ?>

        <?php foreach ($range as $item): ?>
            <?php if ($item === '...'): ?>
                <li class="page-item disabled d-none d-sm-inline">
                    <span class="page-link">...</span>
                </li>
            <?php else: ?>
                <?php $pageNum = (int)$item; ?>
                <li class="page-item <?= $pageNum === $currentPage ? 'active' : '' ?>">
                    <?php if ($pageNum === $currentPage): ?>
                        <span class="page-link"><?= $pageNum ?></span>
                    <?php else: ?>
                        <a class="page-link" href="<?= $buildUrl($pageNum) ?>"><?= $pageNum ?></a>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $buildUrl($currentPage + 1) ?>" aria-label="Próxima">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link" aria-label="Próxima">
                    <span aria-hidden="true">&raquo;</span>
                </span>
            </li>
        <?php endif; ?>
    </ul>
</nav>
