
<?php
session_start();
include "koneksi.php";
$username = $_POST['username'];
$password = $_POST['password'];
$shift	= $_POST['shift'];


if (empty ($username) or empty ($password) or empty($shift))
{
?>
	<script language="javascript">
		alert("Data Belum Lengkap !!");
		document.location="login.php";
	</script>
<?php
}else{
	$result=mysqli_query($con,"SELECT * FROM pegawai WHERE username = '$username' and aktif='1' ");
	$pengguna = mysqli_fetch_array($result);
	mysqli_free_result($result);
	if ($password == $pengguna['password']) {
		$jeneng=$pengguna['nama'];
		$level=$pengguna['level'];
		$idpegawai=$pengguna['idpegawai'];

		$_SESSION['jenengataz'] = $jeneng;
		$_SESSION['levelataz'] = $level;
		$_SESSION['shift'] = $shift;
		$_SESSION['idpegawai'] = $idpegawai;
		header( 'Location: index.php' ) ;

	}else{?>
		<script language="javascript">
			alert("Username dan Password tidak Cucok!!");
			document.location="login.php";
		</script>
	<?php
	}

}
?>