<?php
system("clear");
date_default_timezone_set('Asia/Ho_Chi_Minh');
$re="\033[1;31m";
$gr="\033[1;32m";
$y="\033[1;33m";
$bl="\033[1;34m";
$res="\033[1;35m";
$nau="\033[1;36m";
$trang="\033[1;37m";
error_reporting(0);
function ip(){$url="https://checkip.com.vn/";$mr=curl_init();curl_setopt_array($mr, array(CURLOPT_PORT => "443",CURLOPT_URL => "$url",CURLOPT_RETURNTRANSFER => true,CURLOPT_SSL_VERIFYPEER => false,CURLOPT_TIMEOUT => 30,CURLOPT_CUSTOMREQUEST => "GET",CURLOPT_HTTPHEADER => array("Host:checkip.com.vn","upgrade-insecure-requests:1","user-agent:Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36","accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng;q=0.8,application/signed-exchange;v=b3;q=0.7")));$mr2=curl_exec($mr); curl_close($mr);$json=json_decode($mr2,true);$Speed=explode('span class="text-c00 font-bold">', $mr2)[1];$Speed=explode('</', $Speed)[0];echo"\n IP của bạn là:$gr $Speed \n";}
$mlen="999999999999999";
$a3="content-type:application/json;charset=utf-8";
$a4="t:VFZSamVFMXFXVFJOVkZVMFRsRTlQUT09";
$u1="https://gateway.golike.net/api/users/me";
$u2="https://gateway.golike.net/api/tiktok-account";
echo"$res>>>>> Loading......\r";
function mothai(){echo"                                                            \r";}
function termux(){global $link;
@system('termux-open '.$link);}
function clear(){system("clear");}
function autho(){global $autho,$user;
file_put_contents('autho.txt',"$autho|$t", FILE_APPEND);}
function tkk(){global $tt;
file_put_contents('tk.txt',"$tt", FILE_APPEND);}
function nhapautho(){
	global $autho,$t;
	echo"NHAP t: >> ";
	$t=trim(fgets(STDIN));clear();
	while(true){
	echo"NHAP AUTHOZATION >> ";
	$autho=trim(fgets(STDIN));clear();
	if($autho==false){continue;}
	autho();break;
}}
function delay(){
	global $delay;
	echo"NHAP thời gian delay nhiệm vụ: >> ";
	$delay=trim(fgets(STDIN));clear();}
