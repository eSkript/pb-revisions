SELECT
    chapter.*,
    COALESCE(chapter.menu_order_new, chapter.menu_order_old) AS menu_order,
    IF(COALESCE(chapter.post_type_new, chapter.post_type_old) LIKE "front-matter", 0,
        IF(COALESCE(chapter.post_type_new, chapter.post_type_old) LIKE "back-matter", 999999999,
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
            p.post_type AS post_type_new,
            m.meta_value AS pb_export_new,
            pp.post_content AS post_content_old,
            pp.post_title AS post_title_old,
            pp.post_status AS post_status_old,
            pp.menu_order AS menu_order_old,
            pp.post_type AS post_type_old,
            mp.meta_value AS pb_export_old,
            COALESCE(p.post_parent, pp.post_parent) AS post_parent
        FROM {$pb_revisions_chapter} AS c
        LEFT JOIN {$posts} AS p
        ON c.chapter = p.ID
        LEFT JOIN {$postmeta} AS m
        ON c.chapter = m.post_id AND m.meta_key LIKE 'pb_export'
        LEFT JOIN {$posts_prev} AS pp
        ON c.chapter = pp.ID
        LEFT JOIN {$postmeta_prev} AS mp
        ON c.chapter = mp.post_id AND mp.meta_key LIKE 'pb_export'
        WHERE c.version = %d
    ) as chapter
LEFT JOIN
    (
        (SELECT ID, COALESCE(new.menu_order, old.menu_order) as menu_order
        FROM {$posts} AS new
        LEFT JOIN {$posts_prev} AS old
        USING (ID)
        WHERE new.post_type LIKE "part" AND new.post_status LIKE "publish")

        UNION

        (SELECT ID, COALESCE(new.menu_order, old.menu_order) as menu_order
        FROM {$posts} AS new
        RIGHT JOIN {$posts_prev} AS old
        USING (ID)
        WHERE old.post_type LIKE "part" AND old.post_status LIKE "publish")
    ) as part
ON chapter.post_parent = part.ID
WHERE chapter.post_parent IS NOT NULL
ORDER BY part_menu_order, menu_order