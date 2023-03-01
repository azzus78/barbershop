<?php
include 'koneksi.php';
include 'cek.php';
//$proses=$_POST['proses'];

if (!empty($_POST["proses"])) {
    $proses=$_POST['proses']; 
    switch($proses)
    {
        case 'budget':
            if (empty($_POST['keterangan'])) {
                $jenis             =$_POST['jenis'];
                $idbiaya           =$_POST['idbiaya'];
                $total             =$_POST['total'];
                $idbudgettemp =$_POST['idbudgettemp'];
                $pegawai           =$_POST['pegawai'];
                $jenisgaji         =$_POST['detailgaji'];
                $detailgaji        =$pegawai." ".$jenisgaji;
                $private           =$_POST['private'];
                /*echo $jenis."<br>";
                echo $idbiaya."<br>";
                echo $total."<br>";
                echo $idpengeluarantemp."<br>";
                echo $pegawai."<br>";
                echo $detailgaji."<br>";
                echo $private."<br>";*/
               if ($total<=0) {
                    ?>
                        <script language="javascript">
                            alert("Jumlah Salah!!");
                            document.location="index.php?cp=budget&skac=<?php echo $idpengelbudgettemp ?>";
                        </script>
                    <?php
                }else{
                    $result=mysqli_query($con,"SELECT iddetail_budget_temp as ada FROM detail_budget_temp where biaya_idbiaya='$idbiaya' and budget_temp_idbudget_temp='$idbudgettemp'");
                    $trxbiaya_temp = mysqli_fetch_array($result);
                    mysqli_free_result($result);
                    $ada=$trxbiaya_temp['ada'];
                    if (!empty($ada)) {
                        ?>
                            <script language="javascript">
                                alert("Biaya Sudah Dipilih!!");
                                document.location="index.php?cp=budget&skac=<?php echo $idbudgettemp ?>";
                            </script>
                        <?php
                    }else{
                        if (empty($private)) {
                            if (empty($jenisgaji)) {
                            $result=mysqli_query($con,"INSERT INTO detail_budget_temp(keterangan,total,jenis,biaya_idbiaya,budget_temp_idbudget_temp) 
                            VALUES ('$pegawai','$total','$jenis','$idbiaya','$idbudgettemp')");
                            header("Location: index.php?cp=budget&skac=$idbudgettemp");
                            }else{
                                echo "iki gaji";
                            }
                        }else{
                        $result=mysqli_query($con,"INSERT INTO detail_budget_temp(keterangan,total,jenis,biaya_idbiaya,budget_temp_idbudget_temp) 
                            VALUES ('$private','$total','$jenis','$idbiaya','$idbudgettemp')");
                            header("Location: index.php?cp=budget&skac=$idbudgettemp");            
                        }

                    }
                }
            }else{
                $idbiaya    ='16';
                $keterangan =$_POST['keterangan'];
                $total      =$_POST['total'];
                $jenis      =$_POST['jenis'];
                $idbudgettemp =$_POST['idbudgettemp'];

                $result=mysqli_query($con,"INSERT INTO detail_budget_temp(keterangan,total,jenis,biaya_idbiaya,budget_temp_idbudget_temp) 
                        VALUES ('$keterangan','$total','$jenis','$idbiaya','$idbudgettemp')");
                header("Location: index.php?cp=budget&skac=$idbudgettemp");
            }
            break;
        case 'simpanbudget':
            $idbudgettemp =$_POST['idbudgettemp'];
            $date             =$_POST['tgl'];
            $bulan =date('m', strtotime(str_replace(' ','-', $date)));
            $tahun =date('Y', strtotime(str_replace(' ','-', $date)));
            $tanggal = date('Y-m-d',strtotime($tahun.'-'.$bulan.'-01'));
            $shift =$_POST['shift'];
            $user =$_POST['user'];
            //$tanggal         =date('Y-m-d', strtotime(str_replace('/','-', $tgl)));
            $subtotal     =$_POST['total'];
            $total = preg_replace('/[^0-9]/', '', $subtotal);

            $result=mysqli_query($con,"INSERT INTO budget(tgl,total) 
                    VALUES ('$tanggal','$total')");

            $result=mysqli_query($con,"SELECT max(idbudget) as idbudget from budget");
            $budget = mysqli_fetch_array($result);
            mysqli_free_result($result);
            $idbudget=$budget['idbudget'];

            $result=mysqli_query($con,"SELECT keterangan,total,jenis,biaya_idbiaya FROM detail_budget_temp where budget_temp_idbudget_temp='$idbudgettemp'");
            while($detailbudget_temp=mysqli_fetch_array($result))
            {
            $ket     =$detailbudget_temp['keterangan'];
            $total   =$detailbudget_temp['total'];
            $jenis   =$detailbudget_temp['jenis'];
            $idbiaya =$detailbudget_temp['biaya_idbiaya'];
            $detailbudget=mysqli_query($con,"INSERT INTO detail_budget(keterangan,total,jenis,biaya_idbiaya,budget_idbudget)
                                    VALUES ('$ket','$total','$jenis','$idbiaya','$idbudget')");
            }
            mysqli_free_result($result);

            /* Multiuser Belum ketemu logicnya 
            $delete1=mysqli_query($con,"DELETE FROM detail_budget_temp where budget_temp_idbudget_temp='$idbudgettemp'");
            $delete2=mysqli_query($con,"DELETE FROM budget_temp where idbudget_temp='$idbudgettemp'");*/

            $delete1=mysqli_query($con,"DELETE FROM detail_budget_temp ");
            $delete2=mysqli_query($con,"DELETE FROM budget_temp ");

            header("Location: index.php?cp=budget");
            break;
        default:
        echo "ra ketemu";
            # code...
            break;
    }
}else{  
    $proses=$_GET['proses'];
    switch ($proses) {
        case 'delbudget':
            $id=$_GET['id'];
            $idbudgettemp=$_GET['skac'];

            $delete="Delete from detail_budget_temp Where iddetail_budget_temp='$id'"; 
            mysqli_query($con,$delete) or die ("Error tu"); 
            header("Location: index.php?cp=budget&skac=$idbudgettemp");
            # code...
            break;
        case 'delbudgetsimpan':
            $iddetail_budget=$_GET['id'];
            $result=mysqli_query($con,"SELECT total,budget_idbudget FROM detail_budget where iddetail_budget='$iddetail_budget'");
            $detailbudget = mysqli_fetch_array($result);
            mysqli_free_result($result);
            $idbudget=$detailbudget['budget_idbudget'];
            $totaldetail=$detailbudget['total'];

            $result=mysqli_query($con,"SELECT * FROM budget where idbudget='$idbudget'");
            $budget = mysqli_fetch_array($result);
            mysqli_free_result($result);
            $totalbudget=$budget['total'];

            $newtotal=$totalbudget-$totaldetail;
            $update=mysqli_query($con,"UPDATE budget set total='$newtotal' where idbudget='$idbudget'");
            $delete = mysqli_query($con,"DELETE FROM detail_budget Where iddetail_budget='$iddetail_budget'") or die(mysqli_error());
            header("Location: index.php?cp=showbudget");

            # code...
            break;
        case 'delpengeluaran':
            $iddetail_pengeluaran=$_GET['iddetail'];
            $result=mysqli_query($con,"SELECT total,pengeluaran_idpengeluaran FROM detail_pengeluaran where iddetail_pengeluaran='$iddetail_pengeluaran'");
            $detailpengeluaran = mysqli_fetch_array($result);
            mysqli_free_result($result);
            $idpengeluaran=$detailpengeluaran['pengeluaran_idpengeluaran'];
            $totaldetail=$detailpengeluaran['total'];

            $result=mysqli_query($con,"SELECT * FROM pengeluaran where idpengeluaran='$idpengeluaran'");
            $pengeluaran = mysqli_fetch_array($result);
            mysqli_free_result($result);
            $totalpengeluaran=$pengeluaran['total'];

            $newtotal=$totalpengeluaran-$totaldetail;
            $update=mysqli_query($con,"UPDATE pengeluaran set total='$newtotal' where idpengeluaran='$idpengeluaran'");
            $update=mysqli_query($con,"UPDATE cashflow set keluar='$newtotal' where pengeluaran_idpengeluaran='$idpengeluaran'");
            $delete1 = mysqli_query($con,"DELETE FROM detail_pengeluaran Where iddetail_pengeluaran='$iddetail_pengeluaran'") or die(mysqli_error());
            $delete2 = mysqli_query($con,"DELETE FROM gaji Where pengeluaran_idpengeluaran='$idpengeluaran'") or die(mysqli_error());
            //$delete2 = mysqli_query($con,"DELETE FROM trxjasa Where transaksi_idtransaksi='$idtransaksi'") or die(mysqli_error());
           // $delete3 = mysqli_query($con,"DELETE FROM trxbarang Where transaksi_idtransaksi='$idtransaksi'") or die(mysqli_error());
            ?>
                <script language="javascript">
                    alert("Biaya Berhasil dihapus!!");
                    document.location="index.php?cp=trxbiaya";
                </script>
            <?php
            break;
        default:
            # code...
            break;
    }
}
?>