$tho2=explode("\n",file_get_contents('autho.txt'));
$tho1=$tho2[0];
$autho=explode('|',$tho1)[0];
$t=explode('|',$tho1)[1];
if($autho==false){clear();nhapautho();}else{}
$a5="T:$t";
$a1="authorization:$autho";
$a2="user-agent:Mozilla/5.0 (Linux; Android 9; SM-G977N Build/PQ3B.190801.04011457) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36";
$tsm=array($a1,$a2,$a3,$a4,$a5);
$tkok=json_decode(glike($u2,$tsm),true);clear();
sleep(1);
while(true){
for($i=0;$i<$mlen;$i++){
$id=$tkok['data'][$i]['id'];
if($id==false){
echo"======================================================\n\n";
break;}
$nana=$tkok['data'][$i]['nickname'];
$mama=$tkok['data'][$i]['unique_username'];
echo "$trang Tài khoản số $re [$i]  $nau| $nana |$y $mama\n";}
echo"$trang Mời bạn chọn tài khoản Tiktok làm nhiệm vụ : ";
$chon=trim(fgets(STDIN));clear();
$tid=$tkok['data'][$chon]['id'];
if($tid==false){
echo"Bạn chưa thêm tài khoản Tiktok số $re $chon $trang Vào nhé\n";Sleep(1);clear();continue;}
$cnana=$tkok['data'][$chon]['nickname'];
$vava=$tkok['data'][$chon]['unique_username'];
echo"Nhập thời gian delay nhiệm vụ: >> ";
	$delayy=trim(fgets(STDIN));clear();
echo"Bạn đã chọn tài khoản $cnana $trang để chạy\n";sleep(1);clear();break;}
$tk2=json_decode(glike($u1,$tsm),true);
$ten=$tk2['data']['name'];
$xuu=$tk2['data']['coin'];
ip();sleep(1);
echo" Bạn hiện có $xuu Xu\n Tài khoản chạy : >>> $cnana <<<\n";sleep(1);
echo" STT | Thời gian | Thể loại | ID Tiktok | Nhận Xu | Tổng nhận | \n";
function baoloi(){global $mid, $tsm, $uid, $tid, $type, $u, $hiloi;$daloi='{"ads_id":'.$uid.',"object_id":"'.$mid.'","account_id":'.$tid.',"type":"'.$type.'"}';$hiloi='{"description":"Tôi không muốn làm Job này","users_advertising_id":'.$uid.',"type":"ads","provider":"tiktok","fb_id":'.$tid.',"erros_type":0}';$skip1="https://$u.golike.net/api/advertising/publishers/tiktok/skip-jobs";$skip="https://$u.golike.net/api/report/send";$baoloi1=plike($skip,$tsm,$hiloi);$baoloi=plike($skip1,$tsm,$daloi);echo"Bỏ qua thành công \r";}
function baoloi1(){global $mid, $tsm, $uid, $tid, $type, $u, $hiloi;$daloi='{"ads_id":'.$uid.',"object_id":"'.$mid.'","account_id":'.$tid.',"type":"'.$type.'"}';$hiloi='{"description":"Tôi đã làm Job này rồi","users_advertising_id":'.$uid.',"type":"ads","provider":"tiktok","fb_id":'.$tid.',"erros_type":6}';$skip1="https://$u.golike.net/api/advertising/publishers/tiktok/skip-jobs";$skip="https://$u.golike.net/api/report/send";$baoloi1=plike($skip,$tsm,$hiloi);$baoloi=plike($skip1,$tsm,$daloi);echo"Bỏ qua thành công \r";}
function timjob(){global $tsm,$tid,$uid,$job,$uo,$u,$url,$array,$randomkey;
$array = array('key' => 'gateway','key2' => 'dev',);$randomkey = array_rand($array);$u=$array[$randomkey];
if($u==$uo){return false;}
$url="https://$u.golike.net/api/advertising/publishers/tiktok/jobs?account_id=$tid&data=null";
$uo=$u;$job=json_decode(glike($url,$tsm),true);$uid=$job['data']['id'];
if($uid==false){return false;}}
$st=1;
$stt=$st;
$data_file = 'data.txt';
if (!file_exists($data_file)) {
	touch($data_file);
}
$file_content = file_get_contents($data_file);
$mid_list = explode('|', $file_content);
while(true){$delay=$delayy;
echo "Đang tìm Job.......\r";timjob();$link=$job['data']['link'];$type=$job['lock']['type'];$mid=$job['lock']['object_id'];
$u4="https://$u.golike.net/api/advertising/publishers/tiktok/complete-jobs";$danhan='{"ads_id":'.$uid.',"account_id":'.$tid.',"async":true,"data":null}';
if($type=="comment"){baoloi();continue;}

if(in_array($mid, $mid_list)){
	echo "Đã làm Job này rồi.\r";sleep(1);baoloi();continue;
}
file_put_contents($data_file, "$mid|", FILE_APPEND);
 
if ($type == "like") {
    // Kiểm tra số like hiện tại
    $likee = file_get_contents("https://dkcuti09.x10.mx/tiktok_api/check_tiktok.php?gt=$vava&type=user&");
    $clike = explode(',', explode('solike":', $likee)[1])[0];

    // Nếu không có like, bỏ qua công việc này và tiếp tục vòng lặp
    if ($clike == 0) {
        echo "Không có like, bỏ qua công việc này...\n";
        continue;  // Tiếp tục vòng lặp chính để tìm công việc mới
    }

    echo "Mở đường dẫn...\r";
    termux();  // Mở link theo yêu cầu

    // Tiến hành delay công việc
    for ($delay; $delay < $mlen; $delay--) {
        if ($delay == 0) {
            break;
        }
        echo "Vui lòng thực hiện nhiệm vụ $type $delay giây \r";
        sleep(1);
    }

    // Kiểm tra lại số like sau khi thực hiện nhiệm vụ
    $likee1 = file_get_contents("https://dkcuti09.x10.mx/tiktok_api/check_tiktok.php?gt=$vava&type=user&");
    $clike1 = explode(',', explode('solike":', $likee1)[1])[0];

    // Nếu số like đã thay đổi, nhận tiền
    if ($clike1 > $clike) {
        echo "Đang nhận tiền... \r";
        
        // Gửi yêu cầu nhận tiền
        $nhantien = json_decode(plike($u4, $tsm, $danhan), true);
        $ketqua = $nhantien['status'];

        // Kiểm tra kết quả trả về từ API
        if ($ketqua == 200) {
            // API trả về thành công
            $poi = $nhantien['data']['prices'];
            $poii = $poi + $poiii;  // Cập nhật tổng số tiền đã nhận
            $tg = date("G:i:s", time());
            mothai();
            echo "$re $stt | $tg |$y $type |  $vava  |$gr $poi |$poii \n";
            $sp = $stt + 1;
            $stt = $sp;
        } else {
            // Nếu API trả về không thành công
            echo "Lỗi khi nhận tiền, mã lỗi: " . $ketqua . "\n";
            baoloi();  // Gọi hàm baoloi để bỏ qua công việc
            continue;  // Tiếp tục vòng lặp
        }
    } else {
        // Nếu số like không thay đổi (không thực hiện nhiệm vụ thành công)
        echo "Số like không thay đổi, bỏ qua công việc này.\n";
        baoloi();  // Gọi hàm baoloi để bỏ qua công việc
        continue;  // Tiếp tục vòng lặp
    }
}

if($type=="follow"){
	$flo=file_get_contents("https://dkcuti09.x10.mx/tiktok_api/check_tiktok.php?gt=$vava&type=user&");
	$cflo=explode(',',explode('following":',$flo)[1])[0];
echo"Mở đường dẫn...\r";
termux();
for($delay;$delay<$mlen;$delay--){
if($delay==0){break;}
echo"Vui lòng thực hiện nhiệm vụ $type $delay giây \r";sleep(1);}
mothai();
echo"Đang nhận tiền... \r";
$floo=file_get_contents("https://dkcuti09.x10.mx/tiktok_api/check_tiktok.php?gt=$vava&type=user&");
$cfloo=explode(',',explode('following":',$floo)[1])[0];
if($cfloo==$cflo){baoloi();continue;}
$ll=0;
while($ll<$mlen){$ll++;
	if($ll>2){baoloi();break;}
	$nhantien=json_decode(plike($u4,$tsm,$danhan),true);$ketqua=$nhantien['status'];
	if($ketqua==200){$poi=$nhantien['data']['prices'];$poii=$poi+$poiii;$tg=date("G:i:s", time());mothai();
	echo"$re $stt | $tg |$y $type | $vava |$gr $poi | $poii \n";$sp=$stt+1;$stt=$sp;
	continue;}}
}
	    for($k = 3;$k>0;$k--){
		echo "delay tìm Job $k \r";sleep(1);}
$poiii=$poii;

}
function glike($host,$tsm){
	$mr = curl_init();
	curl_setopt_array($mr, array(
	CURLOPT_PORT =>"443",
	CURLOPT_URL => "$host",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => $tsm));
	$mr2 = curl_exec($mr); curl_close($mr);
	return $mr2;}
function plike($host,$tsm,$data){
	$mr = curl_init();
	curl_setopt_array($mr, array(
	CURLOPT_PORT =>"443",
	CURLOPT_URL => "$host",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => $data,
	CURLOPT_HTTPHEADER => $tsm));
	$mr2 = curl_exec($mr); curl_close($mr);
	return $mr2;}


















