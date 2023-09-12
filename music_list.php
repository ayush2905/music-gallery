<?php 
$page_title = "Music List";
$page_description = "";
if(isset($_GET['cid'])){
    $cat_qry = $conn->query("SELECT * FROM `category_list` where `id` = '{$_GET['cid']}' and `delete_flag` = 0 and `status` = 1 ");
    if($cat_qry->num_rows > 0){
        $result = $cat_qry->fetch_assoc();
        $category_id = $result['id'];
        $page_title = $result['name'];
        $page_description = $result['description'];
    }
}

?>

<style>
    .music-banner{
        width: 100%;
        height: 35vh;
        overflow:auto;
        background: #000;
    }
    .music-banner img{
        width: 100%;
        height: 100%;
        object-fit:scale-down;
        object-position:center center;
    }
    .music-btns{
        padding:0;
        display:flex;
        align-items:center;
        justify-content:center;
        height:1.5em;
        width:1.5em;
        font-size:.5em;
        margin: 0 .5em;
    }
    #player-field{
        display:none;
        position:fixed;
        bottom:0;
        left:0;
        min-height:5em;
        width: 100%;
        background:#000;
        justify-content:center;
        align-items:center;
    }
    #player-slider{
        width:50%;
        line-height:.8em;
        padding:.5em;
    }
    #progress-container {
        height: 7px;
        width: 100%;
        background-color: rgb(255 255 255 / 33%);
        margin: .5em;
        cursor: pointer;
        border-radius: 8px;
    }

    #progress {
        background-color: #d9c9c9;
        width: 0%;
        height: inherit;
        border-radius: inherit;
        transition: width 100ms ease-in;
    }

    #timer-bar {
        display: flex;
        justify-content: space-between;
        font-size: 1rem;
    }
    button.play-btn {
        height: 2.35em;
        width: 2.5em;
        border-radius: 50% 50%;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #player-img-holder {
        position: relative;
        width: 12vw;
        height: 13vh;
    }
    #player-img-holder img {
        position: absolute;
        bottom: 0;
        height: 100%;
        width: 100%;
        object-fit: scale-down;
        object-position: center center;
        background: #000;
    }
    .music-btns i {
        font-size: .7em;
    }
</style>
<div class="row">
    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12 mx-auto mt-5 mb-3 ">
        <h1 class="text-center font-weight-bolder title-font"><?= $page_title ?></h1>
        <hr class="mx-auto bg-primary opacity-100" style="height:2px;opacity:1;width:20%">
        <?php if(!empty($page_description)): ?>
            <card class="rounded-0 shadow">
                <div class="card-body rounded-0">
                    <div class="container-fluid">
                        <div class="text-muted"><em><?= $page_description ?></em></div>
                    </div>
                </div>
            </card>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12 mx-auto mb-5">
        <div class="input-group mb-3">
            <input type="search" id="search_cat"  placeholder="Search Here"  class="form-control">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mx-auto mb-5">
        <div class="row">
            <?php 
            $where = "";
            if(isset($category_id)){
                $where = "and `category_id` = '{$category_id}' ";
            }
            $music_list = $conn->query("SELECT *, COALESCE((SELECT `name` FROM `category_list` where `music_list`.`category_id` = `category_list`.`id`), 'Unkown Category') as `category_name` FROM `music_list` where `status` = 1 and `delete_flag` = 0 and `audio_path` != ''  {$where} order by `title` asc");
            while($row = $music_list->fetch_assoc()):
            ?>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3 cat-items">
                <div class="card rounded-0 card-outline card-primary  h-100">
                    <div class="card-img-top music-banner">
                        <img src="<?= validate_image($row['banner_path']) ?>" alt="<?= $row['title'] ?>">
                    </div>
                    <div class="card-body rounded-0">
                        <div class="container-fluid">
                            <div>
                                <h2 class="rounded-0 card-title-font text-center w-100"><b><?= $row['title'] ?></b></h2>
                            </div>
                            <div>
                                <div class="truncate">
                                    <?= str_replace("\n", "<br>", html_entity_decode($row['description'])) ?>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <div class="row justify-content-end">
                            <a href="<?= base_url.$row['audio_path'] ?>" download="<?= $row['title'].".".(pathinfo($row['audio_path'], PATHINFO_EXTENSION)) ?>" class="btn btn-sm btn-outline-success rounded-circle p-0 music-btns"><i class="fa fa-download"></i></a>
                            <a href="javascript:void(0)" data-id="<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary rounded-circle p-0 music-btns play_music"><i class="fa fa-play"></i></a>
                            <a href="javascript:void(0)" data-id="<?= $row['id'] ?>" class="btn btn-sm btn-outline-info rounded-circle p-0 music-btns view_music_details"><i class="fa fa-info"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<div id="player-field">
    <div>
        <div id="player-img-holder">
            <img src="<?= validate_image("") ?>" alt=""/>
        </div>
    </div>
    <div>
        <button class="play-btn" id="play" type="button"><i class="fa fa-play"></i></button>
    </div>
    <div id="player-slider">
        <div id="music-title" class="text-light"><span id="title">Music Title</span> - <span class="mx-4 text-muted" id="artist">Test</span></div>
        <div id="progress-container">
            <div id="progress"></div>
        </div>
        <div id="timer-bar">
            <span id="timer">0:00</span>
            <span id="duration"></span>
        </div>
    </div>
    <div id="volume-control" class="px-2">
        <a href="javascript:void(0)" id="volume-down" class="text-muted mx-2"><i class="fa fa-volume-down"></i></a>
        <a href="javascript:void(0)" id="volume-up" class="text-muted mx-2"><i class="fa fa-volume-up"></i></a>
    </div>
