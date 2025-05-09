    <div class="card mb-5">
        <div class="card-body pt-4 pb-0">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4 <?php if( $menuActive == 'settings') { echo 'active'; } ?>" href="/index.php?controller=Settings&action=index" aria-selected="true"  <?php if( $menuActive == 'settings') { echo 'aria-selected="true"';} ?>>Settings</a>
                </li>
       
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4" <?php if( $menuActive == 'users') { echo 'active'; } ?> href="/index.php?controller=User&action=index" <?php if( $menuActive == 'users') { echo 'aria-selected="true"';} ?>>Users</a>
                </li>
       
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary <?php if( $menuActive == 'emailtemplates') { echo 'active'; } ?> pb-4" href="/index.php?controller=EmailTemplate&action=index" <?php if( $menuActive == 'emailtemplates') { echo 'aria-selected="true"'; } ?>>Emails</a>
                </li>
        
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary <?php if( $menuActive == 'projectsettings') { echo 'active'; } ?> pb-4" href="/index.php?controller=DataSeeder&action=index" <?php if( $menuActive == 'projectsettings') { echo 'aria-selected="true"'; } ?>>Projects</a>
                </li>
        
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4" <?php if( $menuActive == 'generalstats') { echo 'active'; } ?> href="/index.php?controller=DataSeeder&action=index" <?php if( $menuActive == 'generalstats' ) { echo 'aria-selected="true"'; } ?>>Stats</a>
                </li>
      
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4" <?php if( $menuActive == 'generatedata') { echo 'active'; } ?> href="/index.php?controller=DataSeeder&action=index" <?php if( $menuActive == 'generatedata') { echo 'aria-selected="true"'; } ?>>Generate data</a>
                </li>
        
            </ul>
        </div>
    </div>