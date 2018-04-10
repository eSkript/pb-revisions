SELECT
    chapter.*,
    chapter.menu_order_new AS menu_order,
    IF(chapter.post_type_new LIKE "front-matter", 0,
        IF(chapter.post_type_new LIKE "back-matter", 999999999,
            part.menu_order
        )
    ) AS part_menu_order
FROM
    (
        SELECT
            c.*,
            p.post_content AS post_content_new,
            p.post_title AS post_title_new,
            p.post_status AS post_status_new,
            p.menu_order AS menu_order_new,
            p.post_parent AS post_parent_new,
            p.post_type AS post_type_new,
            m.meta_value AS pb_export_new,
            p.post_parent AS post_parent
        FROM {$pb_revisions_chapter} AS c
        LEFT JOIN {$posts} AS p
        ON c.chapter = p.ID
        LEFT JOIN {$postmeta} AS m
        ON c.chapter = m.post_id AND m.meta_key LIKE 'pb_export'
        WHERE c.version = %d
    ) as chapter
LEFT JOIN
    (
        SELECT ID, menu_order
        FROM {$posts} as new
        WHERE new.post_type LIKE "part" AND new.post_status LIKE "publish"
    ) as part
ON chapter.post_parent = part.ID
WHERE chapter.post_parent IS NOT NULL
ORDER BY part_menu_order, menu_order