</div>
<audio src="" class="d-none" id="player-el"></audio>
<script>
    const banner_img = document.querySelector('#player-img-holder>img');
    const disc = document.getElementById('player-el');
    const title = document.getElementById('title');
    const artist = document.getElementById('artist');
    const progressContainer = document.getElementById('progress-container');
    const progress = document.getElementById('progress');
    const timer = document.getElementById('timer');
    const duration = document.getElementById('duration');
    const play = document.getElementById('play');
    var volume = .5;
    disc.volume = volume
    $(function(){
        $('#search_cat').on('input change', function(e){
            e.preventDefault()
            var _search = $(this).val().toLowerCase()

            $('.cat-items').each(function(e){
                var _text = $(this).text().toLowerCase()
                if(_text.includes(_search) === true){
                    $(this).toggle(true)
                }else{
                    $(this).toggle(false)
                }
            })
        })
        $('.view_music_details').click(function(e){
            e.preventDefault()
            var id = $(this).attr('data-id')
            uni_modal("Music Details", "<?= base_url."view_music_details.php?id=" ?>"+id,"modal-large")
        })

        $('.play_music').click(function(e){
            e.preventDefault()
            var id = $(this).attr('data-id')
            start_loader()
            $.ajax({
                url:_base_url_+"classes/Master.php?f=get_music_details&id="+id,
                dataType:"JSON",
                error: err=>{
                    alert("There's an error occurred while fetching the audio file.")
                    end_loader()
                    console.error(err)
                    
                },
                success:function(resp){
                    if(typeof resp == 'object'){
                        loadSong(resp)
                        end_loader()
                        $('#player-field').css('display', "flex")
                    }else{
                        alert("There's an error occurred while fetching the audio file.")
                        end_loader()
                        console.error(resp)
                    }
                }
            })
        })
        $('#play').click(function(e){
            e.preventDefault()
            playPauseMedia()
        })
        $('#volume-down').click(function(e){
            e.preventDefault()
            change_volume();
        })
        $('#volume-up').click(function(e){
            e.preventDefault()
            change_volume("up");
        })
    })
    function loadSong(song) {
        var dur = 0;
        banner_img.src = song.coverPath;
        disc.src = song.discPath;
        title.textContent = song.title;
        artist.textContent = song.artist;
        disc.addEventListener('canplaythrough', function() {
            dur = disc.duration
            mins = Math.floor(Math.abs(dur / 60))
            mins = String(mins).padStart('2', 0)
            sec = Math.floor(dur - (parseInt(mins) * 60))
            sec = String(sec).padStart('2', 0)
            duration.textContent = `${mins}:${sec}`
        })
        playPauseMedia()
    }
    function playPauseMedia() {
        if (disc.paused) {
            disc.play();
        } else {
            disc.pause();
        }
        updatePlayPauseIcon()
    }
    // Update icon
    function updatePlayPauseIcon() {
        if (disc.paused) {
            play.querySelector('i').classList.remove('fa-pause');
            play.querySelector('i').classList.add('fa-play');
        } else {
            play.querySelector('i').classList.remove('fa-play');
            play.querySelector('i').classList.add('fa-pause');
        }
    }

    // Update progress bar
    function updateProgress() {
        progress.style.width = (disc.currentTime / disc.duration) * 100 + '%';

        let minutes = Math.floor(disc.currentTime / 60);
        let seconds = Math.floor(disc.currentTime % 60);
        if (seconds < 10) {
            seconds = '0' + seconds;
        }
        timer.textContent = `${minutes}:${seconds}`;
    }

    // Reset the progress
    function resetProgress() {
        progress.style.width = 0 + '%';
        timer.textContent = '0:00';
    }
    // Navigate song slider
function progressSlider(ev) {
    var is_playing = !disc.paused
    if (is_playing)
        disc.pause()
    const totalWidth = this.clientWidth;
    const clickWidth = ev.offsetX;
    const clickWidthRatio = clickWidth / totalWidth;
    disc.currentTime = clickWidthRatio * disc.duration;
    if (is_playing)
        disc.play()
    document.addEventListener('mousemove', slideMoving);
    document.addEventListener('mouseup', function() {
        if (is_playing)
            disc.play()
        document.removeEventListener('mousemove', slideMoving);
    });

}

function change_volume($dir = "down"){
        var vol = volume * 10
    if($dir == "down"){
        vol--;
        if(vol <= 0)
        vol = 0;
    }else{
        vol++;
        if(vol >= 10)
        vol = 10;
    }
    volume = vol / 10
        disc.volume = volume
}

    // Navigate song slider while moving
    function slideMoving(ev) {
        var is_playing = !disc.paused
        if (is_playing)
            disc.pause()
        const totalWidth = progressContainer.clientWidth;
        const clickWidth = ev.offsetX;
        const clickWidthRatio = clickWidth / totalWidth;
        disc.currentTime = clickWidthRatio * disc.duration;
        if (is_playing)
            disc.play()
    }
    function song_ended(){
        $('#player-field').hide()
        console.log('audio ended')
    }

    // Various events on disc
    disc.addEventListener('play', updatePlayPauseIcon);
    disc.addEventListener('pause', updatePlayPauseIcon);
    disc.addEventListener('timeupdate', updateProgress);
    disc.addEventListener('ended', song_ended);


    // Move to different place in the song
    progressContainer.addEventListener('mousedown', progressSlider);
</script>