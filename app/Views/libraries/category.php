<?php
use App\Core\View;
ob_start();
$seriesCount = is_array($series ?? null) ? count($series) : 0;

// Separar séries destacadas e organizar o resto
$pinned = [];
$toOrganize = [];
$processedSeriesIds = [];

// Primeiro, separar destacadas
foreach ($series as $s) {
    $isPinned = (int)($s['pin_order'] ?? 0) > 0;
    if ($isPinned) {
        $pinned[] = $s;
        $processedSeriesIds[(int)$s['id']] = true;
    }
}

// Processar grupos e séries não destacadas
$processedGroups = [];
foreach ($series as $s) {
    if (isset($processedSeriesIds[(int)$s['id']])) {
        continue; // Já foi adicionada como destacada
    }
    
    $groupId = $s['group_id'] ?? null;
    
    if ($groupId && !isset($processedGroups[$groupId])) {
        // Primeira aparição deste grupo - coletar todas as séries dele
        $groupSeries = [];
        foreach ($series as $gs) {
            if (($gs['group_id'] ?? null) == $groupId && !isset($processedSeriesIds[(int)$gs['id']])) {
                $groupSeries[] = $gs;
                $processedSeriesIds[(int)$gs['id']] = true;
            }
        }
        
        $toOrganize[] = [
            'type' => 'group',
            'sort_key' => strtolower($s['group_name'] ?? ''),
            'data' => [
                'id' => $groupId,
                'name' => (string)($s['group_name'] ?? ''),
                'description' => (string)($s['group_description'] ?? ''),
                'is_collapsed' => (int)($s['group_is_collapsed'] ?? 0),
                'series' => $groupSeries
            ]
        ];
        $processedGroups[$groupId] = true;
    } elseif (!$groupId) {
        // Série sem grupo
        $toOrganize[] = [
            'type' => 'series',
            'sort_key' => strtolower($s['name'] ?? ''),
            'data' => $s
        ];
        $processedSeriesIds[(int)$s['id']] = true;
    }
}

// Ordenar alfabeticamente
usort($toOrganize, function($a, $b) {
    return strcmp($a['sort_key'], $b['sort_key']);
});
?>
<?php if (!empty($error)): ?>
    <div class="alert alert-warning"><?= View::e($error) ?></div>
<?php endif; ?>
<?php if (empty($series)): ?>
    <div class="alert alert-secondary">Nenhuma série encontrada.</div>
<?php else: ?>
    <?php $allowCbz = !empty($category['content_cbz']); ?>
    <?php $allowPdf = !empty($category['content_pdf']); ?>
    <?php $allowEpub = !empty($category['content_epub']); ?>
<div class="portal-container">
    <section class="category-content">
        <div class="category-header">
            <nav aria-label="breadcrumb" class="category-breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active" aria-current="page"><?= View::e($category['name'] ?? 'Categoria') ?></li>
                </ol>
            </nav>
        </div>
        <div class="category-list">
        <?php 
        // 1. Exibir séries destacadas primeiro
        foreach ($pinned as $s): ?>
            <?php include __DIR__ . '/_series_item.php'; ?>
        <?php endforeach; ?>
        
        <?php 
        // 2. Exibir grupos e séries misturados em ordem alfabética
        foreach ($toOrganize as $item):
            if ($item['type'] === 'group'):
                $group = $item['data'];
                $groupId = (int)$group['id'];
                $isCollapsed = (int)$group['is_collapsed'] === 1;
                $groupCollapseId = 'group-collapse-' . $groupId;
        ?>
            <div class="series-group">
                <div class="series-group-header" role="button" data-bs-toggle="collapse" data-bs-target="#<?= $groupCollapseId ?>" aria-expanded="<?= $isCollapsed ? 'false' : 'true' ?>" aria-controls="<?= $groupCollapseId ?>">
                    <div class="series-group-header-info">
                        <h3 class="series-group-title">
                            <i class="bi bi-layers"></i>
                            <?= View::e($group['name']) ?>
                        </h3>
                        <?php if (!empty($group['description'])): ?>
                            <p class="series-group-description"><?= View::e($group['description']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="series-group-toggle <?= $isCollapsed ? 'collapsed' : '' ?>">
                        <i class="bi bi-chevron-down"></i>
                    </div>
                </div>
                <div class="collapse <?= $isCollapsed ? '' : 'show' ?>" id="<?= $groupCollapseId ?>">
                    <div class="series-group-content">
                        <?php foreach ($group['series'] as $s): ?>
                            <?php include __DIR__ . '/_series_item.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: // type === 'series' ?>
            <?php $s = $item['data']; ?>
            <?php include __DIR__ . '/_series_item.php'; ?>
        <?php endif; ?>
        <?php endforeach; ?>
        </div>
    </section>
</div>
<?php endif; ?>

<?php if (!empty($user) && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user))): ?>
<script>
// Prevenir inicialização prematura dos modals
(function() {
    'use strict';
    
    // Aguardar o Bootstrap estar totalmente carregado
    if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        console.warn('Bootstrap não carregado ainda');
        return;
    }
    
    // Aguardar o DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModals);
    } else {
        initModals();
    }
    
    function initModals() {
        // Garantir que os modais funcionem corretamente
        const triggerButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
        
        triggerButtons.forEach(function(btn) {
            const targetId = btn.getAttribute('data-bs-target');
            if (!targetId) return;
            
            const modalEl = document.querySelector(targetId);
            if (!modalEl) return;
            
            // Adicionar listener para abrir o modal
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                try {
                    const modal = new bootstrap.Modal(modalEl, {
                        backdrop: true,
                        keyboard: true,
                        focus: true
                    });
                    modal.show();
                } catch (error) {
                    console.error('Erro ao abrir modal:', error);
                }
            }, false);
        });
    }
})();
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
