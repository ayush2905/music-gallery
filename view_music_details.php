<?php 
require_once('./config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `music_list` where id = '{$_GET['id']}' and delete_flag = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }else{
		echo '<script>alert("Music ID is not valid."); location.replace("./?page=musics")</script>';
	}
}else{
	echo '<script>alert("Music ID is Required."); location.replace("./?page=musics")</script>';
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<style>
	.music-img{
		width:3em;
		height:3em;
		object-fit:cover;
		object-position:center center;
	}
	img#BannerViewer{
		height: 45vh;
		width: 100%;
		object-fit: scale-down;
		object-position:center center;
		/* border-radius: 100% 100%; */
	}
    .modal-content>.modal-footer{
        display:none;
    }
</style>
<div class="container-fluid">
    <div class="form-group d-flex justify-content-center">
        <img src="<?php echo validate_image((isset($banner_path) ? $banner_path : "")) ?>" alt="" id="BannerViewer" class="img-fluid img-thumbnail bg-dark border-dark">
    </div>
    <div class="form-group">
        <label for="title" class="control-label">Title:</label>
        <div class="pl-4"><?= isset($title) ? $title : "" ?></div>
    </div>
    <div class="form-group">
        <label for="artist" class="control-label">Artist</label>
        <div class="pl-4"><?= isset($artist) ? $artist : "" ?></div>
    </div>
    <div class="form-group">
        <label for="category_id" class="control-label">Category</label>
        <div class="pl-4"><?= isset($category_name) ? $category_name : "" ?></div>
    </div>
    <div class="form-group">
        <label for="description" class="control-label">Description</label>
        <div class="pl-4"><?= isset($description) ? str_replace("\n", "<br>", html_entity_decode($description)) : "" ?></div>
    </div>
    
    <div class="form-group">
        <label for="" class="control-label">Audio File</label>
        <?php if(isset($audio_path) && !empty($audio_path)): ?>
            <div class="pl-4">
                <audio src="<?= base_url.$audio_path ?>" controls></audio>
            </div>
            <div class="pl-4">
                <a href="<?= base_url.$audio_path ?>" target="_blank"><?= (pathinfo($audio_path, PATHINFO_FILENAME)).".".(pathinfo($audio_path, PATHINFO_EXTENSION))  ?></a>

            </div>
        <?php else: ?>
            <div class="pl-4"><span class="text-muted">No Audio File Added.</span></div>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label for="status" class="control-label">Status</label>
        <div class="pl-4"><span class="badge <?= isset($status) && $status == 1 ? "badge-success" : "" ?> rounded-pill px-4"><?= isset($status) && $status == 1 ? "Active" : "Inactive" ?></span></div>
    </div>
</div>
