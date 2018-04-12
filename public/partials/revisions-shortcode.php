<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$store = new \PBRevisions\includes\Store();
$version_number = $store->get_active_version_number();
$versions = $store->get_versions_with_chapters_up_to($store->get_active_version_number());
$date_format = get_option( 'date_format' );

function pb_revision_the_content($content){
	$content = apply_filters( 'the_content', $content );
    echo str_replace( ']]>', ']]&gt;', $content );
}
?>
<?php foreach ( array_reverse($versions) as $version ) : ?>
    <section class="pb_revisions_version">
        <h2>Version <?php echo $version->number?></h2>
        <div class="pb_revisions_version__date"><?php echo get_date_from_gmt($version->date, $date_format); ?></div>
        <?php if(!empty($version->comment)){?>
            <div class="pb_revisions_version__summary">
                <?php pb_revision_the_content($version->comment)?>
            </div>
        <?php }?>
        <?php if(!empty($version->chapters)){?>
            <div class="pb_revisions_version__chapters">
                <?php foreach ( $version->chapters as $chapter ) : ?>
                    <div class="pb_revisions_version__chapter">
                        <?php $url = esc_url( get_permalink( $chapter->chapter ) );
                            if(empty($url) || !$chapter->web_statuts_new()){ 
                        ?>
                            <h3>Chapter: <?php echo $chapter->title()?></h3>
                        <?php }else{ ?>
                            <h3><a href="<?php echo $url?>">Chapter: <?php echo $chapter->title()?></a></h3>
                        <?php } ?>
                        <?php pb_revision_the_content($chapter->title_comment)?>
                        <?php foreach ( $chapter->comments as $comment ) : ?>
                            <?php pb_revision_the_content($comment)?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php }?>
    </section>
<?php endforeach; ?>