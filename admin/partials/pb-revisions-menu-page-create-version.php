<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$form_url = get_admin_url( get_current_blog_id(), '/admin.php?page=pb_revisions&noheader=true' )
?>
<div class="wrap">
	<h2>Create Revision</h2>
	<h1>What type of revision do you want to add?</h1>
	<div>
		<div>
			<h2><?php echo $data['major_number']; ?></h2>
			<h1>Major</h1>
			<p>Did you reorganized the book, changed the way the book is structured and how the content is presentend?</p>
			<p>Then you should create a Major Revision.</p>
			<form action="<?php echo $form_url ?>" method="POST">
				<input type="hidden" name="pb_revisions_type" value="major">
				<button type="submit" class="button button-hero button-primary generate" name="pb_revisions_action" value="create_version">Create <?php echo $data['major_number']; ?></button>
			</form>
		</div>
		<div>
			<h2><?php echo $data['minor_number']; ?></h2>
			<h1>Minor</h1>
			<p>Did you add some additional content within the existing structure?</p>
			<p>Then you should create a Minor Revision.</p>
			<form action="<?php echo $form_url ?>" method="POST">
				<input type="hidden" name="pb_revisions_type" value="minor">
				<button type="submit" class="button button-hero button-primary generate" name="pb_revisions_action" value="create_version">Create <?php echo $data['minor_number']; ?></button>
			</form>
		</div>
		<div>
			<h2><?php echo $data['patch_number']; ?></h2>
			<h1>Patch</h1>
			<p>Did you correct some errors, clarifie some parts or just remove some typos?</p>
			<p>Then you should create a Patch Revision.</p>
			<form action="<?php echo $form_url ?>" method="POST">
				<input type="hidden" name="pb_revisions_type" value="patch">
				<button type="submit" class="button button-hero button-primary generate" name="pb_revisions_action" value="create_version">Create <?php echo $data['patch_number']; ?></button>
			</form>
		</div>
	</div>
</div>