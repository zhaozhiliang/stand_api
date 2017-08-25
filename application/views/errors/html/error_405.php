<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$Loader = & load_class('Loader', 'core');
$Loader->view('include/header.php');
?>
<!--sidebar-menu-->
<div id="sidebar">
  <ul>
    <li class="active"><a href="javascript:;"><i class="icon icon-home"></i> <span>没有访问权限</span></a> </li>
  </ul>
</div>
<!--sidebar-menu-->
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php echo base_url();?>" title="返回仪表盘" class="tip-bottom"><i class="icon-home"></i>仪表盘</a> <a href="javascript:;" class="current">Error 405</a> </div>
    <h1>Error 405</h1>
  </div>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Error 405</h5>
          </div>
          <div class="widget-content">
            <div class="error_ex">
              <h1>405</h1>
              <h3>发生错误了！你没有权限访问当前页面。</h3>
              <p>如需继续访问请联系系统管理员。</p>
              <a class="btn btn-warning btn-big"  href="javascript:history.go(-1);">返回上一页</a> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$Loader->view('include/footer.php');
?>