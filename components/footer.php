<?php
function Footer()
{ ?>
    <footer
        class="d-flex mt-5 flex-wrap justify-content-between align-items-center py-3 my-4 border-top bg-white px-4 mt-auto">

        <div class="col-md-4 d-flex align-items-center">
            <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
                <i class="bi bi-mortarboard-fill fs-4 text-primary"></i>
            </a>
            <span class="mb-3 mb-md-0 text-muted">© <?= date("Y") ?> ระบบรับสมัครนักเรียน (MTC)</span>
        </div>

        <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
            <li class="ms-3">
                <span class="text-muted" style="font-size: 0.9rem;">Developed by </span>
                <a class="text-decoration-none fw-bold text-primary ms-1" target="_blank"
                    href="https://www.facebook.com/anuphappp/">
                    <i class="bi bi-facebook"></i> นายอานุภาพ ศรเทียน
                </a>
            </li>
        </ul>
    </footer>
<?php } ?>