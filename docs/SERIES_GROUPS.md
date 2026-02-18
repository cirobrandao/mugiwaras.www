# Series Grouping System

## Overview

The series grouping system allows organizing related series into collapsible groups within categories. This is useful for grouping different editions or continuations of the same manga/comic (e.g., "Naruto", "Naruto Shippuden", "Naruto Gaiden").

## Database Schema

### Migration: `sql/014_series_groups.sql`

Creates `series_groups` table with:
- `id` (PRIMARY KEY)
- `category_id` (FOREIGN KEY to categories)
- `name` (group display name)
- `description` (optional description)
- `display_order` (INT, lower values appear first)
- `is_collapsed` (TINYINT, 1 = collapsed by default)
- `created_at`, `updated_at`

Alters `series` table:
- Adds `group_id` (FOREIGN KEY to series_groups, SET NULL on delete)
- Adds index on `group_id`

## Architecture

### Model Layer

**`app/Models/SeriesGroup.php`**

Key methods:
- `byCategory(int $categoryId)` - Get all groups in a category
- `findById(int $id)` - Get single group
- `create(array $data)` - Create new group
- `update(int $id, array $data)` - Update group
- `delete(int $id)` - Delete group (series remain, just ungrouped)
- `addSeries(int $groupId, int $seriesId)` - Add series to group
- `removeSeries(int $seriesId)` - Remove series from group
- `byCategoryWithCounts(int $categoryId)` - Get groups with series counts
- `toggleCollapsed(int $id)` - Toggle collapsed state
- `reorder(int $id, int $order)` - Change display order

**`app/Models/Series.php`**

New methods:
- `byCategoryWithCountsTypesAndGroups(int $categoryId)` - Main query that JOINs series_groups data
- `updateGroup(int $seriesId, ?int $groupId)` - Assign series to group
- `byGroup(int $groupId)` - Get all series in a group
- `withoutGroupByCategory(int $categoryId)` - Get ungrouped series

### Controller Layer

**`app/Controllers/Admin/SeriesGroupsController.php`**

Full CRUD operations:
- `index()` - List all groups with series counts
- `create()` - Show create form
- `store()` - Save new group
- `edit(int $id)` - Show edit form with series management
- `update(int $id)` - Save group changes
- `delete(int $id)` - Delete group
- `addSeries(int $id)` - Add series to group
- `removeSeries(int $id)` - Remove series from group
- `reorder(int $id)` - Change display order
- `toggleCollapsed(int $id)` - Toggle collapsed state

**`app/Controllers/LibraryController.php`**

Updated to use `byCategoryWithCountsTypesAndGroups()` for retrieving series with group data.

### View Layer

**`app/Views/libraries/category.php`**

Main category view with grouping logic:
- Separates series into `$grouped` and `$ungrouped` arrays
- Displays groups first (ordered by `display_order`)
- Uses Bootstrap collapse for expand/collapse functionality
- Includes `_series_item.php` for rendering individual series

**`app/Views/libraries/_series_item.php`**

Extracted series item template (reusable):
- Displays series card with all info (title, badges, formats, admin actions)
- Includes edit and delete modals
- Supports favorite functionality
- Shows pin order and 18+ toggle

**`app/Views/admin/series_groups/index.php`**

Admin list view:
- Table showing all groups with counts
- Inline order editing
- Toggle collapsed state
- Edit and delete actions
- Delete confirmation modals

**`app/Views/admin/series_groups/create.php`**

Create form:
- Category selection
- Group name (required)
- Description (optional)
- Display order
- Collapsed state checkbox

**`app/Views/admin/series_groups/edit.php`**

Edit form with series management:
- Edit group properties
- List current series in group
- Add series from dropdown (only ungrouped series shown)
- Remove series from group

### Styles

**`public/assets/css/theme.css`**

Added comprehensive styling:
- `.series-group` - Group container
- `.series-group-header` - Clickable header with gradient
- `.series-group-title` - Group name styling
- `.series-group-description` - Description text
- `.series-group-toggle` - Circular toggle button with rotate animation
- `.series-group-content` - Collapsible content area
- Dark mode variants
- Mobile responsive breakpoints

### Routes

**`public/index.php`**

Added routes (all require `requireAdmin()` middleware):
```php
GET  /admin/series-groups
GET  /admin/series-groups/create
POST /admin/series-groups/store
GET  /admin/series-groups/{id}/edit
POST /admin/series-groups/{id}/update
POST /admin/series-groups/{id}/delete
POST /admin/series-groups/{id}/add-series
POST /admin/series-groups/{id}/remove-series
POST /admin/series-groups/{id}/reorder
POST /admin/series-groups/{id}/toggle-collapsed
```

## Usage

### For Administrators

1. **Access Management**
   - Navigate to Admin Dashboard → "Grupos de Séries"
   - Or directly: `/admin/series-groups`

2. **Create Group**
   - Click "Novo Grupo"
   - Select category
   - Enter group name (e.g., "Naruto - Série Completa")
   - Optionally add description
   - Set display order (0 = first)
   - Choose collapsed state

3. **Add Series to Group**
   - Click edit icon on group
   - Select series from dropdown (only ungrouped series shown)
   - Click "Adicionar"

4. **Manage Group**
   - Reorder: Change number in "Ordem" column
   - Toggle collapsed: Click collapse/expand button
   - Edit: Click pencil icon
   - Delete: Click trash icon (series remain, just ungrouped)

### For Users

Groups appear in library categories:
- Grouped series appear first (by display order)
- Click group header to expand/collapse
- Individual series work normally within groups
- Ungrouped series appear below all groups

## Database Sorting

Series are sorted in this order:
```sql
ORDER BY 
  CASE WHEN sg.id IS NULL THEN 1 ELSE 0 END,  -- Groups first
  sg.display_order ASC,                        -- Group order
  s.name ASC                                   -- Series name
```

## Features

✅ **Collapsible Groups** - Save screen space with collapse/expand
✅ **Custom Ordering** - Control display order of groups
✅ **Category-Scoped** - Groups belong to specific categories
✅ **Soft Delete** - Deleting group doesn't delete series
✅ **Dark Mode** - Full dark theme support
✅ **Mobile Responsive** - Works on all screen sizes
✅ **CSRF Protection** - All forms protected
✅ **Admin Only** - Requires admin authentication
✅ **Audit Trail** - Created/updated timestamps

## Future Enhancements

Possible improvements:
- Drag-and-drop reordering
- Bulk series assignment
- Group templates
- Group icons/images
- Multi-category groups
- Group statistics/analytics
- Export/import groups

## Installation

1. Apply migration:
   ```bash
   mysql -u user -p database < sql/014_series_groups.sql
   ```

2. Access admin panel and create first group

3. Add series to group

4. View category page to see grouped series

## Technical Notes

- Groups use Bootstrap 5 collapse component
- Toggle animation uses CSS transform rotate
- Series item template extracted to `_series_item.php` for reusability
- JOIN query efficient with indexed columns
- Supports NULL group_id (ungrouped series)
- Foreign keys maintain referential integrity
