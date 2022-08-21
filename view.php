<?php
require_once("../../../config/database.php");
require_once("model.php");
if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) )
{
session_start(); 
if ( !isset($_SESSION['session_username']) or !isset($_SESSION['session_id']) or !isset($_SESSION['session_level']) or !isset($_SESSION['session_kode_akses']) or !isset($_SESSION['session_hak_akses']) )
{
  echo '<div class="callout callout-danger">
          <h4>Session Berakhir!!!</h4>
          <p>Silahkan logout dan login kembali. Terimakasih.</p>
        </div>';
} else {
$db = new db();
$view=$_POST['view'];
$username = $_SESSION['session_username'];
$id_menus = 3; 
$cekMenusUser = $db->cekMenusUser($username,$id_menus); 
    foreach($cekMenusUser[1] as $data){
      $create = $data['c'];
      $read = $data['r'];
      $update = $data['u'];
      $delete = $data['d'];
      $nama_menus = $data['nama_menus'];
      $keterangan = $data['keterangan'];
    }
if($cekMenusUser[2] == 1) {
?>

<?php 
if($view == 'table'){
    $kode_akses = htmlspecialchars($_POST['kode_akses']);
    $getTable = $db->getTable($kode_akses); 
?>
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Daftar Kode Akses</h3>
  </div>
  <!-- /.box-header -->
  <div class="box-body table-responsive">
    <table class="table table-bordered table-striped text-nowrap">
      <thead>
      <tr>
        <th style="text-align:center;">#</th>
        <th style="text-align:center;">Aksi</th>
        <th style="text-align:center;">Kode</th>
        <th style="text-align:center;">Kode Akses</th>
      </tr>
      </thead>
      <tbody>
        <?php
          $no = 1;
          foreach($getTable[1] as $row){
        ?>
        <tr>
          <td style="text-align:center;"><?php echo $no++;?></td>
          <td style="text-align:center;">
            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">
                <span class="fa fa-fw fa-cogs"></span>
                <span class="sr-only">Toggle Dropdown</span>
              </button>
                <ul class="dropdown-menu" role="menu">
                <?php if($delete == "y") {?>
                <li><a href="javascript:void(0)" class="delete text-red" id="<?php echo $row['id'];?>"><i class="fa fa-fw fa-trash-o"></i>Hapus</a></li>
                <?php } ?>
                </ul>
            </div>
          </td>
          <td><?php echo $row['kode'];?></td>
          <td><?php echo $row['nama'];?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <!-- /.box-body -->
</div>
<!-- /.box -->
<script>
  $(document).off('click', '.delete').on('click', '.delete', function(){
    let id = $(this).attr('id');
    Swal.fire({
      title: 'Hapus Kode Akses?',
      text: "Kode Akses akan dihapus selamanya!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Loading...',
          html: 'Mohon menunggu sebentar...',
          allowEscapeKey: false,
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
          }
        });
        let value = {
          controller : 'delete',
          id : id,
        }
        $.ajax({
          url:"menus/<?php echo $id_menus;?>/controller.php",
          type: "POST",
          data: value,
          success: function(data, textStatus, jqXHR)
          { 
            loadTable();
            $resp = JSON.parse(data);
            if($resp['status'] == true){
              toastr.success($resp['message'], $resp['title'], {timeOut: 2000, progressBar: true});
            } else {
              toastr.error($resp['message'], $resp['title'], {closeButton: true});
            }
          },
          error: function (request, textStatus, errorThrown) {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: textStatus,
              didOpen: () => {
                Swal.hideLoading()
              }
            });
          }
        }); 
      }
    })
  });
</script>
<?php }?>

<?php 
$kode_akses = htmlspecialchars($_POST['kode_akses']);
if($view == 'modal_tambah' && $create == "y"){
?>
<div class="box-body">
  <div class="form-group">
    <label for="kode" class="col-sm-2 control-label">Kode Akses</label>
    <div class="col-sm-10">
      <select class="form-control select22" id="kode" name="kode" style="width: 100%;">
        <option value="">-- Pilih --</option>
        <?php 
          $getKode = $db->getKode($kode_akses);
          foreach($getKode[1] as $option){
        ?>
        <option value="<?php echo $option['id'];?>"><?php echo $option['nama'];?></option>
        <?php } ?>
      </select>
    </div>
  </div>
</div>
<div class="box-footer">
  <button type="button" class="btn btn-success pull-right" id="btn-save">Simpan</button>
</div>
<script>
  $('#btn-save').click(function() {
    if($('#kode').val() == ''){
      $('#kode').focus();
      Swal.fire("Validasi!", "Kode Akses wajib diisi.");
      return;
    }
    Swal.fire({
      title: 'Tambah Kode Akses?',
      text: "Kode Akses akan ditambahkan!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Tambah',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Loading...',
          html: 'Mohon menunggu sebentar...',
          allowEscapeKey: false,
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
            let value = {
              controller : 'create',
              kode_akses : '<?php echo $kode_akses;?>',
              kode : $('#kode').val(),
            }
            $.ajax({
              url:"menus/<?php echo $id_menus;?>/controller.php",
              type: "POST",
              data: value,
              success: function(data, textStatus, jqXHR)
              { 
                loadTable();
                $("#modal-tambah").modal("hide");
                $resp = JSON.parse(data);
                if($resp['status'] == true){
                  toastr.success($resp['message'], $resp['title'], {timeOut: 2000, progressBar: true});
                } else {
                  toastr.error($resp['message'], $resp['title'], {closeButton: true});
                }
              },
              error: function (request, textStatus, errorThrown) {
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: textStatus,
                  didOpen: () => {
                    Swal.hideLoading()
                  }
                });
              }
            });
          }
        });
      }
    })
  });
</script>
<?php }?>


<script>
  $('.select22').select2()
  $(function () {
    $('.table').DataTable({
      'language': {
        "emptyTable": "Data tidak ditemukan.",
        "info": "Menampilkan _START_ - _END_ dari _TOTAL_",
        "infoEmpty": "Menampilkan 0 - 0 dari 0",
        "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
        "lengthMenu": "Tampilkan _MENU_ baris",
        "search": "Cari:",
        "zeroRecords": "Tidak ditemukan data yang sesuai.",
        "thousands": "'",
        "paginate": {
          "first": "<<",
          "last": ">>",
          "next": ">",
          "previous": "<"
        }
      },  
      'destroy'     : true,
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : true
    })
  })
</script>
<?php 
}}} else {
  header("HTTP/1.1 401 Unauthorized");
  exit;
} ?>