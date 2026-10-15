
<?php section("master_header.php");?>
<!-- ./start wrapper -->  
<div id="wrapper">

<?php section("left_sidebar.php");?>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">
<?php section("topbar.php");?>

<?php if (isset($_SESSION["flash"]) && (isset($_SESSION["flash"]["error"]) || isset($_SESSION["flash"]["success"]))) { ?>
<div class="container-fluid mt-3">
<?php if (!empty($_SESSION["flash"]["success"])) { ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert"><?= htmlspecialchars($_SESSION["flash"]["success"]) ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php } ?>
<?php if (!empty($_SESSION["flash"]["error"])) { ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><?= htmlspecialchars($_SESSION["flash"]["error"]) ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php } ?>
<?php unset($_SESSION["flash"]); ?>
</div>
<?php } ?>

<?php section("main_content.php");?>
<?php section("footer.php");?>
</div>
</div>
</div>

</div> <!-- ./end wrapper --> 

<?php  section("master_footer.php"); ?> 
