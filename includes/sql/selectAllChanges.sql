
SELECT
    *,
    @j:=@j+1 AS pos
FROM
(
    SELECT
        chapter.*,
        COALESCE(chapter.menu_order_new, chapter.menu_order_old) AS menu_order,
        IF(COALESCE(chapter.post_type_new, chapter.post_type_old) = "front-matter", 0,
            IF(COALESCE(chapter.post_type_new, chapter.post_type_old) = "back-matter", 999999999,
                part.menu_order
            )
        ) AS part_menu_order
    FROM
        (
            SELECT
                revisions_chapter_ID AS ID,
                version,
                ID AS chapter,
                content_draft_hash,
                title_comment,
                comments,
                post_content_new,
                post_title_new,
                post_status_new,
                menu_order_new,
                post_type_new,
                post_content_old,
                post_title_old,
                post_status_old,
                menu_order_old,
                post_type_old,
                post_parent

            FROM
                (
                    SELECT
                        ID, 
                        new.post_content AS post_content_new, 
                        new.post_title AS post_title_new, 
                        new.post_status AS post_status_new, 
                        new.menu_order AS menu_order_new,  
                        new.post_type AS post_type_new, 
                        old.post_content AS post_content_old, 
                        old.post_title AS post_title_old, 
                        old.post_status AS post_status_old, 
                        old.menu_order AS menu_order_old,  
                        old.post_type AS post_type_old, 
                        COALESCE(new.post_parent, old.post_parent) AS post_parent
                    FROM
                        (
                            SELECT p.ID AS ID, p.post_content, p.post_title, p.post_status, p.menu_order, p.post_parent, p.post_type
                            FROM {$posts} AS p
                            WHERE FIND_IN_SET(post_type, "front-matter,chapter,back-matter") AND FIND_IN_SET(post_status, "publish,private,web-only")
                        ) AS new
                    LEFT JOIN
                        (
                            SELECT p.ID AS ID, p.post_content, p.post_title, p.post_status, p.menu_order, p.post_parent, p.post_type
                            FROM {$posts_prev} AS p
                            WHERE FIND_IN_SET(post_type, "front-matter,chapter,back-matter") AND FIND_IN_SET(post_status, "publish,private,web-only")
                        )AS old
                    USING (ID)

                    UNION

                    SELECT
                        ID, 
                        new.post_content AS post_content_new, 
                        new.post_title AS post_title_new, 
                        new.post_status AS post_status_new, 
                        new.menu_order AS menu_order_new,  
                        new.post_type AS post_type_new,  
                        old.post_content AS post_content_old, 
                        old.post_title AS post_title_old, 
                        old.post_status AS post_status_old, 
                        old.menu_order AS menu_order_old,  
                        old.post_type AS post_type_old, 
                        COALESCE(new.post_parent, old.post_parent) AS post_parent
                    FROM
                        (
                            SELECT p.ID AS ID, p.post_content, p.post_title, p.post_status, p.menu_order, p.post_parent, p.post_type
                            FROM {$posts} AS p
                            WHERE FIND_IN_SET(post_type, "front-matter,chapter,back-matter") AND FIND_IN_SET(post_status, "publish,private,web-only")
                        ) AS new
                    RIGHT JOIN
                        (
                            SELECT p.ID AS ID, p.post_content, p.post_title, p.post_status, p.menu_order, p.post_parent, p.post_type
                            FROM {$posts_prev} AS p
                            WHERE FIND_IN_SET(post_type, "front-matter,chapter,back-matter") AND FIND_IN_SET(post_status, "publish,private,web-only")
                        )AS old
                    USING (ID)
                )AS cont
            LEFT JOIN
                (
                    SELECT ID AS revisions_chapter_ID, version, chapter, content_draft_hash, title_comment, comments
                    FROM {$pb_revisions_chapter}
                    WHERE version = %d
                )AS com
            ON cont.ID = com.chapter
            WHERE 
                revisions_chapter_ID IS NOT NULL
                OR post_content_new != post_content_old
                OR (post_content_new IS NULL AND post_content_old IS NOT NULL)
                OR (post_content_new IS NOT NULL AND post_content_old IS NULL)
                OR post_title_new != post_title_old
                OR (post_title_new IS NULL AND post_title_old IS NOT NULL)
                OR (post_title_new IS NOT NULL AND post_title_old IS NULL)
                OR post_status_new != post_status_old
                OR (post_status_new IS NULL AND post_status_new IS NOT NULL)
                OR (post_status_new IS NOT NULL AND post_status_new IS NULL)
        ) as chapter
    LEFT JOIN
        (
            (SELECT ID, COALESCE(new.menu_order, old.menu_order) as menu_order
            FROM {$posts} AS new
            LEFT JOIN {$posts_prev} AS old
            USING (ID)
            WHERE new.post_type = "part" AND new.post_status = "publish")

            UNION

            (SELECT ID, COALESCE(new.menu_order, old.menu_order) as menu_order
            FROM {$posts} AS new
            RIGHT JOIN {$posts_prev} AS old
            USING (ID)
            WHERE old.post_type = "part" AND old.post_status = "publish")
        ) as part
    ON chapter.post_parent = part.ID
    ORDER BY part_menu_order, menu_order
) AS Total
