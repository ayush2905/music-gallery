<?php 
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
		height: 30vh;
		width: 100%;
		object-fit: scale-down;
		object-position:center center;
		/* border-radius: 100% 100%; */
	}
</style>
<div class="card card-outline rounded-0 card-purple">
	<div class="card-header">
		<h3 class="card-title">Music Details</h3>
		<div class="card-tools">
			<a href="<?= base_url."admin/?page=musics/manage_music&id={$id}"?>" class="btn btn-flat btn-primary bg-primary"><span class="fas fa-edit"></span>  Edit</a>
			<button id="delete_data" type="button" class="btn btn-flat btn-danger bg-danger"><span class="fas fa-trash"></span>  Edit</button>
			<a href="<?= base_url."admin/?page=musics"?>" class="btn btn-flat btn-light bg-light"><span class="fas fa-angle-left"></span>  Back to List</a>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
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
				<label for="" class="control-label">Music Banner</label>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?php echo validate_image((isset($banner_path) ? $banner_path : "")) ?>" alt="" id="BannerViewer" class="img-fluid img-thumbnail bg-gradient-dark border-dark">
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
		$('#delete_data').click(function(){
			_conf("Are you sure to delete this Music permanently?","delete_music",["<?= isset($id) ? $id : "" ?>"])
		})
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
	
	function delete_music($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_music",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.replace("<?= base_url."admin/?page=musics" ?>");
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>