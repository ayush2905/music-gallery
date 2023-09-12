
<div class="row">
    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12 mx-auto mt-5 mb-3 ">
        <h1 class="text-center font-weight-bolder title-font">Music Categories</h1>
        <hr class="mx-auto bg-primary opacity-100" style="height:2px;opacity:1;width:20%">
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
            $category_list = $conn->query("SELECT * FROM `category_list` where `status` = 1 and `delete_flag` = 0 order by `name` asc");
            while($row = $category_list->fetch_assoc()):
            ?>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3 cat-items">
                <div class="card rounded-0 card-outline card-primary  h-100">
                    <div class="card-header rounded-0">
                        <div class="card-title rounded-0 card-title-font"><b><?= $row['name'] ?></b></div>
                    </div>
                    <div class="card-body rounded-0">
                        <div class="container-fluid">
                            <div class=" truncate">
                                <?= str_replace("\n", "<br>", html_entity_decode($row['description'])) ?>
                            </div>
                            
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a href="<?= base_url."?page=music_list&cid={$row['id']}" ?>" class="btn btn-sm btn-flat btn-primary bg-gradient-primary">View Category</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script>
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
    })
</script>