<?php $hasLinks = !empty($arg["links"]); ?>
<li class="nav-item">
    <?php if ($hasLinks) { ?>
        <a class="nav-link collapsed" href="<?= $arg["route"] ?>" data-toggle="collapse" data-target="#<?= $arg["id"] ?>"
            aria-expanded="true" aria-controls="collapseBootstrap">
            <i class="<?= $arg["icon"] ?>"></i>
            <span><?= $arg["name"] ?></span>
        </a>
        <div id="<?= $arg["id"] ?>" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
            <?php foreach ($arg["links"] as $link) { ?>
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?= $base_url ?>/<?= $link["route"] ?>"><?= $link["text"] ?></a>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <a class="nav-link" href="<?= $arg["route"] ?>">
            <i class="<?= $arg["icon"] ?>"></i>
            <span><?= $arg["name"] ?></span>
        </a>
    <?php } ?>
</li>