<div class="box-content-cities mt-3">
    <h3 class="text-center mt-4">آخرین پست ها</h3>
    <hr>
    <div class="row p-2">
        <?php foreach ($side_post as $pu) { ?>
            <div class="col-4 col-md-4">
                <a class="nav-link" title="<?= $pu['title'] ?>" href="<?= assets('post/' . $pu['id']) ?>"><img
                            style="width:100px ; height: 100px;" src="<?= assets($pu['img']) ?>"
                            width="100" height="100px" alt="<?= $pu['title'] ?>"></a>
            </div>
            <div class="col-8 col-md-8 p-2">
                <a class="nav-link" title="<?= $pu['title'] ?>" href="<?= assets('post/' . $pu['id']) ?>">
                    <p class="text-center text-primary"><?php echo limit_words(trim($pu['title'], "/  "), 6) ?>...</p>
                </a>
                <span> <?php echo limit_words(trim($pu['content'], "/  "), 10) ?>...</span><a class="nav-link"
                                                                                              title="<?= $pu['title'] ?>"
                                                                                              href="<?= assets('post/' . $pu['id']) ?>">
                    <button style="float: left;" class="btn btn-outline-success">ادامه</button>
                </a>
            </div>
            <hr>
        <?php } ?>
    </div>
</div>
<div class="box-content-cities mt-4">
    <h3 class="text-center mt-4">پست های برند ها</h3>
    <hr>
    <div class="row p-2">
        <?php foreach ($side_brand as $pu) { ?>
            <div class="col-4 col-md-4">
                <a class="nav-link" title="<?= $pu['title'] ?>" href="<?= assets($pu['slug']."/" . $pu['id']) ?>"><img
                            style="width:100px ; height: 100px;" src="<?= assets($pu['img']) ?>"
                            width="100" height="100px" alt="<?= $pu['title'] ?>"></a>
            </div>
            <div class="col-8 col-md-8 p-2">
                <a class="nav-link" title="<?= $pu['title'] ?>" href="<?= assets($pu['slug']."/" . $pu['id']) ?>">
                    <p class="text-center text-primary"><?php echo limit_words(trim($pu['title'], "/  "), 6) ?>...</p>
                </a>
                <span> <?php echo limit_words(trim($pu['content'], "/  "), 10) ?>...</span><a class="nav-link"
                                                                                              title="<?= $pu['title'] ?>"
                                                                                              href="<?= assets($pu['slug']."/" . $pu['id']) ?>">
                    <button style="float: left;" class="btn btn-outline-success">ادامه</button>
                </a>
            </div>
            <hr>
        <?php } ?>
    </div>
</div>
