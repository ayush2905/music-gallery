<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `music_list` where id = '{$_GET['id']}' and `delete_flag` = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<style>
	.music-img{
		width:3em;
		height:3em;
		object-fit:cover;
		object-position:center center;
	}
	img#BannerViewer{
		height: 30vh;
		width: 100%;
		object-fit: scale-down;
		object-position:center center;
		/* border-radius: 100% 100%; */
	}
</style>
<div class="card card-outline rounded-0 card-purple">
	<div class="card-header">
		<h3 class="card-title"><?= isset($id) ? "Update Music Details" : "Add New Music Entry" ?></h3>
		<div class="card-tools">
			<a href="<?= base_url."admin/?page=musics"?>" class="btn btn-flat btn-light bg-light"><span class="fas fa-angle-left"></span>  Back to List</a>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<form action="" id="music-form">
				<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
				<div class="form-group">
					<label for="title" class="control-label">Title</label>
					<input type="text" name="title" id="title" class="form-control form-control-sm rounded-0" value="<?php echo isset($title) ? $title : ''; ?>"  required/>
				</div>
				<div class="form-group">
					<label for="artist" class="control-label">Artist</label>
					<input type="text" name="artist" id="artist" class="form-control form-control-sm rounded-0" value="<?php echo isset($artist) ? $artist : ''; ?>"  required/>
				</div>
				<div class="form-group">
					<label for="category_id" class="control-label">Category</label>
					<select name="category_id" id="category_id" class="form-control form-control-sm rounded-0" value="<?php echo isset($category_id) ? $category_id : ''; ?>"  required>
						<option value="" disabled <?= (!isset($category_id) ? "selected" : "") ?>></option>
						<?php 
						$category_qry = $conn->query("SELECT * FROM `category_list` where (`status` = 1 and `delete_flag` = 0) ".(isset($category_id) ? " OR `id` = '{$category_id}'" : ""));
						while($row = $category_qry->fetch_assoc()):
						?>
						<option value="<?= $row['id'] ?>" <?= (isset($category_id) && $category_id == $row['id'] ? "selected" : "") ?>><?= $row['name'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="description" class="control-label">Description</label>
					<textarea rows="3" name="description" id="description" class="form-control form-control-sm rounded-0" value="" required><?php echo isset($description) ? $description : ''; ?></textarea>
				</div>
				<div class="form-group">
					<label for="" class="control-label">Music Banner</label>
					<div class="custom-file">
					<input type="file" class="custom-file-input rounded-circle" id="customFile2" name="banner_img" onchange="displayBanner(this,$(this))">
					<label class="custom-file-label" for="customFile2">Choose file</label>
					</div>
				</div>
				<div class="form-group d-flex justify-content-center">
					<img src="<?php echo validate_image((isset($banner_path) ? $banner_path : "")) ?>" alt="" id="BannerViewer" class="img-fluid img-thumbnail bg-gradient-dark border-dark">
				</div>
				<div class="form-group">
					<label for="" class="control-label">Audio File</label>
					<div class="custom-file mb-2">
						<input type="file" class="custom-file-input rounded-circle" id="customFile2" name="audio_file" accept="audio/*" onchange="displayAudioName(this,$(this))">
						<label class="custom-file-label" for="customFile2">Choose file</label>
					</div>
					<?php if(isset($audio_path) && !empty($audio_path)): ?>
						<div class="pl-4">
							<audio src="<?= base_url.$audio_path ?>" controls></audio>
						</div>
						<div class="pl-4">
							<a href="<?= base_url.$audio_path ?>" target="_blank"><?= (pathinfo($audio_path, PATHINFO_FILENAME)).".".(pathinfo($audio_path, PATHINFO_EXTENSION))  ?></a>
						</div>
					<?php endif; ?>
				</div>
				<div class="form-group">
					<label for="status" class="control-label">Status</label>
					<select name="status" id="status" class="form-control form-control-sm rounded-0" required="required">
						<option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
						<option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
					</select>
				</div>
			</form>
		</div>
	</div>
	<div class="card-footer py-2 text-center">
		<button class="btn btn-primary rounded-0" type="submit" form="music-form">Save</button>
	</div>
</div>
<script>
	function displayBanner(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        	$('#BannerViewer').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }else{
			_this.siblings('.custom-file-label').html("Choose File")

		}
	}
	function displayAudioName(input,_this){
		if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        }

	        reader.readAsDataURL(input.files[0]);
	    }else{
			_this.siblings('.custom-file-label').html("Choose File")
		}
	}
	$(document).ready(function(){
		$('#category_id').select2({
			placeholder:"Please Select Category Here",
			containerCssClass:"rounded-0"
		})
		$('#music-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_music",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						// location.reload()
						location.replace("<?= base_url ?>admin/?page=musics/view_music&id="+resp.mid)
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").scrollTop(0);
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})

	})
</script>