(SELECT ID, COALESCE(new.menu_order, old.menu_order) as menu_order
FROM wp_15_posts AS new
LEFT JOIN wp_15_posts_v1_0_0 AS old
USING (ID)
WHERE new.post_type LIKE "part" AND new.post_status LIKE "publish")

UNION

(SELECT ID, COALESCE(new.menu_order, old.menu_order) as menu_order
FROM wp_15_posts AS new
RIGHT JOIN wp_15_posts_v1_0_0 AS old
USING (ID)
WHERE old.post_type LIKE "part" AND old.post_status LIKE "publish")
ORDER BY menu_order