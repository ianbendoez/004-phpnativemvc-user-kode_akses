<?php 
class db extends dbconn {

    public function __construct()
    {
        $this->initDBO();
    }
    
    // -- START -- SELECT
    public function cekMenusUser($username,$id_menus)
    {
        $db = $this->dblocal;
        try
        {   
            $query = "SELECT
                      tbl_users_menus.*,
                      tbl_menus.nama_menus,
                      tbl_menus.keterangan,
                      tbl_menus.status 
                    FROM
                      tbl_users_menus
                      INNER JOIN tbl_menus ON tbl_users_menus.id_menus = tbl_menus.id_menus
                    WHERE
                      tbl_users_menus.username = :username AND tbl_users_menus.id_menus = :id_menus AND tbl_menus.status = 'a' 
                    ";
            $stmt = $db->prepare($query);
            $stmt->bindParam("username",$username);
            $stmt->bindParam("id_menus",$id_menus);
            $stmt->execute();
            $stat[0] = true;
            $stat[1] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stat[2] = $stmt->rowCount();
            return $stat;
        }
        catch(PDOException $ex)
        {
            $stat[0] = false;
            $stat[1] = $ex->getMessage();
            $stat[2] = [];
            return $stat;
        }
    }

    public function getReferensi($kode,$item)
    {
        $db = $this->dblocal;
        try
        {   
            $query = "SELECT * FROM tbl_referensi WHERE kode = :kode AND item = :item";
            $stmt = $db->prepare($query);
            $stmt->bindParam("kode",$kode);
            $stmt->bindParam("item",$item);
            $stmt->execute();
            $stat[0] = true;
            $stat[1] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stat[2] = $stmt->rowCount();
            return $stat;
        }
        catch(PDOException $ex)
        {
            $stat[0] = false;
            $stat[1] = $ex->getMessage();
            $stat[2] = [];
            return $stat;
        }
    }

    public function getReferensiByKode($kode)
    {
        $db = $this->dblocal;
        try
        {   
            $query = "SELECT * FROM tbl_referensi WHERE kode = :kode AND status = 'a'";
            $stmt = $db->prepare($query);
            $stmt->bindParam("kode",$kode);
            $stmt->execute();
            $stat[0] = true;
            $stat[1] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stat[2] = $stmt->rowCount();
            return $stat;
        }
        catch(PDOException $ex)
        {
            $stat[0] = false;
            $stat[1] = $ex->getMessage();
            $stat[2] = [];
            return $stat;
        }
    }

    public function getTable($kode_akses)
    {
        $db = $this->dblocal;
        try
        {   
            $query = "SELECT
                        tbl_kode_akses.*,
                        tbl_kode.nama,
                        tbl_referensi.keterangan 
                    FROM
                        tbl_kode_akses
                        INNER JOIN tbl_kode ON tbl_kode_akses.kode = tbl_kode.id
                        INNER JOIN tbl_referensi ON tbl_kode_akses.kode_akses = tbl_referensi.item 
                    WHERE
                        tbl_referensi.kode = 'kode_akses' 
                        AND tbl_kode_akses.kode_akses = :kode_akses 
                    ORDER BY
                        tbl_kode.nama ASC";
            $stmt = $db->prepare($query);
            $stmt->bindParam("kode_akses",$kode_akses);
            $stmt->execute();
            $stat[0] = true;
            $stat[1] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stat[2] = $stmt->rowCount();
            return $stat;
        }
        catch(PDOException $ex)
        {
            $stat[0] = false;
            $stat[1] = $ex->getMessage();
            $stat[2] = [];
            return $stat;
        }
    }

    public function getKodeAkses($kriteria)
    {
        $db = $this->dblocal;
        try
        {
            $query = "SELECT * FROM tbl_referensi WHERE kode = 'kode_akses' AND keterangan LIKE '%$kriteria%' AND status = 'a'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $stat[0] = true;
            $stat[1] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $stat;
        }
        catch(PDOException $ex)
        {
            $stat[0] = false;
            $stat[1] = $ex->getMessage();
            return $stat;
        }
    }

    public function getKode($kode_akses)
    {
        $db = $this->dblocal;
        try
        {   
            $query = " SELECT * FROM tbl_kode WHERE id NOT IN (SELECT kode FROM tbl_kode_akses WHERE kode_akses = :kode_akses )";
            $stmt = $db->prepare($query);
            $stmt->bindParam("kode_akses",$kode_akses);
            $stmt->execute();
            $stat[0] = true;
            $stat[1] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stat[2] = $stmt->rowCount();
            return $stat;
        }
        catch(PDOException $ex)
        {
            $stat[0] = false;
            $stat[1] = $ex->getMessage();
            $stat[2] = [];
            return $stat;
        }
    }
    // -- END -- SELECT

    // -- START -- DELETE
    public function delete($id)
    {
        $db = $this->dblocal;
        try
        {   
            $query = "DELETE FROM tbl_kode_akses WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam("id",$id);
            $stmt->execute();
            $stat[0] = true;
            $stat[1] = "HAPUS!";
            $stat[2] = "Kode Akses berhasil dihapus.";
            return $stat;
        }
        catch(PDOException $ex)
        {
            $stat[0] = false;
            $stat[1] = "HAPUS!";
            $stat[2] = $ex->getMessage();
            return $stat;
        }
    }
    // -- END -- DELETE

    // -- START -- CREATE
    public function create($id, $kode_akses, $kode)
    {
        $db = $this->dblocal;
        try
        {   
            $query = "INSERT INTO tbl_kode_akses (id, kode_akses, kode) VALUES (:id, :kode_akses, :kode)";
            $stmt = $db->prepare($query);
            $stmt->bindParam("id",$id);
            $stmt->bindParam("kode_akses",$kode_akses);
            $stmt->bindParam("kode",$kode);
            $stmt->execute();
            $stat[0] = true;
            $stat[1] = "TAMBAH!";
            $stat[2] = "Kode Akses berhasil ditambahkan.";
            return $stat;
        }
        catch(PDOException $ex)
        {
            $stat[0] = false;
            $stat[1] = "TAMBAH!";
            $stat[2] = $ex->getMessage();
            return $stat;
        }
    }
    // -- END -- CREATE

    // -- START -- UPDATE

    // -- END -- UPDATE

}