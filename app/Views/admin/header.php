<body>
    <div class="app menu-off-canvas sidebar-colored header-colorful align-content-stretch d-flex flex-wrap">
        <div class="app-sidebar">
            <div class="logo">
                <a href="<?=site_url('regist/listpeserta')?>" class="logo-icon"><span class="logo-text">Lomba HAM</span></a>
                <div class="sidebar-user-switcher user-activity-online">
                    <a href="#">
                        <img src="<?=base_url('adm-template/images/avatars/single-person.png')?>">
                        <span class="activity-indicator"></span>
                        <span class="user-info-text">Admin<br><span class="user-state-info">Online</span></span>
                    </a>
                </div>
            </div>
            <?php include(__DIR__ . "/sidebar.php");?>
        </div>
        <div class="app-container">
            <div class="app-header">
                <nav class="navbar navbar-light navbar-expand-lg">
                    <div class="container-fluid">
                        <div class="navbar-nav" id="navbarNav">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link hide-sidebar-toggle-button" href="#"><i class="material-icons">menu</i></a>
                                </li>
                            </ul>
            
                        </div>
                        <div class="d-flex">
                            <ul class="navbar-nav">
                                <li class="nav-item hidden-on-mobile">
                                    <a class="nav-link active" href="<?=site_url('auth/logout')?>">Sign Out</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="app-content">
                <div class="content-wrapper">