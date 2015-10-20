      <div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="./?p=home">Instrument Manager</a>
          </div>
          <div class="collapse navbar-collapse">
          <?php if($auth != true){ ?>
            <ul class="nav navbar-nav">
              <li <?php if($page=='home'){echo'class="active"';} ?> ><a href="?p=home">Home</a></li>
              <li <?php if($page=='about'){echo'class="active"';} ?> ><a href="?p=about">About</a></li>
              <li <?php if($page=='contact'){echo'class="active"';} ?> ><a href="?p=contact">Contact</a></li>
            </ul>
          <?php } ?>
          <ul class="nav navbar-nav navbar-right">
            <?php if($auth == true || $public_browse == true){ ?>
              <li <?php if($page=='browse'){echo'class="active"';} ?>><a href="?p=browse">Browse</a></li>
            <?php }
            if($_SESSION['level'] == 'admin'){ ?>
              <li class="dropdown <?php if($page=='users'||$page=='schools'){echo'active';} ?> ">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Manage <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="?p=users">Users</a></li>
                  <li class="divider"></li>
                  <li><a href="?p=schools">Schools</a></li>
                  <li class="divider"></li>
                  <li><a href="?p=browse">Instruments</a></li>
                </ul>
              </li>
            <?php }
            if($auth){ ?>
              <li class="dropdown <?php if($page=='user'){echo'active';} ?> ">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo (isset($user['email'])) ? $user['email'] : $id ; ?><b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="?p=logout">Sign Out</a></li>
                </ul>
              </li>
            <?php } else { ?>
                <li <?php if($page=='login'){echo'class="active"';} ?> ><a href="?p=login">Sign In</a></li> 
            <?php } ?>
          </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>