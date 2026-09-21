  <!-- jQuery -->
  <script src="<?= assets('them/admin/plugins/jquery/jquery.min.js') ?>"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="<?= assets('them/admin/plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <!-- Morris.js charts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
  <script src="<?= assets('them/admin/plugins/morris/morris.min.js'); ?>"></script>
  <!-- Sparkline -->
  <script src="<?= assets('them/admin/plugins/sparkline/jquery.sparkline.min.js'); ?>"></script>
  <!-- jvectormap -->
  <script src="<?= assets('them/admin/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'); ?>"></script>
  <script src="<?= assets('them/admin/plugins/jvectormap/jquery-jvectormap-world-mill-en.js'); ?>"></script>
  <!-- jQuery Knob Chart -->
  <script src="<?= assets('them/admin/plugins/knob/jquery.knob.js'); ?>"></script>
  <!-- daterangepicker -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
  <script src="<?= assets('them/admin/plugins/daterangepicker/daterangepicker.js'); ?>"></script>
  <!-- datepicker -->
  <script src="<?= assets('them/admin/plugins/datepicker/bootstrap-datepicker.js'); ?>"></script>
  <!-- Bootstrap WYSIHTML5 -->
  <script src="<?= assets('them/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
  <!-- Slimscroll -->
  <script src="<?= assets('them/admin/plugins/slimScroll/jquery.slimscroll.min.js'); ?>"></script>
  <!-- FastClick -->
  <script src="<?= assets('them/admin/plugins/fastclick/fastclick.js'); ?>"></script>
  <!-- AdminLTE App -->
  <script src="<?= assets('them/admin/dist/js/adminlte.js') ?>"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="<?= assets('them/admin/dist/js/pages/dashboard.js'); ?>"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="<?= assets('them/admin/dist/js/demo.js'); ?>"></script>
  <script src="<?= assets('them/admin/dist/editor/plugin.js'); ?>"></script>
<?php if (empty($skipDefaultCkeditor)): ?>
<script src="<?= assets('them/admin/plugins/ckeditor/ckeditor.js')?>"></script>
<script>
    $(document).ready(function() {
        if (window.CKEDITOR && document.getElementById('editor1') && !CKEDITOR.instances.editor1) {
            CKEDITOR.replace('editor1');
        }
    });
</script>
<?php endif; ?>