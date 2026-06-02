<?php

if (!empty($_SESSION["flash"])):

?>

<div class="alert alert-info">

    <?= htmlspecialchars(
        $_SESSION["flash"]
    ) ?>

</div>

<?php

unset($_SESSION["flash"]);

endif;