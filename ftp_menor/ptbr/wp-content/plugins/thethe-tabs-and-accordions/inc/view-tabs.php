<?php 
	function getCurrentViewIndex(){
		$fetchAllViewIndex = array('overview','settings');
		$viewIndex = (isset($_REQUEST['view']) && in_array($_REQUEST['view'], $fetchAllViewIndex))
			? $_REQUEST['view'] : 'overview';
		return $viewIndex;
	}

	function getTabURL($viewIndex = null){
		if (!$viewIndex) $viewIndex = 'overview';
		return 'admin.php?page=tabs-n-accordions&amp;view=' . $viewIndex;
	}	
?>

<div id="thethefly">
  <div class="wrap">
    <h2 id="thethefly-panel-title"> <span id="thethefly-panel-icon" class="icon48">&nbsp;</span> TheThe Tabs and Accordions</h2>
    <div id="thethefly-panel-frame">
      <div id="menu-management-liquid">
        <div id="menu-management"> 
          <!-- tabs -->
          <div class="nav-tabs-wrapper">
            <div class="nav-tabs">
              <?php
				$view = getCurrentViewIndex();	
				if ($view == 'overview') echo "<span class='nav-tab nav-tab-active'>Overview</span>"; else echo "<a href='".getTabURL('overview')."' class='nav-tab hide-if-no-js'>Overview</a>"; 
				if ($view == 'settings') echo "<span class='nav-tab nav-tab-active'>Settings</span>"; else echo "<a href='".getTabURL('settings')."' class='nav-tab hide-if-no-js'>Settings</a>"; 
?>
            </div>
          </div>
          <!-- /tabs -->
          <?php 
$tabDesc = '';
switch ($view) {
	case 'settings': $tabDesc = 'TheThe Tabs and Accordions Settings';break;
	default: $tabDesc = 'TheThe Tabs and Accordions Overview';
}?>
          <div class='menu-edit tab-overview'>
            <div id='nav-menu-header'>
              <div class='major-publishing-actions'> <span><?php echo $tabDesc; ?></span>
                <div class="sep">&nbsp;</div>
              </div>
              <!-- END .major-publishing-actions --> 
            </div>
            <!-- END #nav-menu-header -->
            <div id='post-body'>
              <div id='post-body-content'>
                <?php
					if($view == 'overview'){
						include 'view-tab-'.$view.'.php';
					} else {
					?>
                <form method="post" action="">
                  <?php
					include 'inc.submit-buttons.php';
					include 'view-tab-'.$view.'.php';
					include 'inc.submit-buttons.php';
				  ?>
                </form>
                <?php } ?>
              </div>
              <!-- /#post-body-content --> 
            </div>
            <!-- /#post-body -->
            <div id="nav-menu-footer">
              <div class="major-publishing-actions">&nbsp;</div>
            </div>             
          </div>
          <!-- /.menu-edit --> 
          
        </div>
      </div>
      <!-- sidebar -->
      <div id="thethefly-admin-sidebar" class="metabox-holder">
        <div id="side-sortables" class="meta-box-sortables">
          <?php include 'inc.sidebar.help.php';?>
          <?php include 'inc.sidebar.donate.php';?>
          <?php include 'inc.sidebar.newsletter.php';?>
          <?php include 'inc.sidebar.themes.php';?>
          <?php include 'inc.sidebar.plugins.php';?>
          <?php //include 'http://thethefly.com/plugin-links.html';?>
        </div>
      </div>
      <!-- /sidebar -->
      <div class="clear"></div>
    </div>
  </div>
</div>
