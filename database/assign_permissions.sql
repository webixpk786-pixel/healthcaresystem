INSERT INTO role_module_permissions (
    role_id, module_id, can_view, can_create, can_edit, can_delete, created_at, updated_at
)
SELECT
    r.id AS role_id,
    m.id AS module_id,
    1 AS can_view,
    1 AS can_create,
    1 AS can_edit,
    1 AS can_delete,
    NOW() AS created_at,
    NOW() AS updated_at
FROM
    roles r
JOIN
    modules m ON 1 = 1
LEFT JOIN
    role_module_permissions p ON p.role_id = r.id AND p.module_id = m.id
WHERE
    r.id_deleted = 0
    AND m.id_deleted = 0
    AND p.id IS NULL;
