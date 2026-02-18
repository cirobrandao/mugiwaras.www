<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Response;
use App\Models\SeriesGroup;
use App\Models\Series;
use App\Models\Category;

final class SeriesGroupsController extends Controller
{
    /**
     * Lista todos os grupos de todas as categorias
     */
    public function index(): void
    {
        $user = Auth::user();
        if (!Auth::isAdmin($user) && !Auth::isModerator($user)) {
            Response::redirect(base_path('/dashboard'));
            return;
        }

        $groups = SeriesGroup::allWithCounts();

        echo $this->view('admin/series_groups/index', [
            'groups' => $groups,
        ]);
    }

    /**
     * Formulário para criar grupo
     */
    public function create(): void
    {
        $user = Auth::user();
        if (!Auth::isAdmin($user) && !Auth::isModerator($user)) {
            Response::redirect(base_path('/dashboard'));
            return;
        }

        $categories = Category::all();

        echo $this->view('admin/series_groups/create', [
            'categories' => $categories,
            'formData' => $_SESSION['form_data'] ?? [],
            'errors' => $_SESSION['errors'] ?? [],
        ]);
        
        unset($_SESSION['form_data'], $_SESSION['errors']);
    }

    /**
     * Salva novo grupo
     */
    public function store(): void
    {
        $user = Auth::user();
        if (!Auth::isAdmin($user) && !Auth::isModerator($user)) {
            Response::redirect(base_path('/dashboard'));
            return;
        }

        $categoryId = (int)($_POST['category_id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $displayOrder = (int)($_POST['display_order'] ?? 0);
        $isCollapsed = !empty($_POST['is_collapsed']) ? 1 : 0;

        $errors = [];

        if ($categoryId <= 0) {
            $errors[] = 'Selecione uma categoria';
        }
        if ($name === '') {
            $errors[] = 'Nome do grupo é obrigatório';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            Response::redirect(base_path('/admin/series-groups/create'));
            return;
        }

        $category = Category::findById($categoryId);
        if (!$category) {
            $_SESSION['errors'] = ['Categoria não encontrada'];
            $_SESSION['form_data'] = $_POST;
            Response::redirect(base_path('/admin/series-groups/create'));
            return;
        }

        // Verifica se já existe grupo com esse nome
        $existing = SeriesGroup::findByName($categoryId, $name);
        if ($existing) {
            $_SESSION['errors'] = ['Já existe um grupo com esse nome nesta categoria'];
            $_SESSION['form_data'] = $_POST;
            Response::redirect(base_path('/admin/series-groups/create'));
            return;
        }

        try {
            $groupId = SeriesGroup::create($categoryId, $name, $description !== '' ? $description : null, $displayOrder);
            
            // Atualiza is_collapsed se necessário
            if ($isCollapsed) {
                SeriesGroup::update($groupId, $name, $description !== '' ? $description : null, $displayOrder, 1);
            }
            
            $_SESSION['flash_success'] = 'Grupo criado com sucesso';
            Response::redirect(base_path('/admin/series-groups'));
        } catch (\Throwable $e) {
            $_SESSION['errors'] = ['Erro ao criar grupo: ' . $e->getMessage()];
            $_SESSION['form_data'] = $_POST;
            Response::redirect(base_path('/admin/series-groups/create'));
        }
    }

    /**
     * Formulário para editar grupo
     */
    public function edit(\App\Core\Request $request, string $id): void
    {
        $user = Auth::user();
        if (!Auth::isAdmin($user) && !Auth::isModerator($user)) {
            Response::redirect(base_path('/dashboard'));
            return;
        }

        $groupId = (int)$id;
        $group = SeriesGroup::findById($groupId);
        if (!$group) {
            $_SESSION['flash_error'] = 'Grupo não encontrado';
            Response::redirect(base_path('/admin/series-groups'));
            return;
        }

        $categories = Category::all();
        $groupSeries = Series::byGroup($groupId);
        $availableSeries = Series::withoutGroupByCategory((int)$group['category_id']);

        echo $this->view('admin/series_groups/edit', [
            'group' => $group,
            'categories' => $categories,
            'groupSeries' => $groupSeries,
            'availableSeries' => $availableSeries,
            'errors' => $_SESSION['errors'] ?? [],
        ]);
        
        unset($_SESSION['errors']);
    }

    /**
     * Atualiza grupo
     */
    public function update(\App\Core\Request $request, string $id): void
    {
        $user = Auth::user();
        if (!Auth::isAdmin($user) && !Auth::isModerator($user)) {
            Response::redirect(base_path('/dashboard'));
            return;
        }

        $groupId = (int)$id;
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $displayOrder = (int)($_POST['display_order'] ?? 0);
        $isCollapsed = !empty($_POST['is_collapsed']) ? 1 : 0;

        $errors = [];
        if ($categoryId <= 0) {
            $errors[] = 'Selecione uma categoria';
        }
        if ($name === '') {
            $errors[] = 'Nome do grupo é obrigatório';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            Response::redirect(base_path('/admin/series-groups/' . $groupId . '/edit'));
            return;
        }

        $group = SeriesGroup::findById($groupId);
        if (!$group) {
            $_SESSION['flash_error'] = 'Grupo não encontrado';
            Response::redirect(base_path('/admin/series-groups'));
            return;
        }

        try {
            // Se mudou de categoria, remove todas as séries do grupo
            if ((int)$group['category_id'] !== $categoryId) {
                $db = \App\Core\Database::connection();
                $stmt = $db->prepare('UPDATE series SET group_id = NULL WHERE group_id = :gid');
                $stmt->execute(['gid' => $groupId]);
                
                // Atualiza a categoria do grupo
                $stmt = $db->prepare('UPDATE series_groups SET category_id = :cid WHERE id = :gid');
                $stmt->execute(['cid' => $categoryId, 'gid' => $groupId]);
            }
            
            SeriesGroup::update($groupId, $name, $description !== '' ? $description : null, $displayOrder, $isCollapsed);
            $_SESSION['flash_success'] = 'Grupo atualizado com sucesso';
            Response::redirect(base_path('/admin/series-groups/' . $groupId . '/edit'));
        } catch (\Throwable $e) {
            $_SESSION['errors'] = ['Erro ao atualizar grupo: ' . $e->getMessage()];
            Response::redirect(base_path('/admin/series-groups/' . $groupId . '/edit'));
        }
    }

    /**
     * Deleta grupo
     */
    public function delete(\App\Core\Request $request, string $id): void
    {
        $user = Auth::user();
        if (!Auth::isAdmin($user) && !Auth::isModerator($user)) {
            Response::redirect(base_path('/dashboard'));
            return;
        }

        $groupId = (int)$id;
        $group = SeriesGroup::findById($groupId);
        if (!$group) {
            $_SESSION['flash_error'] = 'Grupo não encontrado';
            Response::redirect(base_path('/admin/series-groups'));
            return;
        }

        try {
            SeriesGroup::delete($groupId);
            $_SESSION['flash_success'] = 'Grupo deletado com sucesso';
            Response::redirect(base_path('/admin/series-groups'));
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Erro ao deletar grupo: ' . $e->getMessage();
            Response::redirect(base_path('/admin/series-groups'));
        }
    }

    /**
     * Adiciona série a um grupo
     */
    public function addSeries(\App\Core\Request $request, string $id): void
    {
        $user = Auth::user();
        if (!Auth::isAdmin($user) && !Auth::isModerator($user)) {
            Response::redirect(base_path('/dashboard'));
            return;
        }

        $groupId = (int)$id;
        $seriesId = (int)($_POST['series_id'] ?? 0);

        if ($seriesId <= 0) {
            $_SESSION['flash_error'] = 'Série inválida';
            Response::redirect(base_path('/admin/series-groups/' . $groupId . '/edit'));
            return;
        }

        try {
            Series::updateGroup($seriesId, $groupId);
            $_SESSION['flash_success'] = 'Série adicionada ao grupo';
            Response::redirect(base_path('/admin/series-groups/' . $groupId . '/edit'));
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Erro ao adicionar série: ' . $e->getMessage();
            Response::redirect(base_path('/admin/series-groups/' . $groupId . '/edit'));
        }
    }

    /**
     * Remove série de um grupo
     */
    public function removeSeries(\App\Core\Request $request, string $id): void
    {
        $user = Auth::user();
        if (!Auth::isAdmin($user) && !Auth::isModerator($user)) {
            Response::redirect(base_path('/dashboard'));
            return;
        }

        $groupId = (int)$id;
        $seriesId = (int)($_POST['series_id'] ?? 0);

        if ($seriesId <= 0) {
            $_SESSION['flash_error'] = 'Série inválida';
            Response::redirect(base_path('/admin/series-groups/' . $groupId . '/edit'));
            return;
        }

        try {
            Series::updateGroup($seriesId, null);
            $_SESSION['flash_success'] = 'Série removida do grupo';
            Response::redirect(base_path('/admin/series-groups/' . $groupId . '/edit'));
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Erro ao remover série: ' . $e->getMessage();
            Response::redirect(base_path('/admin/series-groups/' . $groupId . '/edit'));
        }
    }

    /**
     * Reordena grupo
     */
    public function reorder(\App\Core\Request $request, string $id): void
    {
        $user = Auth::user();
        if (!Auth::isAdmin($user) && !Auth::isModerator($user)) {
            Response::redirect(base_path('/dashboard'));
            return;
        }

        $groupId = (int)$id;
        $displayOrder = (int)($_POST['display_order'] ?? 0);

        $group = SeriesGroup::findById($groupId);
        if (!$group) {
            $_SESSION['flash_error'] = 'Grupo não encontrado';
            Response::redirect(base_path('/admin/series-groups'));
            return;
        }

        try {
            $db = \App\Core\Database::connection();
            $stmt = $db->prepare('UPDATE series_groups SET display_order = :o WHERE id = :id');
            $stmt->execute(['o' => $displayOrder, 'id' => $groupId]);
            
            $_SESSION['flash_success'] = 'Ordem atualizada';
            Response::redirect(base_path('/admin/series-groups'));
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Erro ao reordenar: ' . $e->getMessage();
            Response::redirect(base_path('/admin/series-groups'));
        }
    }

    /**
     * Alterna estado de colapso
     */
    public function toggleCollapsed(\App\Core\Request $request, string $id): void
    {
        $user = Auth::user();
        if (!Auth::isAdmin($user) && !Auth::isModerator($user)) {
            Response::redirect(base_path('/dashboard'));
            return;
        }

        $groupId = (int)$id;
        $group = SeriesGroup::findById($groupId);
        if (!$group) {
            $_SESSION['flash_error'] = 'Grupo não encontrado';
            Response::redirect(base_path('/admin/series-groups'));
            return;
        }

        try {
            SeriesGroup::toggleCollapsed($groupId);
            $_SESSION['flash_success'] = 'Estado alterado';
            Response::redirect(base_path('/admin/series-groups'));
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Erro: ' . $e->getMessage();
            Response::redirect(base_path('/admin/series-groups'));
        }
    }
}
