<?php

function showStatus()
{ ?>
    <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>

    <?php if (isset($_SESSION['error'])) { ?>

        <script>
            Swal.fire({
                position: "center",
                icon: "error",
                title: "<?php echo $_SESSION['error'] ?>",
                showConfirmButton: false,
                background: '#ffff',
                timer: 1500
            });
        </script>


        <?php
        unset($_SESSION['error']);
        ?>



    <?php } ?>

    <?php if (isset($_SESSION['warning'])) { ?>

        <script>
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "<?php echo $_SESSION['warning'] ?>",
                showConfirmButton: false,
                background: '#fff',
                timer: 1500
            });
        </script>

        <?php
        unset($_SESSION['warning']);
    ?>
    <?php } ?>

    <?php if (isset($_SESSION['success'])) { ?>

        <script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "<?php echo $_SESSION['success'] ?>",
                showConfirmButton: false,
                background: '#fff',
                timer: 1500
            });
        </script>
        <?php unset($_SESSION['success']); ?>


    <?php } ?>

    <?php
}
?>
<?php function showStatus_admin()
{ ?>
    <script src="../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>

    <?php if (isset($_SESSION['error'])) { ?>

        <script>
            Swal.fire({
                position: "center",
                icon: "error",
                title: "<?php echo $_SESSION['error'] ?>",
                showConfirmButton: false,
                background: '#ffff',
                timer: 1500
            });
        </script>


        <?php
        unset($_SESSION['error']);
        ?>



    <?php } ?>

    <?php if (isset($_SESSION['warning'])) { ?>

        <script>
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "<?php echo $_SESSION['warning'] ?>",
                showConfirmButton: false,
                background: '#fff',
                timer: 1500
            });
        </script>

        <?php
        unset($_SESSION['warning']);
    ?>
    <?php } ?>

    <?php if (isset($_SESSION['success'])) { ?>

        <script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "<?php echo $_SESSION['success'] ?>",
                showConfirmButton: false,
                background: '#fff',
                timer: 1500
            });
        </script>
        <?php unset($_SESSION['success']); ?>


    <?php } ?>

    <?php
}
?>