<div class="footer w-100">
    <small>
        <?php
        echo strtolower($_SESSION['uEmail']);
        echo isset($_SESSION["acc_name"]) ? " - " . $_SESSION["acc_name"] : "";
        echo isset($_SESSION["role_desc"]) ? " - " . $_SESSION["role_desc"] : "";
        ?>
    </small>
</div>